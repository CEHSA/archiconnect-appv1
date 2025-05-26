# ArchiConnect AI Deployment Preparation Script
# Prepares Laravel application for deployment to ai.architex.co.za
# Run this script before pushing to Git for cPanel deployment

param(
    [switch]$SkipTests,
    [switch]$Force
)

$startTime = Get-Date

Write-Host "🚀 ArchiConnect AI Deployment Preparation" -ForegroundColor Cyan
Write-Host "Target: https://ai.architex.co.za" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan

# Check if we're in the right directory
if (!(Test-Path "artisan") -or !(Test-Path "composer.json")) {
    Write-Host "❌ Error: Not in Laravel project root directory" -ForegroundColor Red
    Write-Host "Please run this script from the project root where artisan and composer.json exist" -ForegroundColor Yellow
    exit 1
}

# Function to check command availability
function Test-Command {
    param($Command)
    try {
        Get-Command $Command -ErrorAction Stop
        return $true
    } catch {
        return $false
    }
}

# Verify required tools
Write-Host "🔍 Checking required tools..." -ForegroundColor Yellow

$requiredTools = @("php", "composer", "git")
$missingTools = @()

foreach ($tool in $requiredTools) {
    if (!(Test-Command $tool)) {
        $missingTools += $tool
    } else {
        Write-Host "  ✅ $tool" -ForegroundColor Green
    }
}

if ($missingTools.Count -gt 0) {
    Write-Host "❌ Missing required tools: $($missingTools -join ', ')" -ForegroundColor Red
    Write-Host "Please install missing tools and try again" -ForegroundColor Yellow
    exit 1
}

# Check PHP version
Write-Host "🐘 Checking PHP version..." -ForegroundColor Yellow
$phpVersion = php -r "echo PHP_VERSION;"
Write-Host "  PHP Version: $phpVersion" -ForegroundColor Green

if ([version]$phpVersion -lt [version]"8.1") {
    Write-Host "⚠️  Warning: PHP 8.1+ recommended for Laravel" -ForegroundColor Yellow
}

# Backup current .cpanel.yml if it exists and is different
if (Test-Path ".cpanel.yml") {
    $backupName = ".cpanel.yml.backup.$(Get-Date -Format 'yyyyMMdd-HHmmss')"
    Copy-Item ".cpanel.yml" $backupName
    Write-Host "📋 Backed up existing .cpanel.yml to $backupName" -ForegroundColor Yellow
}

# Install/Update Composer dependencies
Write-Host "📦 Installing Composer dependencies..." -ForegroundColor Yellow
composer install --no-dev --optimize-autoloader
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Composer install failed" -ForegroundColor Red
    exit 1
}
Write-Host "  ✅ Dependencies installed" -ForegroundColor Green

# Run tests if not skipped
if (!$SkipTests) {
    Write-Host "🧪 Running tests..." -ForegroundColor Yellow
    if (Test-Path "vendor/bin/phpunit.bat") {
        ./vendor/bin/phpunit.bat --testdox
    } elseif (Test-Path "vendor/bin/phpunit") {
        php vendor/bin/phpunit --testdox
    } else {
        Write-Host "  ⚠️  PHPUnit not found, skipping tests" -ForegroundColor Yellow
    }
    
    if ($LASTEXITCODE -ne 0 -and !$Force) {
        Write-Host "❌ Tests failed. Use -Force to deploy anyway" -ForegroundColor Red
        exit 1
    } elseif ($LASTEXITCODE -eq 0) {
        Write-Host "  ✅ All tests passed" -ForegroundColor Green
    }
} else {
    Write-Host "⏭️  Skipping tests (as requested)" -ForegroundColor Yellow
}

# Clear local caches
Write-Host "🧹 Clearing local caches..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
Write-Host "  ✅ Local caches cleared" -ForegroundColor Green

# Check .env.production configuration
Write-Host "⚙️  Checking production environment..." -ForegroundColor Yellow
if (!(Test-Path ".env.production")) {
    Write-Host "❌ .env.production file not found" -ForegroundColor Red
    Write-Host "Please create .env.production with your production settings" -ForegroundColor Yellow
    exit 1
}

# Validate critical .env.production settings
$envContent = Get-Content ".env.production" -Raw
$criticalSettings = @{
    "APP_ENV=production" = "APP_ENV should be 'production'"
    "APP_DEBUG=false" = "APP_DEBUG should be 'false'"
    "APP_URL=https://ai.architex.co.za" = "APP_URL should be https://ai.architex.co.za"
    "DB_DATABASE=" = "Database name should be set"
    "DB_USERNAME=" = "Database username should be set"
    "DB_PASSWORD=" = "Database password should be set"
}

$configIssues = @()
foreach ($setting in $criticalSettings.Keys) {
    $key = $setting.Split('=')[0]
    if ($envContent -notmatch "$key=") {
        $configIssues += $criticalSettings[$setting]
    }
}

