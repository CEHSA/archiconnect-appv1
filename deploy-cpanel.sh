#!/bin/bash

# ArchiConnect App Deployment Script for cPanel (No SSH)
# This script helps prepare the Laravel application for deployment to cPanel shared hosting

echo "Starting preparation for ArchiConnect App deployment to cPanel..."

# Configuration - Update these variables
CPANEL_USERNAME="architex"
CPANEL_DOMAIN="login.architex.co.za"
CPANEL_GIT_REPO_NAME="archiconnect-app"
PRODUCTION_ENV_FILE=".env.production"
DEPLOYMENT_ZIP="archiconnect-app-deployment.zip"

# Check if the production environment file exists
if [ ! -f "$PRODUCTION_ENV_FILE" ]; then
    echo "Production environment file ($PRODUCTION_ENV_FILE) not found."
    exit 1
fi

# Build frontend assets for production
echo "Building frontend assets for production..."
npm run build

# Verify Vite build was successful
if [ ! -d "public/build" ] || [ ! -f "public/build/manifest.json" ]; then
    echo "ERROR: Vite build failed or assets are missing. Cannot continue deployment."
    echo "Please check for build errors and try again."
    exit 1
fi

# Create a deployment directory
echo "Creating deployment directory..."
mkdir -p deployment

# Copy necessary files to deployment directory
echo "Copying files to deployment directory..."
cp -R app bootstrap config database resources routes storage tests vendor composer.json composer.lock artisan deployment/

# Copy public directory but handle Vite build directory specially to ensure assets are included
echo "Copying public directory (including Vite assets)..."
mkdir -p deployment/public
cp -R public/* deployment/public/
# Make absolutely sure the Vite build directory is included
if [ -d "public/build" ]; then
    echo "Ensuring Vite assets are included..."
    mkdir -p deployment/public/build
    cp -R public/build/* deployment/public/build/
fi

cp $PRODUCTION_ENV_FILE deployment/.env
cp public/.htaccess deployment/public/
cp .htaccess deployment/
cp .cpanel.yml deployment/
cp post-deployment.php deployment/
cp phpinfo.php deployment/
cp db-check.php deployment/
cp check-vite-assets.php deployment/

# Create necessary directories
echo "Creating necessary directories..."
mkdir -p deployment/storage/framework/sessions
mkdir -p deployment/storage/framework/views
mkdir -p deployment/storage/framework/cache
mkdir -p deployment/storage/app/public

# Create a zip file for deployment
echo "Creating deployment zip file..."

# Clean up any existing deployment zip
if [ -f "$DEPLOYMENT_ZIP" ]; then
    echo "Removing existing deployment zip..."
    rm -f "$DEPLOYMENT_ZIP"
fi

# Try using 7-Zip if available
if command -v 7z &> /dev/null; then
    echo "Using 7-Zip to create archive..."
    7z a -r "$DEPLOYMENT_ZIP" "./deployment/*"
# Try using zip if available
elif command -v zip &> /dev/null; then
    echo "Using zip command to create archive..."
    cd deployment
    zip -r ../$DEPLOYMENT_ZIP .
    cd ..
# If all else fails, instruct the user to manually zip
else
    echo "Neither 7z nor zip commands are available."
    echo "Please manually compress the 'deployment' folder into a zip archive."
    echo "You can use Windows Explorer to do this:"
    echo "1. Navigate to the project folder"
    echo "2. Right-click on the 'deployment' folder"
    echo "3. Select 'Send to' > 'Compressed (zipped) folder'"
    echo "4. Rename the resulting zip file to '$DEPLOYMENT_ZIP'"
fi

# Verify the zip file was created
if [ -f "$DEPLOYMENT_ZIP" ]; then
    echo "Deployment zip file created successfully: $DEPLOYMENT_ZIP"
    echo "Size: $(du -h "$DEPLOYMENT_ZIP" | cut -f1)"
else
    echo "Warning: Deployment zip file was not created."
    echo "Please manually compress the 'deployment' folder."
fi

echo "Deployment preparation completed!"
echo ""
echo "Manual deployment steps to perform on cPanel:"
echo "1. Log in to cPanel"
echo "2. Go to File Manager and navigate to public_html/login (or your desired directory)"
echo "3. Upload the $DEPLOYMENT_ZIP file"
echo "4. Extract the zip file"
echo "5. In cPanel, go to 'Setup PHP Version' and ensure PHP 8.2+ is selected"
echo "6. In cPanel, go to 'Terminal' or 'Run PHP' and execute the following commands:"
echo "   - cd public_html/login"
echo "   - php artisan key:generate (if needed)"
echo "   - php artisan migrate --force"
echo "   - php artisan config:cache"
echo "   - php artisan route:cache"
echo "   - php artisan view:cache"
echo "   - php artisan storage:link"
echo ""
echo "If Terminal is not available, you may need to create a PHP script to run these commands."
echo "A post-deployment.php script has been created for this purpose."
echo ""
echo "Deployment preparation completed!"
