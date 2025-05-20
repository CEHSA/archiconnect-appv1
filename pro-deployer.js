#!/usr/bin/env node

/**
 * Pro Deployer CLI
 * 
 * A command-line version of the vscode-pro-deployer extension
 * https://github.com/heminei/vscode-pro-deployer
 * 
 * This script allows you to deploy files via SFTP/FTP using the same
 * configuration format as the VS Code extension.
 */

const fs = require('fs');
const path = require('path');
const { promisify } = require('util');
const glob = require('glob');
const minimatch = require('minimatch');
const SftpClient = require('ssh2-sftp-client');
const FtpClient = require('ftp');
const chalk = require('chalk');
const { program } = require('commander');
const inquirer = require('inquirer');
const ora = require('ora');
const ignore = require('ignore');

// Promisify FTP client methods
const ftpConnect = promisify((config, callback) => {
    const client = new FtpClient();
    client.on('ready', () => {
        callback(null, client);
    });
    client.on('error', (err) => {
        callback(err);
    });
    client.connect(config);
});

// Constants
const CONFIG_FILE_NAME = 'pro-deployer.json';
const CONFIG_PATHS = [
    path.join(process.cwd(), '.vscode', CONFIG_FILE_NAME),
    path.join(process.cwd(), CONFIG_FILE_NAME)
];

// Global variables
let config = null;
let gitignorePatterns = null;

/**
 * Load the configuration file
 */
function loadConfig() {
    for (const configPath of CONFIG_PATHS) {
        if (fs.existsSync(configPath)) {
            try {
                const configData = fs.readFileSync(configPath, 'utf8');
                config = JSON.parse(configData);
                console.log(chalk.green(`Configuration loaded from ${configPath}`));
                return config;
            } catch (error) {
                console.error(chalk.red(`Error loading configuration from ${configPath}: ${error.message}`));
                process.exit(1);
            }
        }
    }
    
    console.error(chalk.red(`Configuration file not found. Please create a ${CONFIG_FILE_NAME} file in your project root or .vscode directory.`));
    process.exit(1);
}

/**
 * Load .gitignore patterns if checkGitignore is enabled
 */
function loadGitignorePatterns() {
    if (!config.checkGitignore) {
        return null;
    }
    
    const gitignorePath = path.join(process.cwd(), '.gitignore');
    if (fs.existsSync(gitignorePath)) {
        try {
            const gitignoreContent = fs.readFileSync(gitignorePath, 'utf8');
            return ignore().add(gitignoreContent);
        } catch (error) {
            console.warn(chalk.yellow(`Warning: Could not load .gitignore file: ${error.message}`));
        }
    }
    
    return null;
}

/**
 * Check if a file should be ignored based on the configuration
 */
function shouldIgnoreFile(filePath) {
    const relativePath = path.relative(process.cwd(), filePath);
    
    // Check gitignore patterns
    if (gitignorePatterns && gitignorePatterns.ignores(relativePath)) {
        return true;
    }
    
    // Check ignore patterns from config
    if (config.ignore && config.ignore.length > 0) {
        for (const pattern of config.ignore) {
            if (minimatch(relativePath, pattern)) {
                return true;
            }
        }
    }
    
    // Check include patterns from config
    if (config.include && config.include.length > 0) {
        for (const pattern of config.include) {
            if (minimatch(relativePath, pattern)) {
                return false;
            }
        }
        return true; // If include patterns exist but none match, ignore the file
    }
    
    return false;
}

/**
 * Get all files to be deployed
 */
async function getFilesToDeploy(sourcePath) {
    const isDirectory = fs.lstatSync(sourcePath).isDirectory();
    
    if (!isDirectory) {
        if (shouldIgnoreFile(sourcePath)) {
            return [];
        }
        return [sourcePath];
    }
    
    return new Promise((resolve, reject) => {
        glob('**/*', { 
            cwd: sourcePath,
            dot: true,
            nodir: true,
            absolute: true
        }, (err, matches) => {
            if (err) {
                reject(err);
                return;
            }
            
            const filesToDeploy = matches.filter(file => !shouldIgnoreFile(file));
            resolve(filesToDeploy);
        });
    });
}

/**
 * Connect to an SFTP server
 */
