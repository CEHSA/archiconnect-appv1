#!/usr/bin/env node

/**
 * SFTP Deployment Script for ArchiConnect App
 * 
 * This script deploys the application to a remote server using SFTP.
 * It uses the ssh2-sftp-client package to handle the SFTP connection.
 */

const Client = require('ssh2-sftp-client');
const path = require('path');
const fs = require('fs');
const glob = require('glob');
const minimatch = require('minimatch');
const config = require('./deploy-config');
const readline = require('readline');

// Create a readline interface for user input
const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout
});

// Colors for console output
const colors = {
    reset: '\x1b[0m',
    bright: '\x1b[1m',
    dim: '\x1b[2m',
    underscore: '\x1b[4m',
    blink: '\x1b[5m',
    reverse: '\x1b[7m',
    hidden: '\x1b[8m',
    
    fg: {
        black: '\x1b[30m',
        red: '\x1b[31m',
        green: '\x1b[32m',
        yellow: '\x1b[33m',
        blue: '\x1b[34m',
        magenta: '\x1b[35m',
        cyan: '\x1b[36m',
        white: '\x1b[37m'
    },
    
    bg: {
        black: '\x1b[40m',
        red: '\x1b[41m',
        green: '\x1b[42m',
        yellow: '\x1b[43m',
        blue: '\x1b[44m',
        magenta: '\x1b[45m',
        cyan: '\x1b[46m',
        white: '\x1b[47m'
    }
};

// Helper function to log messages with colors
function log(message, type = 'info') {
    const timestamp = new Date().toISOString().replace(/T/, ' ').replace(/\..+/, '');
    
    switch (type) {
        case 'success':
            console.log(`${colors.fg.green}[${timestamp}] ✓ ${message}${colors.reset}`);
            break;
        case 'error':
            console.error(`${colors.fg.red}[${timestamp}] ✗ ${message}${colors.reset}`);
            break;
        case 'warning':
            console.warn(`${colors.fg.yellow}[${timestamp}] ⚠ ${message}${colors.reset}`);
            break;
        case 'info':
        default:
            console.log(`${colors.fg.cyan}[${timestamp}] ℹ ${message}${colors.reset}`);
            break;
    }
}

// Function to ask for password if not provided in config
function askForPassword() {
    return new Promise((resolve) => {
        if (config.connection.privateKey || config.connection.password) {
            resolve();
            return;
        }
        
        rl.question('Enter SFTP password: ', (password) => {
            config.connection.password = password;
            resolve();
        });
    });
}

// Function to check if a file should be included in the deployment
function shouldIncludeFile(filePath) {
    const relativePath = path.relative(config.deployment.localRoot, filePath);
    
    // Check if file matches any include pattern
    const included = config.deployment.include.some(pattern => 
        minimatch(relativePath, pattern, { dot: true })
    );
    
    // Check if file matches any exclude pattern
    const excluded = config.deployment.exclude.some(pattern => 
        minimatch(relativePath, pattern, { dot: true })
    );
    
    return included && !excluded;
}

// Function to get all files to be deployed
function getFilesToDeploy() {
    return new Promise((resolve, reject) => {
        const files = [];
        
        // Use glob to find all files in the project
        glob('**/*', { 
            cwd: config.deployment.localRoot,
            dot: true,
            nodir: true,
            absolute: true
        }, (err, matches) => {
            if (err) {
                reject(err);
                return;
            }
            
            // Filter files based on include/exclude patterns
            matches.forEach(file => {
                if (shouldIncludeFile(file)) {
                    files.push(file);
                }
            });
            
            resolve(files);
        });
    });
}

// Function to create remote directories
async function createRemoteDirectories(sftp, filePaths) {
    const directories = new Set();
    
    // Extract unique directories from file paths
    filePaths.forEach(filePath => {
        const relativePath = path.relative(config.deployment.localRoot, filePath);
        const remoteFilePath = path.join(config.deployment.remoteRoot, relativePath);
        const remoteDir = path.dirname(remoteFilePath);
        directories.add(remoteDir);
    });
    
    // Sort directories by depth to ensure parent directories are created first
    const sortedDirs = Array.from(directories).sort((a, b) => 
        a.split('/').length - b.split('/').length
    );
    
    // Create directories
    for (const dir of sortedDirs) {
        try {
            const exists = await sftp.exists(dir);
            if (!exists) {
                log(`Creating directory: ${dir}`);
                await sftp.mkdir(dir, true);
            }
        } catch (err) {
            log(`Error creating directory ${dir}: ${err.message}`, 'error');
            // Continue with other directories
        }
    }
}

