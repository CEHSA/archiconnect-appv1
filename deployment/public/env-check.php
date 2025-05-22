<?php

// Environment diagnosis script
// Save this as public/env-check.php on your production server

// Disable error reporting for security
error_reporting(0);
ini_set('display_errors', 0);

// Define safe output
function safe_output($key, $value) {
    if (in_array(strtolower($key), ['password', 'key', 'secret', 'token'])) {
        return '[REDACTED]';
    }
    
    if (is_array($value)) {
        return '[Array]';
    }
    
    return htmlspecialchars($value);
}

// Get environment information
$environment = [
    'APP_ENV' => getenv('APP_ENV') ?: 'Not set',
    'APP_DEBUG' => getenv('APP_DEBUG') ?: 'Not set',
    'PHP Version' => phpversion(),
    'Server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'Document Root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
    'Script Path' => $_SERVER['SCRIPT_FILENAME'] ?? 'Unknown',
    'Now' => date('Y-m-d H:i:s'),
];

// Check for built assets
$environment['Vite Manifest'] = file_exists(__DIR__ . '/build/manifest.json') ? 'Present' : 'Missing';
$environment['Manifest Contents'] = file_exists(__DIR__ . '/build/manifest.json') ? 
    substr(file_get_contents(__DIR__ . '/build/manifest.json'), 0, 100) . '...' : 'N/A';

// Check if Laravel is loaded
$environment['Laravel Loaded'] = class_exists('Illuminate\Foundation\Application') ? 'Yes' : 'No';

// Output as HTML
echo "<!DOCTYPE html><html><head><title>Environment Check</title>";
echo "<style>body{font-family:sans-serif;max-width:800px;margin:0 auto;padding:20px}
table{width:100%;border-collapse:collapse}
th,td{padding:8px;text-align:left;border-bottom:1px solid #ddd}
th{background-color:#f2f2f2}
.error{color:red}
.success{color:green}
</style></head><body>";
echo "<h1>Environment Diagnosis</h1>";
echo "<p>This page shows the current environment configuration. For security reasons, please delete this file after use.</p>";

echo "<h2>Environment Variables</h2>";
echo "<table><tr><th>Variable</th><th>Value</th></tr>";
foreach ($environment as $key => $value) {
    $class = '';
    if ($key === 'APP_ENV' && $value !== 'production') {
        $class = 'error';
    } elseif ($key === 'APP_DEBUG' && $value === 'true') {
        $class = 'error';
    } elseif ($key === 'Vite Manifest' && $value === 'Missing') {
        $class = 'error';
    }
    
    echo "<tr class=\"{$class}\"><td>" . htmlspecialchars($key) . "</td><td>" . safe_output($key, $value) . "</td></tr>";
}
echo "</table>";

// Only show file listing when not in production
if (getenv('APP_ENV') !== 'production') {
    echo "<h2>Public Directory Files</h2>";
    echo "<pre>";
    $files = scandir(__DIR__);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $isDir = is_dir(__DIR__ . '/' . $file);
            echo htmlspecialchars($file) . ($isDir ? '/' : '') . "\n";
        }
    }
    echo "</pre>";
}

echo "<p>Generated at: " . date('Y-m-d H:i:s') . "</p>";
echo "</body></html>";
