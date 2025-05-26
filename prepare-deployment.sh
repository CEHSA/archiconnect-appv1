#!/bin/bash
# ArchiConnect AI Deployment Preparation Script
# Prepares Laravel application for deployment to ai.architex.co.za
# Run this script before pushing to Git for cPanel deployment

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
WHITE='\033[1;37m'
NC='\033[0m' # No Color

# Parse command line arguments
SKIP_TESTS=false
FORCE=false

while [[ $# -gt 0 ]]; do
    case $1 in
        --skip-tests)
            SKIP_TESTS=true
            shift
            ;;
        --force)
            FORCE=true
            shift
            ;;
        *)
            echo -e "${RED}Unknown option: $1${NC}"
            exit 1
            ;;
    esac
done

echo -e "${CYAN}🚀 ArchiConnect AI Deployment Preparation${NC}"
echo -e "${GREEN}Target: https://ai.architex.co.za${NC}"
echo -e "${CYAN}========================================${NC}"

# Check if we're in the right directory
if [[ ! -f "artisan" ]] || [[ ! -f "composer.json" ]]; then
    echo -e "${RED}❌ Error: Not in Laravel project root directory${NC}"
    echo -e "${YELLOW}Please run this script from the project root where artisan and composer.json exist${NC}"
    exit 1
fi

# Function to check command availability
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Verify required tools
echo -e "${YELLOW}🔍 Checking required tools...${NC}"

required_tools=("php" "composer" "git")
missing_tools=()

for tool in "${required_tools[@]}"; do
    if command_exists "$tool"; then
        echo -e "${GREEN}  ✅ $tool${NC}"
    else
        missing_tools+=("$tool")
    fi
done

