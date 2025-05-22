#!/bin/bash

# ArchiConnect App SFTP Deployment Script
# This script builds the application for production and deploys it via SFTP

# Display colorful messages
function echo_color() {
    local color=$1
    local message=$2
    
    case $color in
        "red") echo -e "\e[31m$message\e[0m" ;;
        "green") echo -e "\e[32m$message\e[0m" ;;
        "yellow") echo -e "\e[33m$message\e[0m" ;;
        "blue") echo -e "\e[34m$message\e[0m" ;;
        "magenta") echo -e "\e[35m$message\e[0m" ;;
        "cyan") echo -e "\e[36m$message\e[0m" ;;
        *) echo "$message" ;;
    esac
}

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo_color "red" "Error: Node.js is not installed. Please install Node.js to continue."
    exit 1
fi

# Check if npm is installed
if ! command -v npm &> /dev/null; then
    echo_color "red" "Error: npm is not installed. Please install npm to continue."
    exit 1
fi

# Check if required packages are installed
echo_color "blue" "Checking required npm packages..."
if ! npm list ssh2-sftp-client glob minimatch &> /dev/null; then
    echo_color "yellow" "Installing required npm packages..."
    npm install --save-dev ssh2-sftp-client glob minimatch
fi

# Build frontend assets for production
echo_color "blue" "Building frontend assets for production..."
npm run build

if [ $? -ne 0 ]; then
    echo_color "red" "Error: Failed to build frontend assets."
    exit 1
fi

# Verify the build was successful
if [ ! -f "public/build/manifest.json" ]; then
    echo_color "red" "Error: Vite manifest file not found. The build may have failed."
    exit 1
fi

echo_color "green" "Frontend assets built successfully!"

# Run the deployment script
echo_color "blue" "Starting deployment via SFTP..."
node deploy.js

if [ $? -ne 0 ]; then
    echo_color "red" "Error: Deployment failed."
    exit 1
fi

echo_color "green" "Deployment completed successfully!"
echo_color "cyan" "Note: Post-deployment commands need to be run manually on the server."
echo_color "cyan" "Check the deployment log for details."

exit 0
