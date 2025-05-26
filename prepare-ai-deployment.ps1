# PowerShell deployment preparation script for ai.architex.co.za
# This script builds assets and prepares the application for cPanel deployment

param(
    [switch]$SkipBuild = $false,
    [switch]$Verbose = $false
)

# Function to write colored output
function Write-ColoredOutput {
    param(
        [string]$Message,
        [string]$Color = "White"
    )
    Write-Host $Message -ForegroundColor $Color
}

function Write-Header {
    param([string]$Title)
    Write-Host ""
    Write-ColoredOutput "=== $Title ===" "Cyan"
    Write-Host ""
}

function Write-Success {
    param([string]$Message)
    Write-ColoredOutput "[SUCCESS] $Message" "Green"
}

function Write-Info {
    param([string]$Message)
    Write-ColoredOutput "[INFO] $Message" "Blue"
}

function Write-Warning {
    param([string]$Message)
    Write-ColoredOutput "[WARNING] $Message" "Yellow"
}

function Write-Error {
    param([string]$Message)
    Write-ColoredOutput "[ERROR] $Message" "Red"
}

# Start deployment preparation
Clear-Host
Write-Header "AI.ARCHITEX.CO.ZA DEPLOYMENT PREPARATION"
Write-ColoredOutput "Starting deployment preparation for ai.architex.co.za at $(Get-Date)" "Cyan"

# Check if we're in the correct directory
if (-not (Test-Path "composer.json") -or -not (Test-Path "package.json")) {
    Write-Error "Not in Laravel project root directory!"
    Write-Info "Please navigate to your Laravel project root directory."
    exit 1
}

Write-Success "Found Laravel project files"

# ===== ENVIRONMENT SETUP =====
Write-Header "ENVIRONMENT SETUP"

# Check if .env.production.ai exists
if (-not (Test-Path ".env.production.ai")) {
    Write-Error ".env.production.ai file not found!"
    Write-Info "Please create .env.production.ai with your production settings"
    exit 1
}

Write-Success "Production environment file found"

# Create backup of current .env if it exists
if (Test-Path ".env") {
    $backupName = ".env.backup." + (Get-Date -Format "yyyyMMdd_HHmmss")
    Copy-Item ".env" $backupName
    Write-Info "Backed up existing .env file to $backupName"
}

# ===== DEPENDENCY CHECK =====
Write-Header "DEPENDENCY VERIFICATION"

# Check Node.js and npm
try {
    $nodeVersion = node --version
    $npmVersion = npm --version
    Write-Success "Node.js $nodeVersion and npm $npmVersion are available"
} catch {
    Write-Error "Node.js or npm is not installed or not in PATH!"
    Write-Info "Please install Node.js from https://nodejs.org/"
    exit 1
}