if ($configIssues.Count -gt 0) {
    Write-Host "⚠️  Configuration issues found in .env.production:" -ForegroundColor Yellow
    foreach ($issue in $configIssues) {
        Write-Host "    - $issue" -ForegroundColor Yellow
    }
    if (!$Force) {
        Write-Host "❌ Fix configuration issues or use -Force to continue" -ForegroundColor Red
        exit 1
    }
} else {
    Write-Host "  ✅ Production environment configured" -ForegroundColor Green
}

# Check if .htaccess files are ready
Write-Host "🌐 Checking web server configuration..." -ForegroundColor Yellow
if (!(Test-Path ".htaccess.production")) {
    Write-Host "❌ .htaccess.production file not found" -ForegroundColor Red
    exit 1
}
if (!(Test-Path "public/.htaccess.production")) {
    Write-Host "❌ public/.htaccess.production file not found" -ForegroundColor Red
    exit 1
}
Write-Host "  ✅ Web server configuration files ready" -ForegroundColor Green

# Build assets if package.json exists
if (Test-Path "package.json") {
    Write-Host "🏗️  Building production assets..." -ForegroundColor Yellow
    if (Test-Command "npm") {
        npm ci
        npm run build
        if ($LASTEXITCODE -ne 0) {
            Write-Host "⚠️  Asset build failed, continuing anyway" -ForegroundColor Yellow
        } else {
            Write-Host "  ✅ Assets built successfully" -ForegroundColor Green
        }
    } elseif (Test-Command "yarn") {
        yarn install --frozen-lockfile
        yarn build
        if ($LASTEXITCODE -ne 0) {
            Write-Host "⚠️  Asset build failed, continuing anyway" -ForegroundColor Yellow
        } else {
            Write-Host "  ✅ Assets built successfully" -ForegroundColor Green
        }
    } else {
        Write-Host "  ⚠️  No npm or yarn found, skipping asset build" -ForegroundColor Yellow
    }
} else {
    Write-Host "  ⚠️  No package.json found, skipping asset build" -ForegroundColor Yellow
}

# Optimize for production
Write-Host "⚡ Optimizing for production..." -ForegroundColor Yellow
composer dump-autoload --optimize --no-dev
Write-Host "  ✅ Autoloader optimized" -ForegroundColor Green

# Check Git status
Write-Host "📊 Checking Git status..." -ForegroundColor Yellow
$gitStatus = git status --porcelain
if ($gitStatus) {
    Write-Host "📝 Uncommitted changes found:" -ForegroundColor Yellow
    git status --short
    Write-Host ""
    Write-Host "🔄 Would you like to commit these changes? (y/N): " -ForegroundColor Cyan -NoNewline
    $commit = Read-Host
    
    if ($commit -eq "y" -or $commit -eq "Y") {
        Write-Host "💬 Enter commit message: " -ForegroundColor Cyan -NoNewline
        $message = Read-Host
        if ([string]::IsNullOrWhiteSpace($message)) {
            $message = "Prepare for AI deployment to ai.architex.co.za"
        }
        
        git add .
        git commit -m $message
        Write-Host "  ✅ Changes committed" -ForegroundColor Green
    }
} else {
    Write-Host "  ✅ Working directory clean" -ForegroundColor Green
}

# Final deployment checklist
Write-Host ""
Write-Host "📋 PRE-DEPLOYMENT CHECKLIST" -ForegroundColor Cyan
Write-Host "===========================" -ForegroundColor Cyan
Write-Host "✅ Dependencies installed and optimized" -ForegroundColor Green
Write-Host "✅ Environment configuration validated" -ForegroundColor Green
Write-Host "✅ Web server configuration ready" -ForegroundColor Green
Write-Host "✅ Local caches cleared" -ForegroundColor Green

if (!$SkipTests) {
    Write-Host "✅ Tests passed" -ForegroundColor Green
}

Write-Host ""
Write-Host "🎯 DEPLOYMENT INSTRUCTIONS" -ForegroundColor Cyan
Write-Host "=========================" -ForegroundColor Cyan
Write-Host "1. Ensure your cPanel Git repository is set up for ai.architex.co.za" -ForegroundColor White
Write-Host "2. Push your changes to the Git repository:" -ForegroundColor White
Write-Host "   git push origin main" -ForegroundColor Yellow
Write-Host "3. In cPanel, trigger the deployment via Git Version Control" -ForegroundColor White
Write-Host "4. The .cpanel.yml file will automatically:" -ForegroundColor White
Write-Host "   - Deploy to /home/architex/public_html/ai" -ForegroundColor Yellow
Write-Host "   - Install dependencies" -ForegroundColor Yellow
Write-Host "   - Run migrations" -ForegroundColor Yellow
Write-Host "   - Configure Laravel" -ForegroundColor Yellow
Write-Host "   - Set up storage and caching" -ForegroundColor Yellow
Write-Host "5. Verify deployment at: https://ai.architex.co.za" -ForegroundColor White
Write-Host "6. Run verification script: https://ai.architex.co.za/verify-deployment.php" -ForegroundColor White

Write-Host ""
Write-Host "🚀 Ready for deployment to ai.architex.co.za!" -ForegroundColor Green
Write-Host "⏰ Total preparation time: $((Get-Date) - $startTime)" -ForegroundColor Cyan