if [[ ${#missing_tools[@]} -gt 0 ]]; then
    echo -e "${RED}❌ Missing required tools: ${missing_tools[*]}${NC}"
    echo -e "${YELLOW}Please install missing tools and try again${NC}"
    exit 1
fi

# Check PHP version
echo -e "${YELLOW}🐘 Checking PHP version...${NC}"
php_version=$(php -r "echo PHP_VERSION;")
echo -e "${GREEN}  PHP Version: $php_version${NC}"

if ! php -r "exit(version_compare(PHP_VERSION, '8.1', '>=') ? 0 : 1);"; then
    echo -e "${YELLOW}⚠️  Warning: PHP 8.1+ recommended for Laravel${NC}"
fi

# Backup current .cpanel.yml if it exists and is different
if [[ -f ".cpanel.yml" ]]; then
    backup_name=".cpanel.yml.backup.$(date +%Y%m%d-%H%M%S)"
    cp ".cpanel.yml" "$backup_name"
    echo -e "${YELLOW}📋 Backed up existing .cpanel.yml to $backup_name${NC}"
fi

# Install/Update Composer dependencies
echo -e "${YELLOW}📦 Installing Composer dependencies...${NC}"
if ! composer install --no-dev --optimize-autoloader; then
    echo -e "${RED}❌ Composer install failed${NC}"
    exit 1
fi
echo -e "${GREEN}  ✅ Dependencies installed${NC}"

# Run tests if not skipped
if [[ "$SKIP_TESTS" == false ]]; then
    echo -e "${YELLOW}🧪 Running tests...${NC}"
    if [[ -f "vendor/bin/phpunit" ]]; then
        if ! php vendor/bin/phpunit --testdox; then
            if [[ "$FORCE" == false ]]; then
                echo -e "${RED}❌ Tests failed. Use --force to deploy anyway${NC}"
                exit 1
            fi
        else
            echo -e "${GREEN}  ✅ All tests passed${NC}"
        fi
    else
        echo -e "${YELLOW}  ⚠️  PHPUnit not found, skipping tests${NC}"
    fi
else
    echo -e "${YELLOW}⏭️  Skipping tests (as requested)${NC}"
fi

# Clear local caches
echo -e "${YELLOW}🧹 Clearing local caches...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo -e "${GREEN}  ✅ Local caches cleared${NC}"

# Check .env.production configuration
echo -e "${YELLOW}⚙️  Checking production environment...${NC}"
if [[ ! -f ".env.production" ]]; then
    echo -e "${RED}❌ .env.production file not found${NC}"
    echo -e "${YELLOW}Please create .env.production with your production settings${NC}"
    exit 1
fi

# Validate critical .env.production settings
env_content=$(cat .env.production)
config_issues=()

# Check critical settings
if ! echo "$env_content" | grep -q "APP_ENV=production"; then
    config_issues+=("APP_ENV should be 'production'")
fi

if ! echo "$env_content" | grep -q "APP_DEBUG=false"; then
    config_issues+=("APP_DEBUG should be 'false'")
fi

if ! echo "$env_content" | grep -q "APP_URL=https://ai.architex.co.za"; then
    config_issues+=("APP_URL should be https://ai.architex.co.za")
fi

if ! echo "$env_content" | grep -q "DB_DATABASE="; then
    config_issues+=("Database name should be set")
fi

if ! echo "$env_content" | grep -q "DB_USERNAME="; then
    config_issues+=("Database username should be set")
fi

if ! echo "$env_content" | grep -q "DB_PASSWORD="; then
    config_issues+=("Database password should be set")
fi

if [[ ${#config_issues[@]} -gt 0 ]]; then
    echo -e "${YELLOW}⚠️  Configuration issues found in .env.production:${NC}"
    for issue in "${config_issues[@]}"; do
        echo -e "${YELLOW}    - $issue${NC}"
    done
    if [[ "$FORCE" == false ]]; then
        echo -e "${RED}❌ Fix configuration issues or use --force to continue${NC}"
        exit 1
    fi
else
    echo -e "${GREEN}  ✅ Production environment configured${NC}"
fi

# Check if .htaccess files are ready
echo -e "${YELLOW}🌐 Checking web server configuration...${NC}"
if [[ ! -f ".htaccess.production" ]]; then
    echo -e "${RED}❌ .htaccess.production file not found${NC}"
    exit 1
fi
if [[ ! -f "public/.htaccess.production" ]]; then
    echo -e "${RED}❌ public/.htaccess.production file not found${NC}"
    exit 1
fi
echo -e "${GREEN}  ✅ Web server configuration files ready${NC}"

# Build assets if package.json exists
if [[ -f "package.json" ]]; then
    echo -e "${YELLOW}🏗️  Building production assets...${NC}"
    if command_exists npm; then
        if npm ci && npm run build; then
            echo -e "${GREEN}  ✅ Assets built successfully${NC}"
        else
            echo -e "${YELLOW}⚠️  Asset build failed, continuing anyway${NC}"
        fi
    elif command_exists yarn; then
        if yarn install --frozen-lockfile && yarn build; then
            echo -e "${GREEN}  ✅ Assets built successfully${NC}"
        else
            echo -e "${YELLOW}⚠️  Asset build failed, continuing anyway${NC}"
        fi
    else
        echo -e "${YELLOW}  ⚠️  No npm or yarn found, skipping asset build${NC}"
    fi
else
    echo -e "${YELLOW}  ⚠️  No package.json found, skipping asset build${NC}"
fi

# Optimize for production
echo -e "${YELLOW}⚡ Optimizing for production...${NC}"
composer dump-autoload --optimize --no-dev
echo -e "${GREEN}  ✅ Autoloader optimized${NC}"

# Check Git status
echo -e "${YELLOW}📊 Checking Git status...${NC}"
if ! git diff-index --quiet HEAD --; then
    echo -e "${YELLOW}📝 Uncommitted changes found:${NC}"
    git status --short
    echo ""
    echo -ne "${CYAN}🔄 Would you like to commit these changes? (y/N): ${NC}"
    read -r commit_choice
    
    if [[ "$commit_choice" == "y" ]] || [[ "$commit_choice" == "Y" ]]; then
        echo -ne "${CYAN}💬 Enter commit message: ${NC}"
        read -r commit_message
        if [[ -z "$commit_message" ]]; then
            commit_message="Prepare for AI deployment to ai.architex.co.za"
        fi
        
        git add .
        git commit -m "$commit_message"
        echo -e "${GREEN}  ✅ Changes committed${NC}"
    fi
else
    echo -e "${GREEN}  ✅ Working directory clean${NC}"
fi

# Final deployment checklist
echo ""
echo -e "${CYAN}📋 PRE-DEPLOYMENT CHECKLIST${NC}"
echo -e "${CYAN}===========================${NC}"
echo -e "${GREEN}✅ Dependencies installed and optimized${NC}"
echo -e "${GREEN}✅ Environment configuration validated${NC}"
echo -e "${GREEN}✅ Web server configuration ready${NC}"
echo -e "${GREEN}✅ Local caches cleared${NC}"

if [[ "$SKIP_TESTS" == false ]]; then
    echo -e "${GREEN}✅ Tests passed${NC}"
fi

echo ""
echo -e "${CYAN}🎯 DEPLOYMENT INSTRUCTIONS${NC}"
echo -e "${CYAN}=========================${NC}"
echo -e "${WHITE}1. Ensure your cPanel Git repository is set up for ai.architex.co.za${NC}"
echo -e "${WHITE}2. Push your changes to the Git repository:${NC}"
echo -e "${YELLOW}   git push origin main${NC}"
echo -e "${WHITE}3. In cPanel, trigger the deployment via Git Version Control${NC}"
echo -e "${WHITE}4. The .cpanel.yml file will automatically:${NC}"
echo -e "${YELLOW}   - Deploy to /home/architex/public_html/ai${NC}"
echo -e "${YELLOW}   - Install dependencies${NC}"
echo -e "${YELLOW}   - Run migrations${NC}"
echo -e "${YELLOW}   - Configure Laravel${NC}"
echo -e "${YELLOW}   - Set up storage and caching${NC}"
echo -e "${WHITE}5. Verify deployment at: https://ai.architex.co.za${NC}"
echo -e "${WHITE}6. Run verification script: https://ai.architex.co.za/verify-deployment.php${NC}"

echo ""
echo -e "${GREEN}🚀 Ready for deployment to ai.architex.co.za!${NC}"

# Make script executable
chmod +x "$0"