// Function to upload files
async function uploadFiles(sftp, filePaths) {
    const totalFiles = filePaths.length;
    let uploadedFiles = 0;
    let failedFiles = 0;
    
    log(`Starting upload of ${totalFiles} files...`);
    
    // Create a queue for concurrent uploads
    const queue = [];
    const concurrency = config.deployment.concurrency || 5;
    
    for (const localPath of filePaths) {
        const relativePath = path.relative(config.deployment.localRoot, localPath);
        const remotePath = path.join(config.deployment.remoteRoot, relativePath);
        
        // Add upload task to queue
        const uploadTask = async () => {
            try {
                log(`Uploading: ${relativePath} (${++uploadedFiles}/${totalFiles})`);
                await sftp.put(localPath, remotePath);
                return true;
            } catch (err) {
                log(`Failed to upload ${relativePath}: ${err.message}`, 'error');
                failedFiles++;
                return false;
            }
        };
        
        // Manage concurrency
        if (queue.length >= concurrency) {
            await Promise.race(queue);
            queue.splice(queue.findIndex(p => p.status !== 'pending'), 1);
        }
        
        queue.push(uploadTask());
    }
    
    // Wait for remaining uploads to complete
    await Promise.all(queue);
    
    return { uploadedFiles, failedFiles };
}

// Function to execute post-deployment commands
async function executePostDeployCommands(sftp) {
    if (!config.postDeployCommands || config.postDeployCommands.length === 0) {
        return;
    }
    
    log('Executing post-deployment commands...');
    
    // Create a temporary script file with the commands
    const scriptContent = `#!/bin/bash
cd ${config.deployment.remoteRoot}
${config.postDeployCommands.join('\n')}
`;
    
    const localScriptPath = path.join(config.deployment.localRoot, '.deploy-commands.sh');
    const remoteScriptPath = path.join(config.deployment.remoteRoot, '.deploy-commands.sh');
    
    try {
        // Write script to local file
        fs.writeFileSync(localScriptPath, scriptContent, 'utf8');
        
        // Upload script to server
        await sftp.put(localScriptPath, remoteScriptPath);
        
        // Make script executable and execute it
        log('Post-deployment commands uploaded. Please execute them manually on the server.');
        log(`Run: cd ${config.deployment.remoteRoot} && bash .deploy-commands.sh`, 'warning');
        
        // Clean up local script file
        fs.unlinkSync(localScriptPath);
    } catch (err) {
        log(`Error with post-deployment commands: ${err.message}`, 'error');
    }
}

// Main deployment function
async function deploy() {
    const sftp = new Client();
    
    try {
        // Ask for password if needed
        await askForPassword();
        
        // Connect to SFTP server
        log(`Connecting to ${config.connection.host}...`);
        await sftp.connect(config.connection);
        log(`Connected to ${config.connection.host}`, 'success');
        
        // Get files to deploy
        log('Finding files to deploy...');
        const filesToDeploy = await getFilesToDeploy();
        log(`Found ${filesToDeploy.length} files to deploy`, 'success');
        
        // Create remote directories
        if (config.deployment.createDirectories) {
            log('Creating remote directories...');
            await createRemoteDirectories(sftp, filesToDeploy);
        }
        
        // Upload files
        const { uploadedFiles, failedFiles } = await uploadFiles(sftp, filesToDeploy);
        
        // Execute post-deployment commands
        await executePostDeployCommands(sftp);
        
        // Log deployment summary
        log('Deployment completed!', 'success');
        log(`Successfully uploaded ${uploadedFiles} files.`, 'success');
        
        if (failedFiles > 0) {
            log(`Failed to upload ${failedFiles} files.`, 'warning');
        }
    } catch (err) {
        log(`Deployment failed: ${err.message}`, 'error');
    } finally {
        // Close SFTP connection
        try {
            await sftp.end();
            log('SFTP connection closed');
        } catch (err) {
            // Ignore errors when closing connection
        }
        
        // Close readline interface
        rl.close();
    }
}

// Start deployment
deploy();
