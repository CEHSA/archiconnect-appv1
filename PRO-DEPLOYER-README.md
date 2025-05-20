# Pro Deployer CLI

A command-line version of the [vscode-pro-deployer](https://github.com/heminei/vscode-pro-deployer) extension. This tool allows you to deploy your files via SFTP/FTP using the same configuration format as the VS Code extension.

## Features

- Upload files to SFTP/FTP servers
- Support for multiple targets
- Concurrent file uploads for faster deployment
- Ignore patterns to exclude files from deployment
- Include patterns to specify which files to deploy
- Support for SSH keys for SFTP authentication

## Installation

1. Make sure you have Node.js installed (version 14 or higher)

2. Install the required dependencies:

```bash
npm install chalk commander ftp glob ignore inquirer minimatch ora ssh2-sftp-client
```

3. Make the script executable:

```bash
chmod +x pro-deployer.js
```

## Configuration

The script uses the same configuration format as the vscode-pro-deployer extension. You can create a configuration file in one of the following locations:

- `.vscode/pro-deployer.json` (recommended)
- `pro-deployer.json` (in the project root)

You can generate a sample configuration file by running:

```bash
node pro-deployer.js generate-config
```

### Configuration Options

```json
{
    "enableStatusBarItem": true,
    "enableQuickPick": true,
    "uploadOnSave": true,
    "autoDelete": true,
    "checkGitignore": false,
    "activeTargets": [
        "Production Server"
    ],
    "concurrency": 5,
    "ignore": [
        ".git/**/*",
        ".vscode/**/*"
    ],
    "include": [],
    "targets": [
        {
            "name": "Production Server",
            "type": "sftp",
            "host": "your-server.com",
            "port": 22,
            "user": "your-username",
            "password": "your-password",
            "dir": "/path/to/remote/directory",
            "baseDir": "/",
            "privateKey": null,
            "passphrase": null
        },
        {
            "name": "Staging Server",
            "type": "ftp",
            "host": "staging-server.com",
            "port": 21,
            "user": "staging-username",
            "password": "staging-password",
            "dir": "/path/to/staging/directory",
            "baseDir": "/",
            "transferDataType": "binary"
        }
    ]
}
```

#### Configuration Options Explained

- `enableStatusBarItem`: Enable extension status bar item (not used in CLI)
- `enableQuickPick`: Enable quick pick when upload/error occurs (not used in CLI)
- `uploadOnSave`: On file change will be uploaded to active targets (not used in CLI)
- `autoDelete`: On file delete will be deleted to active targets (not used in CLI)
- `checkGitignore`: Skip files that are ignored in .gitignore
- `activeTargets`: Array of target names that are currently active
- `concurrency`: Maximum number of concurrent file uploads
- `ignore`: Array of glob patterns for files to ignore
- `include`: Array of glob patterns for files to include (if empty, all files are included)
- `targets`: Array of target configurations

#### Target Configuration Options

- `name`: Name of the target
- `type`: Type of the target (`sftp` or `ftp`)
- `host`: Hostname or IP address of the server
- `port`: Port number (default: 22 for SFTP, 21 for FTP)
- `user`: Username for authentication
- `password`: Password for authentication
- `dir`: Remote directory where files will be uploaded
- `baseDir`: Base directory for local files (useful when you want to upload files from a subdirectory)
- `privateKey`: Path to SSH private key file (SFTP only)
- `passphrase`: Passphrase for SSH private key (SFTP only)
- `transferDataType`: Data transfer type for FTP (`binary` or `ascii`, default: `binary`)

## Usage

### Upload Files

To upload all files in the current directory (respecting the include/ignore patterns):

```bash
node pro-deployer.js upload
```

To upload a specific file or directory:

```bash
node pro-deployer.js upload path/to/file-or-directory
```

### Generate Configuration

To generate a sample configuration file:

```bash
node pro-deployer.js generate-config
```

### Help

To see all available commands:

```bash
node pro-deployer.js --help
```

## Adding to package.json

You can add the following scripts to your `package.json` file for easier usage:

```json
"scripts": {
  "deploy": "node pro-deployer.js upload",
  "generate-config": "node pro-deployer.js generate-config"
}
```

Then you can run:

```bash
npm run deploy
npm run generate-config
```

## Security Considerations

- Do not commit your configuration file to version control if it contains passwords
- Consider using SSH keys instead of passwords for SFTP authentication
- Store sensitive information in environment variables and reference them in your code

## License

MIT
