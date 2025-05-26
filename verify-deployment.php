<?php
/**
 * Post-Deployment Verification Script for ai.architex.co.za
 * 
 * This script verifies that the Laravel application is properly deployed
 * and all components are working correctly.
 * 
 * Usage: Upload this file to your deployment directory and access via browser
 * URL: https://ai.architex.co.za/verify-deployment.php?token=YOUR_SECURITY_TOKEN
 */

// Security token - change this to something unique for your deployment
$securityToken = 'ai_architex_deploy_verify_2025';

// Check security token
if (!isset($_GET['token']) || $_GET['token'] !== $securityToken) {
    http_response_code(403);
    die('
    <!DOCTYPE html>
    <html>
    <head>
        <title>Access Denied</title>
        <style>body{font-family:Arial,sans-serif;padding:20px;background:#f5f5f5;}</style>
    </head>
    <body>
        <h1>Access Denied</h1>
        <p>Please provide a valid security token to access this verification script.</p>
        <p>URL format: <code>verify-deployment.php?token=YOUR_TOKEN</code></p>
    </body>
    </html>
    ');
}

// Set execution time limit
set_time_limit(300);

// Start output buffering
ob_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI.Architex.co.za - Deployment Verification</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 1.1em;
        }
        .content {
            padding: 30px;
        }
        .test-section {
            margin-bottom: 30px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }
        .test-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            font-weight: bold;
            color: #2c3e50;
        }
        .test-content {
            padding: 20px;
        }
        .status {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.8em;
        }
        .status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .status.warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .status.info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .command-output {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            white-space: pre-wrap;
            margin: 10px 0;
            max-height: 300px;
            overflow-y: auto;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .metric {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .metric-value {
            font-size: 2em;
            font-weight: bold;
            color: #3498db;
        }
        .metric-label {
            color: #7f8c8d;
            font-size: 0.9em;
        }
        .footer {
            background: #34495e;
            color: white;
            padding: 20px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 AI.Architex.co.za</h1>
            <p>Deployment Verification Report</p>
            <p><?php echo date('Y-m-d H:i:s'); ?> | Server: <?php echo $_SERVER['HTTP_HOST']; ?></p>
        </div>
        
        <div class="content">
            <?php
            
            // Initialize counters
            $totalTests = 0;
            $passedTests = 0;
            $failedTests = 0;
            $warnings = 0;
            
            // Helper function to run commands and capture output
            function runCommand($command, $workingDir = null) {
                $originalDir = getcwd();
                if ($workingDir && is_dir($workingDir)) {
                    chdir($workingDir);
                }
                
                $descriptorSpec = [
                    0 => ["pipe", "r"],
                    1 => ["pipe", "w"],
                    2 => ["pipe", "w"]
                ];
                
                $process = proc_open($command, $descriptorSpec, $pipes, $workingDir);
                
                if (is_resource($process)) {
                    fclose($pipes[0]);
                    $stdout = stream_get_contents($pipes[1]);
                    $stderr = stream_get_contents($pipes[2]);
                    fclose($pipes[1]);
                    fclose($pipes[2]);
                    $returnCode = proc_close($process);
                    
                    if ($workingDir) {
                        chdir($originalDir);
                    }
                    
                    return [
                        'output' => $stdout,
                        'error' => $stderr,
                        'code' => $returnCode
                    ];
                }
                
                if ($workingDir) {
                    chdir($originalDir);
                }
                
                return [
                    'output' => '',
                    'error' => 'Failed to execute command',
                    'code' => 1
                ];
            }
            
            // Helper function to display test results
            function displayTest($title, $status, $message = '', $details = '') {
                global $totalTests, $passedTests, $failedTests, $warnings;
                
                $totalTests++;
                
                switch ($status) {
                    case 'success':
                        $passedTests++;
                        $statusClass = 'success';
                        $statusText = 'PASS';
                        break;
                    case 'error':
                        $failedTests++;
                        $statusClass = 'error';
                        $statusText = 'FAIL';
                        break;
                    case 'warning':
                        $warnings++;
                        $statusClass = 'warning';
                        $statusText = 'WARN';
                        break;
                    default:
                        $statusClass = 'info';
                        $statusText = 'INFO';
                }
                
                echo "<div class='test-section'>";
                echo "<div class='test-header'>";
                echo "<span class='status $statusClass'>$statusText</span> $title";
                echo "</div>";
                echo "<div class='test-content'>";
                if ($message) {
                    echo "<p>$message</p>";
                }
                if ($details) {
                    echo "<div class='command-output'>$details</div>";
                }
                echo "</div>";
                echo "</div>";
            }
            
            // Get application base path
            $basePath = __DIR__;
            
            // ===== ENVIRONMENT TESTS =====
            echo "<h2>🔧 Environment Configuration</h2>";
            
            // Check if .env file exists
            if (file_exists($basePath . '/.env')) {
                displayTest('Environment File', 'success', '.env file exists and is readable');
                
                // Load environment variables
                $envVars = [];
                $lines = file($basePath . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    if (strpos(trim($line), '#') === 0 || trim($line) === '') {
                        continue;
                    }
                    list($name, $value) = explode('=', $line, 2);
                    $envVars[trim($name)] = trim($value, '"\'');
                }
                
                // Check critical environment variables
                $requiredVars = ['APP_NAME', 'APP_ENV', 'APP_KEY', 'APP_URL', 'DB_CONNECTION', 'DB_HOST', 'DB_DATABASE'];
                $missingVars = [];
                foreach ($requiredVars as $var) {
                    if (!isset($envVars[$var]) || empty($envVars[$var])) {
                        $missingVars[] = $var;
                    }
                }
                
                if (empty($missingVars)) {
                    displayTest('Required Environment Variables', 'success', 'All required environment variables are set');
                } else {
                    displayTest('Required Environment Variables', 'error', 'Missing variables: ' . implode(', ', $missingVars));
                }
                
                // Display key environment values
                $envDisplay = "APP_NAME: " . ($envVars['APP_NAME'] ?? 'Not set') . "\n";
                $envDisplay .= "APP_ENV: " . ($envVars['APP_ENV'] ?? 'Not set') . "\n";
                $envDisplay .= "APP_URL: " . ($envVars['APP_URL'] ?? 'Not set') . "\n";
                $envDisplay .= "DB_CONNECTION: " . ($envVars['DB_CONNECTION'] ?? 'Not set') . "\n";
                $envDisplay .= "DB_HOST: " . ($envVars['DB_HOST'] ?? 'Not set') . "\n";
                displayTest('Environment Configuration', 'info', 'Current environment settings', $envDisplay);
                
            } else {
                displayTest('Environment File', 'error', '.env file not found');
            }
            
            // ===== PHP ENVIRONMENT =====
            echo "<h2>🐘 PHP Environment</h2>";
            
            // PHP Version
            $phpVersion = PHP_VERSION;
            if (version_compare($phpVersion, '8.1.0', '>=')) {
                displayTest('PHP Version', 'success', "PHP $phpVersion (Recommended for Laravel)");
            } elseif (version_compare($phpVersion, '8.0.0', '>=')) {
                displayTest('PHP Version', 'warning', "PHP $phpVersion (Consider upgrading to 8.1+)");
            } else {
                displayTest('PHP Version', 'error', "PHP $phpVersion (Upgrade required)");
            }
            
            // PHP Extensions
            $requiredExtensions = [
                'openssl', 'pdo', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo'
            ];
            
            $missingExtensions = [];
            $loadedExtensions = [];
            
            foreach ($requiredExtensions as $ext) {
                if (extension_loaded($ext)) {
                    $loadedExtensions[] = $ext;
                } else {
                    $missingExtensions[] = $ext;
                }
            }
            
            if (empty($missingExtensions)) {
                displayTest('PHP Extensions', 'success', 'All required PHP extensions are loaded', implode(', ', $loadedExtensions));
            } else {
                displayTest('PHP Extensions', 'error', 'Missing extensions: ' . implode(', ', $missingExtensions), 'Loaded: ' . implode(', ', $loadedExtensions));
            }
            
            // ===== LARAVEL APPLICATION TESTS =====
            echo "<h2>🎯 Laravel Application</h2>";
            
            // Check if artisan exists
            if (file_exists($basePath . '/artisan')) {
                displayTest('Artisan Console', 'success', 'Artisan command-line tool is available');
                
                // Test Laravel installation
                $artisanAbout = runCommand('php artisan about', $basePath);
                if ($artisanAbout['code'] === 0) {
                    displayTest('Laravel Framework', 'success', 'Laravel application is properly installed', $artisanAbout['output']);
                } else {
                    displayTest('Laravel Framework', 'error', 'Laravel application has issues', $artisanAbout['error']);
                }
                
                // Test configuration caching
                $configCache = runCommand('php artisan config:cache', $basePath);
                if ($configCache['code'] === 0) {
                    displayTest('Configuration Caching', 'success', 'Configuration cached successfully');
                } else {
                    displayTest('Configuration Caching', 'warning', 'Configuration caching failed', $configCache['error']);
                }
                
            } else {
                displayTest('Artisan Console', 'error', 'Artisan file not found');
            }
            
            // ===== DATABASE TESTS =====
            echo "<h2>🗄️ Database Connection</h2>";
            
            try {
                // Test database connection using Laravel
                $migrationCheck = runCommand('php artisan migrate:status', $basePath);
                if ($migrationCheck['code'] === 0) {
                    displayTest('Database Connection', 'success', 'Database connection successful', $migrationCheck['output']);
                    
                    // Run migrations
                    $migrate = runCommand('php artisan migrate --force', $basePath);
                    if ($migrate['code'] === 0) {
                        displayTest('Database Migrations', 'success', 'Database migrations completed successfully');
                    } else {
                        displayTest('Database Migrations', 'warning', 'Migration issues detected', $migrate['error']);
                    }
                    
                } else {
                    displayTest('Database Connection', 'error', 'Database connection failed', $migrationCheck['error']);
                }
            } catch (Exception $e) {
                displayTest('Database Connection', 'error', 'Database test failed: ' . $e->getMessage());
            }
            
            // ===== STORAGE AND PERMISSIONS =====
            echo "<h2>📁 Storage & Permissions</h2>";
            
            // Check storage directories
            $storageDirectories = [
                'storage/app',
                'storage/app/public',
                'storage/framework',
                'storage/framework/cache',
                'storage/framework/sessions',
                'storage/framework/views',
                'storage/logs',
                'bootstrap/cache'
            ];
            
            $permissionIssues = [];
            foreach ($storageDirectories as $dir) {
                $fullPath = $basePath . '/' . $dir;
                if (is_dir($fullPath)) {
                    if (is_writable($fullPath)) {
                        // Directory exists and is writable
                    } else {
                        $permissionIssues[] = $dir . ' (not writable)';
                    }
                } else {
                    $permissionIssues[] = $dir . ' (does not exist)';
                }
            }
            
            if (empty($permissionIssues)) {
                displayTest('Storage Permissions', 'success', 'All storage directories are properly configured');
            } else {
                displayTest('Storage Permissions', 'error', 'Storage permission issues', implode("\n", $permissionIssues));
            }
            
            // Check storage link
            $storageLink = runCommand('php artisan storage:link', $basePath);
            if ($storageLink['code'] === 0) {
                displayTest('Storage Link', 'success', 'Public storage link created successfully');
            } else {
                displayTest('Storage Link', 'warning', 'Storage link creation failed', $storageLink['error']);
            }
            
            // ===== OPTIMIZATION TESTS =====
            echo "<h2>⚡ Application Optimization</h2>";
            
            // Route caching
            $routeCache = runCommand('php artisan route:cache', $basePath);
            if ($routeCache['code'] === 0) {
                displayTest('Route Caching', 'success', 'Routes cached successfully');
            } else {
                displayTest('Route Caching', 'warning', 'Route caching failed', $routeCache['error']);
            }
            
            // View caching
            $viewCache = runCommand('php artisan view:cache', $basePath);
            if ($viewCache['code'] === 0) {
                displayTest('View Caching', 'success', 'Views cached successfully');
            } else {
                displayTest('View Caching', 'warning', 'View caching failed', $viewCache['error']);
            }
            
            // Application optimization
            $optimize = runCommand('php artisan optimize', $basePath);
            if ($optimize['code'] === 0) {
                displayTest('Application Optimization', 'success', 'Application optimized successfully');
            } else {
                displayTest('Application Optimization', 'warning', 'Optimization issues detected', $optimize['error']);
            }
            
            // ===== SECURITY TESTS =====
            echo "<h2>🔒 Security Configuration</h2>";
            
            // Check if APP_DEBUG is false in production
            if (isset($envVars['APP_DEBUG']) && strtolower($envVars['APP_DEBUG']) === 'false') {
                displayTest('Debug Mode', 'success', 'Debug mode is disabled (production setting)');
            } else {
                displayTest('Debug Mode', 'warning', 'Debug mode may be enabled (security risk in production)');
            }
            
            // Check HTTPS
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                displayTest('HTTPS Security', 'success', 'Site is being served over HTTPS');
            } else {
                displayTest('HTTPS Security', 'warning', 'Site may not be using HTTPS');
            }
            
            // Check .htaccess files
            if (file_exists($basePath . '/.htaccess') && file_exists($basePath . '/public/.htaccess')) {
                displayTest('Web Server Configuration', 'success', '.htaccess files are present');
            } else {
                displayTest('Web Server Configuration', 'warning', 'Missing .htaccess files');
            }
            
            // ===== SUMMARY =====
            echo "<h2>📊 Deployment Summary</h2>";
            
            ?>
            
            <div class="grid">
                <div class="metric">
                    <div class="metric-value"><?php echo $totalTests; ?></div>
                    <div class="metric-label">Total Tests</div>
                </div>
                <div class="metric">
                    <div class="metric-value" style="color: #27ae60;"><?php echo $passedTests; ?></div>
                    <div class="metric-label">Passed</div>
                </div>
                <div class="metric">
                    <div class="metric-value" style="color: #f39c12;"><?php echo $warnings; ?></div>
                    <div class="metric-label">Warnings</div>
                </div>
                <div class="metric">
                    <div class="metric-value" style="color: #e74c3c;"><?php echo $failedTests; ?></div>
                    <div class="metric-label">Failed</div>
                </div>
            </div>
            
            <?php
            
            // Overall status
            if ($failedTests === 0 && $warnings <= 2) {
                $overallStatus = 'success';
                $overallMessage = '🎉 Deployment is successful! Your Laravel application is properly configured and ready for production use.';
            } elseif ($failedTests === 0) {
                $overallStatus = 'warning';
                $overallMessage = '⚠️ Deployment is mostly successful but has some warnings. Please review the issues above.';
            } else {
                $overallStatus = 'error';
                $overallMessage = '❌ Deployment has critical issues that need to be resolved before the application can function properly.';
            }
            
            displayTest('Overall Deployment Status', $overallStatus, $overallMessage);
            
            ?>
            
            <div class="test-section">
                <div class="test-header">📋 Next Steps</div>
                <div class="test-content">
                    <ol>
                        <li><strong>Security:</strong> Remove this verification script after confirming deployment</li>
                        <li><strong>SSL:</strong> Ensure SSL certificate is properly configured for ai.architex.co.za</li>
                        <li><strong>Monitoring:</strong> Set up application monitoring and error tracking</li>
                        <li><strong>Backups:</strong> Configure regular database and file backups</li>
                        <li><strong>Testing:</strong> Perform thorough functionality testing of your application</li>
                        <li><strong>Performance:</strong> Monitor application performance and optimize as needed</li>
                    </ol>
                </div>
            </div>
            
        </div>
        
        <div class="footer">
            <p>🚀 AI.Architex.co.za | Deployment Verification Complete</p>
            <p>Generated on <?php echo date('Y-m-d H:i:s'); ?></p>
            <p><small>⚠️ Remember to delete this file after verification for security</small></p>
        </div>
    </div>
</body>
</html>

<?php
// Clean output buffer and send to browser
ob_end_flush();
?>