# Check Composer
try {
    $composerVersion = composer --version 2>$null
    if ($composerVersion) {
        Write-Success "Composer is available: $($composerVersion.Split("`n")[0])"
    } else {
        throw "Composer not found"
    }
} catch {
    Write-Warning "Composer not found in PATH, will rely on cPanel's composer"
}

# ===== CLEAN PREVIOUS BUILDS =====
Write-Header "CLEANING PREVIOUS BUILDS"

Write-Info "Removing previous build artifacts..."

# Remove build directories and files
$itemsToRemove = @(
    "public\build",
    "public\hot",
    "public\mix-manifest.json",
    "node_modules\.cache"
)

foreach ($item in $itemsToRemove) {
    if (Test-Path $item) {
        Remove-Item $item -Recurse -Force
        Write-Info "Removed $item"
    }
}

Write-Success "Previous builds cleaned"

# ===== INSTALL DEPENDENCIES =====
Write-Header "INSTALLING DEPENDENCIES"

if (-not $SkipBuild) {
    Write-Info "Installing npm dependencies..."
    try {
        $npmInstall = npm ci --production=false
        if ($LASTEXITCODE -eq 0) {
            Write-Success "npm dependencies installed successfully"
        } else {
            throw "npm install failed"
        }
    } catch {
        Write-Error "Failed to install npm dependencies"
        Write-Info "Error: $($_.Exception.Message)"
        exit 1
    }

    # ===== BUILD PRODUCTION ASSETS =====
    Write-Header "BUILDING PRODUCTION ASSETS"

    Write-Info "Building assets for production..."
    try {
        $buildResult = npm run build
        if ($LASTEXITCODE -eq 0) {
            Write-Success "Production assets built successfully"
        } else {
            throw "Build failed"
        }
    } catch {
        Write-Error "Failed to build production assets"
        Write-Info "Error: $($_.Exception.Message)"
        exit 1
    }

    # Verify build output
    if ((Test-Path "public\build") -and (Get-ChildItem "public\build" | Measure-Object).Count -gt 0) {
        $buildFiles = (Get-ChildItem "public\build" -Recurse | Measure-Object).Count
        Write-Success "Build directory created with $buildFiles files"
    } else {
        Write-Error "Build directory is empty or missing"
        exit 1
    }
} else {
    Write-Warning "Skipping npm install and build (SkipBuild flag set)"
}

# ===== OPTIMIZE COMPOSER AUTOLOADER =====
Write-Header "OPTIMIZING COMPOSER AUTOLOADER"

try {
    $composerOptimize = composer install --optimize-autoloader --no-dev 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Success "Composer autoloader optimized"
    } else {
        Write-Warning "Could not optimize composer locally, will be done during deployment"
    }
} catch {
    Write-Info "Composer optimization will be performed during cPanel deployment"
}

# ===== GENERATE DEPLOYMENT FILES =====
Write-Header "PREPARING DEPLOYMENT FILES"

Write-Info "Preparing .htaccess files for ai.architex.co.za..."

# Copy the correct .htaccess files
if (Test-Path ".htaccess.ai") {
    Copy-Item ".htaccess.ai" ".htaccess.deploy"
    Write-Success "Root .htaccess prepared"
} else {
    Write-Error ".htaccess.ai file not found!"
    exit 1
}

if (Test-Path "public\.htaccess.ai") {
    Copy-Item "public\.htaccess.ai" "public\.htaccess.deploy"
    Write-Success "Public .htaccess prepared"
} else {
    Write-Error "public\.htaccess.ai file not found!"
    exit 1
}

# ===== SECURITY CHECKS =====
Write-Header "SECURITY VERIFICATION"

Write-Info "Verifying security configurations..."

$securityIssues = 0

if (Test-Path ".env.local") {
    Write-Warning "Found .env.local file - ensure it's in .gitignore"
    $securityIssues++
}

if (Test-Path "node_modules") {
    Write-Info "node_modules directory will be excluded from deployment"
}

if ($securityIssues -eq 0) {
    Write-Success "Security checks passed"
} else {
    Write-Warning "$securityIssues security issues found - please review"
}

# ===== DEPLOYMENT VERIFICATION =====
Write-Header "DEPLOYMENT VERIFICATION"

Write-Info "Verifying deployment readiness..."

# Check critical files
$criticalFiles = @(
    "composer.json",
    "composer.lock",
    ".env.production.ai",
    "artisan",
    "public\index.php",
    ".cpanel-ai.yml"
)

$missingFiles = @()
foreach ($file in $criticalFiles) {
    if (Test-Path $file) {
        Write-Success "✓ $file"
    } else {
        Write-Error "✗ $file (missing)"
        $missingFiles += $file
    }
}

# Check critical directories
$criticalDirs = @(
    "app",
    "bootstrap", 
    "config",
    "database",
    "public",
    "resources",
    "routes",
    "storage",
    "vendor",
    "public\build"
)

$missingDirs = @()
foreach ($dir in $criticalDirs) {
    if (Test-Path $dir) {
        Write-Success "✓ $dir\"
    } else {
        Write-Error "✗ $dir\ (missing)"
        $missingDirs += $dir
    }
}

if ($missingFiles.Count -gt 0 -or $missingDirs.Count -gt 0) {
    Write-Error "Missing critical files or directories. Cannot proceed with deployment."
    exit 1
}

# ===== FINAL DEPLOYMENT INSTRUCTIONS =====
Write-Header "DEPLOYMENT READY"

Write-Success "🚀 Application is ready for deployment to ai.architex.co.za!"
Write-Host ""
Write-Info "Next steps:"
Write-Host "1. Ensure your cPanel Git repository is configured"
Write-Host "2. Update your database credentials in cPanel (if needed)"
Write-Host "3. Rename .cpanel-ai.yml to .cpanel.yml"
Write-Host "4. Commit all changes to your Git repository"
Write-Host "5. Push to your cPanel Git repository:"
Write-ColoredOutput "   git add ." "Yellow"
Write-ColoredOutput "   git commit -m 'Deploy to ai.architex.co.za'" "Yellow"
Write-ColoredOutput "   git push origin main" "Yellow"
Write-Host ""
Write-Info "After Git deployment:"
Write-Host "• The .cpanel.yml file will automatically run all deployment tasks"
Write-Host "• Your application will be available at: https://ai.architex.co.za"
Write-Host "• Check the deployment.log file on the server for detailed logs"
Write-Host ""
Write-Warning "Important reminders:"
Write-Host "• Update your database credentials in .env.production.ai before deployment"
Write-Host "• Ensure your cPanel MySQL database 'architex_ai' exists"
Write-Host "• Verify SSL certificate is installed for ai.architex.co.za"
Write-Host "• Test the application after deployment"
Write-Host ""
Write-Header "DEPLOYMENT PREPARATION COMPLETED SUCCESSFULLY"
Write-ColoredOutput "Timestamp: $(Get-Date)" "Cyan"

# Pause to allow user to read the output
Write-Host ""
Write-Host "Press any key to continue..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
