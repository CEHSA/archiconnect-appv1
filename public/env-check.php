<?php
/**
 * Production Environment Check Script
 * Delete this file after successful deployment verification
 */

// Basic security check
if (!isset($_SERVER['HTTP_X_DEPLOYMENT_CHECK']) || $_SERVER['HTTP_X_DEPLOYMENT_CHECK'] !== 'secure_check_token') {
    header('HTTP/1.0 403 Forbidden');
    exit('Access Denied');
}

$checks = [];

// Check PHP Version
$checks['php_version'] = [
    'status' => version_compare(PHP_VERSION, '8.1.0', '>='),
    'message' => 'PHP Version: ' . PHP_VERSION,
    'required' => 'PHP >= 8.1.0'
];

// Check Required Extensions
$required_extensions = ['pdo', 'mysql', 'openssl', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json', 'curl', 'fileinfo', 'zip'];
$missing_extensions = [];
foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $missing_extensions[] = $ext;
    }
}
$checks['extensions'] = [
    'status' => empty($missing_extensions),
    'message' => empty($missing_extensions) ? 'All required extensions installed' : 'Missing: ' . implode(', ', $missing_extensions),
    'required' => 'Required extensions: ' . implode(', ', $required_extensions)
];

// Check Storage Directory Permissions
$storage_path = __DIR__ . '/../storage';
$storage_writable = is_writable($storage_path);
$checks['storage_permissions'] = [
    'status' => $storage_writable,
    'message' => $storage_writable ? 'Storage directory is writable' : 'Storage directory is not writable',
    'required' => 'Storage directory must be writable'
];

// Check Environment File
$env_exists = file_exists(__DIR__ . '/../.env');
$checks['env_file'] = [
    'status' => $env_exists,
    'message' => $env_exists ? '.env file exists' : '.env file missing',
    'required' => '.env file must exist'
];

// Check Database Connection
try {
    if (file_exists(__DIR__ . '/../.env')) {
        require_once __DIR__ . '/../vendor/autoload.php';
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();
        
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $dbname = $_ENV['DB_DATABASE'];
        $username = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];
        
        $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $checks['database'] = [
            'status' => true,
            'message' => 'Database connection successful',
            'required' => 'Must connect to database'
        ];
    } else {
        $checks['database'] = [
            'status' => false,
            'message' => 'Could not test database connection - no .env file',
            'required' => 'Must connect to database'
        ];
    }
} catch (Exception $e) {
    $checks['database'] = [
        'status' => false,
        'message' => 'Database connection failed: ' . $e->getMessage(),
        'required' => 'Must connect to database'
    ];
}

// Check if public/storage symlink exists
$public_storage = __DIR__ . '/storage';
$checks['storage_link'] = [
    'status' => file_exists($public_storage),
    'message' => file_exists($public_storage) ? 'Storage symlink exists' : 'Storage symlink missing',
    'required' => 'Storage symlink must exist in public directory'
];

// Check Vite Build
$manifest_path = __DIR__ . '/build/manifest.json';
$checks['vite_build'] = [
    'status' => file_exists($manifest_path),
    'message' => file_exists($manifest_path) ? 'Vite build exists' : 'Vite build missing',
    'required' => 'Vite build files must exist'
];

// Output Results
header('Content-Type: application/json');
echo json_encode([
    'timestamp' => date('Y-m-d H:i:s'),
    'checks' => $checks,
    'all_passed' => array_reduce($checks, function($carry, $item) {
        return $carry && $item['status'];
    }, true)
], JSON_PRETTY_PRINT);
