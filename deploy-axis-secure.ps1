# ArchiConnect App Deployment Script for cPanel with secure upload (PowerShell version)
# This script prepares and uploads the Laravel application to axis.architex.co.za

Write-Host "Starting preparation for ArchiConnect App deployment to cPanel..." -ForegroundColor Cyan

# Configuration - Update these variables
$CPANEL_USERNAME = "architex"
$CPANEL_DOMAIN = "axis.architex.co.za"
$DEPLOYMENT_PATH = "/home/architex/public_html/axis"
$PRODUCTION_ENV_FILE = ".env.production"
$DEPLOYMENT_ZIP = "archiconnect-axis-deployment.zip"

# FTP credentials (these will be used for upload)
$FTP_HOST = "architex.co.za"
$FTP_USER = "****" # Credentials masked for security
$FTP_PASS = "****" # Credentials masked for security
$FTP_PORT = 21

# Build frontend assets for production
Write-Host "Building frontend assets for production..." -ForegroundColor Yellow
npm run build

# Remove hot file if it exists (prevents dev server connections in production)
Write-Host "Removing hot file if it exists..." -ForegroundColor Yellow
if (Test-Path "public/hot") {
    Remove-Item "public/hot" -Force
}

# Create a deployment directory
Write-Host "Creating deployment directory..." -ForegroundColor Yellow
New-Item -Path "deployment" -ItemType Directory -Force | Out-Null

# Copy necessary files to deployment directory
Write-Host "Copying files to deployment directory..." -ForegroundColor Yellow
# Copy entire directories with their structure intact
Copy-Item -Path "app", "bootstrap", "config", "database", "resources", "routes", "storage", "tests", "vendor" -Destination "deployment\" -Recurse -Force

# Ensure frontend build files are properly copied
Write-Host "Copying frontend build files..." -ForegroundColor Yellow
# Create public directory first
New-Item -Path "deployment\public" -ItemType Directory -Force | Out-Null

# Copy all public directory contents EXCEPT node_modules and source files
Get-ChildItem -Path "public" -Exclude "node_modules", "src", "hot" | ForEach-Object {
    if ($_.PSIsContainer) {
        Copy-Item -Path $_.FullName -Destination "deployment\public\$($_.Name)" -Recurse -Force
    } else {
        Copy-Item -Path $_.FullName -Destination "deployment\public\$($_.Name)" -Force
    }
}

# Ensure build directory is copied (may be in different locations depending on your build tool)
if (Test-Path "public\build") {
    Copy-Item -Path "public\build" -Destination "deployment\public\build" -Recurse -Force
}

# If using Vite, ensure the resources/js/build directory is copied if it exists
if (Test-Path "resources\js\build") {
    Copy-Item -Path "resources\js\build" -Destination "deployment\resources\js\build" -Recurse -Force
}

# Copy individual files to root of deployment directory
Copy-Item -Path "composer.json", "composer.lock", "artisan", "package.json" -Destination "deployment\" -Force
Copy-Item -Path $PRODUCTION_ENV_FILE -Destination "deployment\.env" -Force
Copy-Item -Path "public\.htaccess" -Destination "deployment\public\" -Force
Copy-Item -Path ".htaccess", ".cpanel.yml", "post-deployment.php", "phpinfo.php", "db-check.php" -Destination "deployment\" -Force

# Update .env file with production settings
Write-Host "Updating .env file with production settings..." -ForegroundColor Yellow
$envContent = Get-Content "deployment\.env" -Raw
$envContent = $envContent -replace "APP_ENV=local", "APP_ENV=production"
$envContent = $envContent -replace "APP_DEBUG=true", "APP_DEBUG=false"
$envContent = $envContent -replace "ASSET_URL=", "ASSET_URL=https://$CPANEL_DOMAIN"
Set-Content -Path "deployment\.env" -Value $envContent

# Create necessary directories - Ensure proper structure
Write-Host "Creating necessary directories..." -ForegroundColor Yellow
New-Item -Path "deployment\storage\framework\sessions", 
         "deployment\storage\framework\views", 
         "deployment\storage\framework\cache", 
         "deployment\storage\framework\cache\data", 
         "deployment\storage\app\public" -ItemType Directory -Force | Out-Null

# Set proper permissions for files and directories (will be applied on server)
Write-Host "Creating permission script for server execution..." -ForegroundColor Yellow
$permissionScript = @"
#!/bin/bash
# This script sets proper permissions for Laravel application
# Execute this script after extracting the deployment package

