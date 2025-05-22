<?php
/**
 * Post-deployment script for ArchiConnect App on cPanel
 * 
 * This script runs the necessary Laravel commands after deployment
 * Upload this file to your cPanel hosting and access it via browser
 * to complete the deployment process
 */

// Set maximum execution time to 5 minutes
ini_set('max_execution_time', 300);

// Security check - change this token to something unique
$securityToken = 'change_this_to_a_secure_random_string';

// Verify security token
if (!isset($_GET['token']) || $_GET['token'] !== $securityToken) {
    die('Unauthorized access. Please provide a valid security token.');
}

// Define the base path
$basePath = __DIR__;

// Function to run shell commands and capture output
function runCommand($command, $workingDir = null) {
    $descriptorSpec = [
        0 => ["pipe", "r"],  // stdin
        1 => ["pipe", "w"],  // stdout
        2 => ["pipe", "w"]   // stderr
    ];
    
    $process = proc_open($command, $descriptorSpec, $pipes, $workingDir);
    
    if (is_resource($process)) {
        $output = stream_get_contents($pipes[1]);
        $error = stream_get_contents($pipes[2]);
        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $returnCode = proc_close($process);
        
        return [
            'output' => $output,
            'error' => $error,
            'code' => $returnCode
        ];
    }
    
    return [
        'output' => '',
        'error' => 'Failed to execute command',
        'code' => -1
    ];
}

// Function to display command results
function displayResult($title, $result) {
    echo "<h3>$title</h3>";
    echo "<pre>";
    echo "Output:\n" . htmlspecialchars($result['output']) . "\n\n";
    if ($result['error']) {
        echo "Error:\n" . htmlspecialchars($result['error']) . "\n\n";
    }
    echo "Return code: " . $result['code'];
    echo "</pre>";
    echo "<hr>";
}

// Start output
echo "<!DOCTYPE html>
<html>
<head>
    <title>ArchiConnect App Deployment</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #3B8A8A; }
        h2 { color: #2D7A7A; }
        h3 { color: #2D5C5C; }
        pre { background-color: #F4F7F6; padding: 10px; border-radius: 5px; }
        .success { color: green; }
        .error { color: red; }
        hr { border: 1px solid #eee; }
    </style>
</head>
<body>
    <h1>ArchiConnect App Deployment</h1>
    <h2>Running post-deployment tasks...</h2>
";

// Check if .env file exists
if (!file_exists($basePath . '/.env')) {
    echo "<div class='error'>Error: .env file not found. Please ensure you've copied .env.production to .env</div>";
    exit;
}

// Run artisan commands
$commands = [
    'Generate application key' => 'php artisan key:generate --force',
    'Run database migrations' => 'php artisan migrate --force',
    'Clear application cache' => 'php artisan cache:clear',
    'Cache configuration' => 'php artisan config:cache',
    'Cache routes' => 'php artisan route:cache',
    'Cache views' => 'php artisan view:cache',
    'Create storage link' => 'php artisan storage:link',
];

foreach ($commands as $title => $command) {
    $result = runCommand($command, $basePath);
    displayResult($title, $result);
}

// Set proper permissions
echo "<h3>Setting proper permissions</h3>";
echo "<pre>";

// Try to set permissions using PHP
$directories = [
    'storage',
    'storage/framework',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/framework/cache',
    'storage/logs',
    'storage/app',
    'storage/app/public',
    'bootstrap/cache'
];

foreach ($directories as $dir) {
    $fullPath = $basePath . '/' . $dir;
    if (file_exists($fullPath)) {
        if (chmod($fullPath, 0755)) {
            echo "Set permissions for $dir: Success\n";
        } else {
            echo "Set permissions for $dir: Failed\n";
        }
    } else {
        echo "Directory $dir does not exist\n";
    }
}

echo "</pre>";
echo "<hr>";

// Completion message
echo "<h2 class='success'>Post-deployment tasks completed!</h2>";
echo "<p>Your ArchiConnect App should now be properly configured and ready to use.</p>";
echo "<p>If you encounter any issues, please check the Laravel log files in the storage/logs directory.</p>";
echo "</body></html>";