async function connectSftp(target) {
    const sftp = new SftpClient();
    
    try {
        await sftp.connect({
            host: target.host,
            port: target.port || 22,
            username: target.user,
            password: target.password,
            privateKey: target.privateKey ? fs.readFileSync(target.privateKey) : undefined,
            passphrase: target.passphrase
        });
        
        return sftp;
    } catch (error) {
        throw new Error(`Failed to connect to SFTP server ${target.name}: ${error.message}`);
    }
}

/**
 * Connect to an FTP server
 */
async function connectFtp(target) {
    try {
        const client = await ftpConnect({
            host: target.host,
            port: target.port || 21,
            user: target.user,
            password: target.password
        });
        
        return client;
    } catch (error) {
        throw new Error(`Failed to connect to FTP server ${target.name}: ${error.message}`);
    }
}

/**
 * Upload a file to an SFTP server
 */
async function uploadFileToSftp(sftp, localPath, remotePath, target) {
    try {
        // Create remote directory if it doesn't exist
        const remoteDir = path.dirname(remotePath);
        try {
            await sftp.mkdir(remoteDir, true);
        } catch (error) {
            // Ignore directory already exists errors
        }
        
        // Upload the file
        await sftp.put(localPath, remotePath);
        return true;
    } catch (error) {
        console.error(chalk.red(`Error uploading ${localPath} to ${target.name}: ${error.message}`));
        return false;
    }
}

/**
 * Upload a file to an FTP server
 */
async function uploadFileToFtp(ftp, localPath, remotePath, target) {
    return new Promise((resolve, reject) => {
        try {
            // Create remote directory if it doesn't exist
            const remoteDir = path.dirname(remotePath);
            ftp.mkdir(remoteDir, true, (err) => {
                if (err && err.code !== 550) { // Ignore "File exists" errors
                    console.error(chalk.yellow(`Warning: Could not create directory ${remoteDir}: ${err.message}`));
                }
                
                // Upload the file
                ftp.put(fs.createReadStream(localPath), remotePath, (err) => {
                    if (err) {
                        console.error(chalk.red(`Error uploading ${localPath} to ${target.name}: ${err.message}`));
                        resolve(false);
                    } else {
                        resolve(true);
                    }
                });
            });
        } catch (error) {
            console.error(chalk.red(`Error uploading ${localPath} to ${target.name}: ${error.message}`));
            resolve(false);
        }
    });
}

/**
 * Upload files to a target
 */
async function uploadFilesToTarget(files, target) {
    const spinner = ora(`Connecting to ${target.name}...`).start();
    
    let client;
    try {
        if (target.type === 'sftp') {
            client = await connectSftp(target);
        } else if (target.type === 'ftp') {
            client = await connectFtp(target);
        } else {
            spinner.fail(`Unsupported target type: ${target.type}`);
            return;
        }
        
        spinner.succeed(`Connected to ${target.name}`);
        
        // Process files in batches to respect concurrency limit
        const concurrency = config.concurrency || 5;
        const batches = [];
        
        for (let i = 0; i < files.length; i += concurrency) {
            batches.push(files.slice(i, i + concurrency));
        }
        
        let uploadedCount = 0;
        let failedCount = 0;
        
        for (let i = 0; i < batches.length; i++) {
            const batch = batches[i];
            const batchSpinner = ora(`Uploading batch ${i + 1}/${batches.length}...`).start();
            
            const uploadPromises = batch.map(async (localPath) => {
                const relativePath = path.relative(process.cwd(), localPath);
                const baseDir = target.baseDir || '/';
                const remotePath = path.join(target.dir, path.relative(baseDir === '/' ? process.cwd() : path.join(process.cwd(), baseDir), localPath));
                
                let success;
                if (target.type === 'sftp') {
                    success = await uploadFileToSftp(client, localPath, remotePath, target);
                } else {
                    success = await uploadFileToFtp(client, localPath, remotePath, target);
                }
                
                if (success) {
                    uploadedCount++;
                } else {
                    failedCount++;
                }
                
                return { localPath, remotePath, success };
            });
            
            const results = await Promise.all(uploadPromises);
            
            batchSpinner.succeed(`Batch ${i + 1}/${batches.length} completed`);
            
            // Log results
            results.forEach(({ localPath, remotePath, success }) => {
                const relativePath = path.relative(process.cwd(), localPath);
                if (success) {
                    console.log(chalk.green(`✓ Uploaded: ${relativePath} -> ${remotePath}`));
                } else {
                    console.log(chalk.red(`✗ Failed: ${relativePath}`));
                }
            });
        }
        
        console.log(chalk.bold(`\nUpload Summary for ${target.name}:`));
        console.log(chalk.green(`✓ Successfully uploaded: ${uploadedCount} files`));
        
        if (failedCount > 0) {
            console.log(chalk.red(`✗ Failed to upload: ${failedCount} files`));
        }
    } catch (error) {
        spinner.fail(`Error: ${error.message}`);
    } finally {
        // Close the connection
        if (client) {
            if (target.type === 'sftp') {
                await client.end();
            } else {
                client.end();
            }
        }
    }
}

