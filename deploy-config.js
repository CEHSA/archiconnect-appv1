/**
 * SFTP Deployment Configuration
 * 
 * This file contains the configuration for the SFTP deployment script.
 * Update the values below with your SFTP server details.
 */

module.exports = {
    // SFTP Connection Settings
    connection: {
        host: 'your-sftp-server.com',
        port: 22,
        username: 'your-username',
        // You can use either password or privateKey for authentication
        // If both are provided, privateKey will be used
        password: 'your-password',
        // privateKey: require('fs').readFileSync('/path/to/private/key'),
        // passphrase: 'your-key-passphrase', // If your private key has a passphrase
        
        // Connection options
        readyTimeout: 20000, // How long (in ms) to wait for the SSH handshake
        strictHostChecking: false, // Set to true to verify host fingerprint
    },
    
    // Deployment Settings
    deployment: {
        localRoot: __dirname, // The root directory of your project
        remoteRoot: '/home/architex/public_html/login', // Remote directory where files will be uploaded
        
        // Files to include in the deployment (uses minimatch patterns)
        include: [
            'app/**',
            'bootstrap/**',
            'config/**',
            'database/**',
            'public/**',
            'public/build/**', // Explicitly include the Vite build directory
            'resources/**',
            'routes/**',
            'storage/**',
            'vendor/**',
            'artisan',
            'composer.json',
            'composer.lock',
            '.htaccess',
            '.env.production'
        ],
        
        // Files to exclude from deployment (uses minimatch patterns)
        exclude: [
            '.git/**',
            '.github/**',
            'node_modules/**',
            'tests/**',
            '.env',
            '.env.example',
            '.gitignore',
            '.gitattributes',
            'README.md',
            'deploy-config.js',
            'deploy.js',
            'deploy.sh',
            'storage/framework/cache/**/*',
            'storage/framework/sessions/**/*',
            'storage/framework/views/**/*',
            'storage/logs/**/*'
        ],
        
        // Set to true to delete all files in the remote directory before uploading
        deleteRemote: false,
        
        // Set to true to create directories that don't exist on the remote server
        createDirectories: true,
        
        // Maximum number of concurrent file transfers
        concurrency: 10
    },
    
    // Post-deployment commands to run on the server
    // These commands will be executed in the remoteRoot directory
    postDeployCommands: [
        'cp .env.production .env',
        'php artisan key:generate --force',
        'php artisan migrate --force',
        'php artisan config:cache',
        'php artisan route:cache',
        'php artisan view:cache',
        'php artisan storage:link'
    ]
};
