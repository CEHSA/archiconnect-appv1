#!/bin/bash

# Build for production deployment script
# This script prepares and builds assets for production deployment

# Define colors for console output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}Starting production build process...${NC}"

# Check if Node.js and npm are available
if ! command -v node &> /dev/null || ! command -v npm &> /dev/null; then
    echo -e "${RED}Error: Node.js or npm is not installed.${NC}"
    exit 1
fi

# Check for package.json
if [ ! -f "package.json" ]; then
    echo -e "${RED}Error: package.json not found. Make sure you're in the project root.${NC}"
    exit 1
fi

# Clear existing builds
echo -e "${YELLOW}Clearing previous build files...${NC}"
rm -rf public/build

# Install dependencies if needed
if [ ! -d "node_modules" ] || [ "$1" == "--fresh" ]; then
    echo -e "${YELLOW}Installing npm dependencies...${NC}"
    npm ci
fi

# Build frontend assets
echo -e "${YELLOW}Building frontend assets for production...${NC}"
npm run build

# Verify build success
if [ ! -f "public/build/manifest.json" ]; then
    echo -e "${RED}Error: Build failed - manifest.json not found in public/build/.${NC}"
    echo -e "${RED}The Vite build process did not complete successfully.${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Frontend assets built successfully!${NC}"
echo -e "${BLUE}Verifying build contents:${NC}"

# List the build files to verify
ls -la public/build/

# Create .env.production if it doesn't exist
if [ ! -f ".env.production" ]; then
    echo -e "${YELLOW}Creating .env.production file...${NC}"
    cp .env.example .env.production
    echo -e "${YELLOW}Please update .env.production with your production settings.${NC}"
fi

# Ensure APP_ENV is set to production in .env.production
if [ -f ".env.production" ]; then
    if ! grep -q "APP_ENV=production" .env.production; then
        echo -e "${YELLOW}Setting APP_ENV=production in .env.production...${NC}"
        sed -i 's/APP_ENV=.*/APP_ENV=production/' .env.production
    fi
    
    # Ensure debug is turned off
    if ! grep -q "APP_DEBUG=false" .env.production; then
        echo -e "${YELLOW}Setting APP_DEBUG=false in .env.production...${NC}"
        sed -i 's/APP_DEBUG=.*/APP_DEBUG=false/' .env.production
    fi
fi

echo -e "${GREEN}✓ Production build is ready for deployment!${NC}"
echo -e "${BLUE}Run 'npm run deploy' to deploy to your production server.${NC}"

exit 0