/**
 * Upload files to all active targets
 */
async function uploadFiles(sourcePath) {
    // Load configuration
    loadConfig();
    gitignorePatterns = loadGitignorePatterns();
    
    // Get files to deploy
    const spinner = ora('Finding files to deploy...').start();
    let files;
    
    try {
        files = await getFilesToDeploy(sourcePath);
        spinner.succeed(`Found ${files.length} files to deploy`);
    } catch (error) {
        spinner.fail(`Error finding files: ${error.message}`);
        process.exit(1);
    }
    
    if (files.length === 0) {
        console.log(chalk.yellow('No files to deploy.'));
        return;
    }
    
    // Get active targets
    const activeTargets = config.targets.filter(target => 
        config.activeTargets.includes(target.name)
    );
    
    if (activeTargets.length === 0) {
        console.error(chalk.red('No active targets found in configuration.'));
        process.exit(1);
    }
    
    // Upload files to each active target
    for (const target of activeTargets) {
        await uploadFilesToTarget(files, target);
    }
}

// Set up command-line interface
program
    .name('pro-deployer')
    .description('Command-line version of the vscode-pro-deployer extension')
    .version('1.0.0');

program
    .command('upload [path]')
    .description('Upload a file or directory to all active targets')
    .action((path) => {
        const sourcePath = path || process.cwd();
        uploadFiles(sourcePath);
    });

program
    .command('generate-config')
    .description('Generate a sample configuration file')
    .action(() => {
        const configPath = path.join(process.cwd(), '.vscode', CONFIG_FILE_NAME);
        
        // Create .vscode directory if it doesn't exist
        if (!fs.existsSync(path.dirname(configPath))) {
            fs.mkdirSync(path.dirname(configPath), { recursive: true });
        }
        
        // Check if config file already exists
        if (fs.existsSync(configPath)) {
            inquirer.prompt([
                {
                    type: 'confirm',
                    name: 'overwrite',
                    message: `Configuration file already exists at ${configPath}. Overwrite?`,
                    default: false
                }
            ]).then((answers) => {
                if (answers.overwrite) {
                    generateConfigFile(configPath);
                } else {
                    console.log(chalk.yellow('Configuration generation cancelled.'));
                }
            });
        } else {
            generateConfigFile(configPath);
        }
    });

function generateConfigFile(configPath) {
    const sampleConfig = {
        "enableStatusBarItem": true,
        "enableQuickPick": true,
        "uploadOnSave": true,
        "autoDelete": true,
        "checkGitignore": false,
        "activeTargets": [
            "My SFTP"
        ],
        "concurrency": 5,
        "ignore": [
            ".git/**/*",
            ".vscode/**/*"
        ],
        "include": [],
        "targets": [
            {
                "name": "My SFTP",
                "type": "sftp",
                "host": "localhost",
                "port": 22,
                "user": "admin",
                "password": "123456",
                "dir": "/public_html",
                "baseDir": "/",
                "privateKey": null,
                "passphrase": null
            },
            {
                "name": "My FTP",
                "type": "ftp",
                "host": "localhost",
                "port": 21,
                "user": "admin",
                "password": "123456",
                "dir": "/public_html",
                "baseDir": "/",
                "transferDataType": "binary"
            }
        ]
    };
    
    fs.writeFileSync(configPath, JSON.stringify(sampleConfig, null, 4), 'utf8');
    console.log(chalk.green(`Configuration file generated at ${configPath}`));
}

// Parse command-line arguments
program.parse(process.argv);

// If no command is provided, show help
if (!process.argv.slice(2).length) {
    program.outputHelp();
}
