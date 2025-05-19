#!/bin/bash

# Build script for ArchiConnect App deployment
# Run this script before pushing to the cPanel Git repository

echo "Building ArchiConnect App for deployment..."

# Check if npm is installed
if ! command -v npm &> /dev/null; then
    echo "npm is not installed. Please install Node.js and npm first."
    exit 1
fi

# Install npm dependencies if needed
if [ ! -d "node_modules" ]; then
    echo "Installing npm dependencies..."
    npm install
fi

# Build frontend assets for production
echo "Building frontend assets for production..."
npm run build

echo "Frontend assets built successfully!"
echo ""
echo "Next steps:"
echo "1. Commit all changes including the built assets"
echo "2. Push to your cPanel Git repository"
echo "   The .cpanel.yml file will handle the deployment automatically"
echo ""
echo "Example Git commands:"
echo "  git add ."
echo "  git commit -m \"Deployment build $(date)\""
echo "  git push cpanel main"
