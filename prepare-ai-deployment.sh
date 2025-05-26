#!/bin/bash

# Comprehensive deployment preparation script for ai.architex.co.za
# This script builds assets and prepares the application for cPanel deployment

# Define colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_header() {
    echo -e "${CYAN}=== $1 ===${NC}"
}

# Start deployment preparation
print_header "AI.ARCHITEX.CO.ZA DEPLOYMENT PREPARATION"
echo -e "${CYAN}Starting deployment preparation for ai.architex.co.za at $(date)${NC}"
echo ""

# Check if we're in the correct directory
if [ ! -f "composer.json" ] || [ ! -f "package.json" ]; then
    print_error "Not in Laravel project root directory!"
    exit 1
fi

print_success "Found Laravel project files"

# ===== ENVIRONMENT SETUP =====
print_header "ENVIRONMENT SETUP"

# Check if .env.production.ai exists
if [ ! -f ".env.production.ai" ]; then
    print_error ".env.production.ai file not found!"
    print_status "Please create .env.production.ai with your production settings"
    exit 1
fi

print_success "Production environment file found"

# Create backup of current .env if it exists
if [ -f ".env" ]; then
    cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
    print_status "Backed up existing .env file"
fi

# ===== DEPENDENCY CHECK =====
print_header "DEPENDENCY VERIFICATION"

# Check Node.js and npm
if ! command -v node &> /dev/null; then
    print_error "Node.js is not installed!"
    exit 1
fi

if ! command -v npm &> /dev/null; then
    print_error "npm is not installed!"
    exit 1
fi

print_success "Node.js $(node --version) and npm $(npm --version) are available"

# Check Composer
if ! command -v composer &> /dev/null; then
    print_warning "Composer not found in PATH, will rely on cPanel's composer"
else
    print_success "Composer $(composer --version | head -n1) is available"
fi

# ===== CLEAN PREVIOUS BUILDS =====
print_header "CLEANING PREVIOUS BUILDS"

print_status "Removing previous build artifacts..."
rm -rf public/build
rm -rf public/hot
rm -f public/mix-manifest.json
rm -rf node_modules/.cache

print_success "Previous builds cleaned"

# ===== INSTALL DEPENDENCIES =====
print_header "INSTALLING DEPENDENCIES"

print_status "Installing npm dependencies..."
if npm ci --production=false; then
    print_success "npm dependencies installed successfully"
else
    print_error "Failed to install npm dependencies"
    exit 1
fi

# ===== BUILD PRODUCTION ASSETS =====
print_header "BUILDING PRODUCTION ASSETS"

print_status "Building assets for production..."
if npm run build; then
    print_success "Production assets built successfully"
else
    print_error "Failed to build production assets"
    exit 1
fi

# Verify build output
if [ -d "public/build" ] && [ "$(ls -A public/build)" ]; then
    print_success "Build directory created with assets"
    print_status "Built assets: $(ls -la public/build/ | wc -l) files"
else
    print_error "Build directory is empty or missing"
    exit 1
fi

# ===== OPTIMIZE COMPOSER AUTOLOADER =====
print_header "OPTIMIZING COMPOSER AUTOLOADER"

if command -v composer &> /dev/null; then
    print_status "Optimizing Composer autoloader for production..."
    if composer install --optimize-autoloader --no-dev; then
        print_success "Composer autoloader optimized"
    else
        print_warning "Could not optimize composer locally, will be done during deployment"
    fi
else
    print_status "Composer optimization will be performed during cPanel deployment"
fi

# ===== GENERATE DEPLOYMENT FILES =====
print_header "PREPARING DEPLOYMENT FILES"

# Copy the correct .htaccess files
print_status "Preparing .htaccess files for ai.architex.co.za..."

if [ -f ".htaccess.ai" ]; then
    cp .htaccess.ai .htaccess.deploy
    print_success "Root .htaccess prepared"
else
    print_error ".htaccess.ai file not found!"
    exit 1
fi

if [ -f "public/.htaccess.ai" ]; then
    cp public/.htaccess.ai public/.htaccess.deploy
    print_success "Public .htaccess prepared"
else
    print_error "public/.htaccess.ai file not found!"
    exit 1
fi

# ===== SECURITY CHECKS =====
print_header "SECURITY VERIFICATION"

# Check that sensitive files won't be deployed
print_status "Verifying security configurations..."

if [ -f ".env.production.ai" ]; then
    print_success "Production environment file ready"
fi

# Check for development files that should be excluded
SECURITY_ISSUES=0

if [ -f ".env.local" ]; then
    print_warning "Found .env.local file - ensure it's in .gitignore"
    SECURITY_ISSUES=$((SECURITY_ISSUES + 1))
fi

if [ -d "node_modules" ]; then
    print_status "node_modules directory will be excluded from deployment"
fi

if [ $SECURITY_ISSUES -eq 0 ]; then
    print_success "Security checks passed"
else
    print_warning "$SECURITY_ISSUES security issues found - please review"
fi

# ===== DEPLOYMENT VERIFICATION =====
print_header "DEPLOYMENT VERIFICATION"

print_status "Verifying deployment readiness..."

# Check critical files
CRITICAL_FILES=(
    "composer.json"
    "composer.lock"
    ".env.production.ai"
    "artisan"
    "public/index.php"
    ".cpanel-ai.yml"
)

for file in "${CRITICAL_FILES[@]}"; do
    if [ -f "$file" ]; then
        print_success "✓ $file"
    else
        print_error "✗ $file (missing)"
        exit 1
    fi
done

# Check critical directories
CRITICAL_DIRS=(
    "app"
    "bootstrap"
    "config"
    "database"
    "public"
    "resources"
    "routes"
    "storage"
    "vendor"
    "public/build"
)

for dir in "${CRITICAL_DIRS[@]}"; do
    if [ -d "$dir" ]; then
        print_success "✓ $dir/"
    else
        print_error "✗ $dir/ (missing)"
        exit 1
    fi
done

# ===== FINAL DEPLOYMENT INSTRUCTIONS =====
print_header "DEPLOYMENT READY"

print_success "🚀 Application is ready for deployment to ai.architex.co.za!"
echo ""
print_status "Next steps:"
echo "1. Ensure your cPanel Git repository is configured"
echo "2. Update your database credentials in cPanel (if needed)"
echo "3. Rename .cpanel-ai.yml to .cpanel.yml"
echo "4. Commit all changes to your Git repository"
echo "5. Push to your cPanel Git repository:"
echo "   git add ."
echo "   git commit -m 'Deploy to ai.architex.co.za'"
echo "   git push origin main"
echo ""
print_status "After Git deployment:"
echo "• The .cpanel.yml file will automatically run all deployment tasks"
echo "• Your application will be available at: https://ai.architex.co.za"
echo "• Check the deployment.log file on the server for detailed logs"
echo ""
print_warning "Important reminders:"
echo "• Update your database credentials in .env.production.ai before deployment"
echo "• Ensure your cPanel MySQL database 'architex_ai' exists"
echo "• Verify SSL certificate is installed for ai.architex.co.za"
echo "• Test the application after deployment"
echo ""
print_header "DEPLOYMENT PREPARATION COMPLETED SUCCESSFULLY"
echo -e "${CYAN}Timestamp: $(date)${NC}"