# Set directory permissions
find . -type d -exec chmod 755 {} \;

# Set file permissions
find . -type f -exec chmod 644 {} \;

# Set executable permissions for specific files
chmod 755 artisan
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Ensure storage and bootstrap/cache are writable
chown -R \$USER:www-data storage
chown -R \$USER:www-data bootstrap/cache

echo "Permissions have been set correctly."
"@
Set-Content -Path "deployment\set-permissions.sh" -Value $permissionScript

# Create a zip file for deployment - Preserve directory structure
Write-Host "Creating deployment zip file..." -ForegroundColor Yellow
Compress-Archive -Path "deployment\*" -DestinationPath $DEPLOYMENT_ZIP -Force

# Upload via FTP (requires PowerShell 7+ or WinSCP module)
$uploadChoice = Read-Host "Do you want to upload the deployment package via FTP? (y/n)"
if ($uploadChoice -eq "y") {
    try {
        Write-Host "Attempting to upload via FTP..." -ForegroundColor Yellow
        
        # Check if WinSCP module is installed
        if (-not (Get-Module -ListAvailable -Name WinSCP)) {
            Write-Host "WinSCP PowerShell module not found. Installing..." -ForegroundColor Yellow
            Install-Module -Name WinSCP -Scope CurrentUser -Force
        }
        
        # Import WinSCP module
        Import-Module WinSCP
        
        # Setup session options
        $sessionOptions = New-Object WinSCP.SessionOptions -Property @{
            Protocol = [WinSCP.Protocol]::Ftp
            HostName = $FTP_HOST
            UserName = $FTP_USER
            Password = $FTP_PASS
            PortNumber = $FTP_PORT
            FtpSecure = [WinSCP.FtpSecure]::Explicit
        }
        
        $session = New-Object WinSCP.Session
        
        try {
            # Connect
            $session.Open($sessionOptions)
            
            # Upload file
            $transferOptions = New-Object WinSCP.TransferOptions
            $transferOptions.TransferMode = [WinSCP.TransferMode]::Binary
            
            $transferResult = $session.PutFiles($DEPLOYMENT_ZIP, "$DEPLOYMENT_PATH/$DEPLOYMENT_ZIP", $False, $transferOptions)
            
            # Check for errors
            if ($transferResult.IsSuccess) {
                Write-Host "Upload successful!" -ForegroundColor Green
                
                # Execute unzip command via FTP if possible
                Write-Host "Note: You'll need to manually extract the zip file in cPanel File Manager" -ForegroundColor Yellow
                Write-Host "      and run the set-permissions.sh script after extraction." -ForegroundColor Yellow
            } else {
                Write-Host "Upload failed!" -ForegroundColor Red
                foreach ($transfer in $transferResult.Transfers) {
                    if ($transfer.Error) {
                        Write-Host "Error: $($transfer.Error.Message)" -ForegroundColor Red
                    }
                }
            }
        } finally {
            # Disconnect, clean up
            $session.Dispose()
        }
    } catch {
        Write-Host "FTP upload failed: $_" -ForegroundColor Red
        Write-Host "Please upload the $DEPLOYMENT_ZIP file manually via cPanel File Manager" -ForegroundColor Yellow
    }
}

# Clean up deployment directory after creating zip
Write-Host "Cleaning up deployment directory..." -ForegroundColor Yellow
Remove-Item -Path "deployment" -Recurse -Force

Write-Host "Deployment preparation completed!" -ForegroundColor Green
Write-Host ""
Write-Host "Manual deployment steps to perform on cPanel:" -ForegroundColor Cyan
Write-Host "1. Log in to cPanel at architex.co.za:2083"
Write-Host "2. Go to File Manager and navigate to $DEPLOYMENT_PATH"
Write-Host "3. If not uploaded automatically, upload the $DEPLOYMENT_ZIP file"
Write-Host "4. Extract the zip file"
Write-Host "5. Run the permission script: bash set-permissions.sh"
Write-Host "6. In cPanel, go to 'Setup PHP Version' and ensure PHP 8.2+ is selected"
Write-Host "7. In cPanel, go to 'Terminal' or 'Run PHP' and execute the following commands:"
Write-Host "   - cd $DEPLOYMENT_PATH"
Write-Host "   - php artisan key:generate (if needed)"
Write-Host "   - php artisan migrate --force"
Write-Host "   - php artisan config:cache"
Write-Host "   - php artisan route:cache"
Write-Host "   - php artisan view:cache"
Write-Host "   - php artisan storage:link"