<?php
/**
 * Production Environment Diagnostic Script
 * Delete this file after troubleshooting
 */

// Basic security check
if (!isset($_SERVER['HTTP_X_DIAGNOSTIC_KEY']) || $_SERVER['HTTP_X_DIAGNOSTIC_KEY'] !== 'secure_diagnostic_token') {
    header('HTTP/1.0 403 Forbidden');
    exit('Access Denied');
}

header('Content-Type: application/json');

$results = [];

// Check PHP Version
$results['php'] = [
    'version' => PHP_VERSION,
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size')
];

// Check Extensions
$required_extensions = ['pdo', 'pdo_mysql', 'openssl', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json'];
$results['extensions'] = [
    'loaded' => array_filter($required_extensions, 'extension_loaded'),
    'missing' => array_filter($required_extensions, function($ext) { return !extension_loaded($ext); })
];

// Check Storage Permissions
$storage_paths = [
    '../storage/framework',
    '../storage/logs',
    '../bootstrap/cache'
];

$results['storage'] = array_map(function($path) {
    return [
        'path' => $path,
        'exists' => file_exists($path),
        'writable' => is_writable($path),
        'permissions' => file_exists($path) ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A'
    ];
}, $storage_paths);

// Check .env
$results['env'] = [
    'exists' => file_exists('../.env'),
    'production' => getenv('APP_ENV') === 'production',
    'debug' => getenv('APP_DEBUG') === 'true',
    'key_set' => !empty(getenv('APP_KEY')),
    'url_set' => !empty(getenv('APP_URL'))
];

// Check Database Connection
try {
    if (file_exists('../.env')) {
        require_once '../vendor/autoload.php';
        
        $db_config = [
            'driver' => getenv('DB_CONNECTION'),
            'host' => getenv('DB_HOST'),
            'database' => getenv('DB_DATABASE'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD')
        ];
        
        $results['database'] = [
            'config' => array_merge($db_config, ['password' => '[hidden]']),
            'connection_test' => false,
            'error' => null
        ];
        
        try {
            $pdo = new PDO(
                "{$db_config['driver']}:host={$db_config['host']};dbname={$db_config['database']}",
                $db_config['username'],
                $db_config['password']
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $results['database']['connection_test'] = true;
        } catch (PDOException $e) {
            $results['database']['error'] = $e->getMessage();
        }
    }
} catch (Exception $e) {
    $results['database'] = ['error' => $e->getMessage()];
}

// Check Laravel Log
$log_file = '../storage/logs/laravel.log';
$results['laravel_log'] = [
    'exists' => file_exists($log_file),
    'writable' => is_writable($log_file),
    'size' => file_exists($log_file) ? filesize($log_file) : 0,
    'recent_entries' => file_exists($log_file) ? array_slice(array_filter(array_map('trim', file($log_file))), -5) : []
];

// Check Cache Configuration
$results['cache'] = [
    'driver' => getenv('CACHE_DRIVER'),
    'path_writable' => is_writable('../bootstrap/cache'),
    'config_cached' => file_exists('../bootstrap/cache/config.php'),
    'routes_cached' => file_exists('../bootstrap/cache/routes-v7.php')
];

// Check Vite Build
$manifest_path = './build/manifest.json';
$results['vite'] = [
    'manifest_exists' => file_exists($manifest_path),
    'manifest_content' => file_exists($manifest_path) ? json_decode(file_get_contents($manifest_path), true) : null
];

// Output Results
echo json_encode([
    'timestamp' => date('Y-m-d H:i:s'),
    'diagnostics' => $results
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
