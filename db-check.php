<?php
/**
 * Database connection check script
 * 
 * This script checks if the application can connect to the database
 * Delete this file after deployment for security reasons
 */

// Security check - change this token to something unique
$securityToken = 'change_this_to_a_secure_random_string';

// Verify security token
if (!isset($_GET['token']) || $_GET['token'] !== $securityToken) {
    die('Unauthorized access. Please provide a valid security token.');
}

// Load environment variables from .env file
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments and empty lines
        if (strpos(trim($line), '#') === 0 || trim($line) === '') {
            continue;
        }
        
        // Parse the line
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        // Remove quotes if present
        if (strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) {
            $value = substr($value, 1, -1);
        } elseif (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1) {
            $value = substr($value, 1, -1);
        }
        
        // Set environment variable
        putenv("$name=$value");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

// Get database configuration from environment variables
$dbConnection = getenv('DB_CONNECTION') ?: 'mysql';
$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbPort = getenv('DB_PORT') ?: '3306';
$dbDatabase = getenv('DB_DATABASE') ?: 'laravel';
$dbUsername = getenv('DB_USERNAME') ?: 'root';
$dbPassword = getenv('DB_PASSWORD') ?: '';

// Start output
echo "<!DOCTYPE html>
<html>
<head>
    <title>ArchiConnect App Database Check</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #3B8A8A; }
        h2 { color: #2D7A7A; }
        pre { background-color: #F4F7F6; padding: 10px; border-radius: 5px; }
        .success { color: green; }
        .error { color: red; }
        table { border-collapse: collapse; width: 100%; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>ArchiConnect App Database Check</h1>
";

// Display database configuration (with password masked)
echo "<h2>Database Configuration</h2>";
echo "<table>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
echo "<tr><td>Connection</td><td>" . htmlspecialchars($dbConnection) . "</td></tr>";
echo "<tr><td>Host</td><td>" . htmlspecialchars($dbHost) . "</td></tr>";
echo "<tr><td>Port</td><td>" . htmlspecialchars($dbPort) . "</td></tr>";
echo "<tr><td>Database</td><td>" . htmlspecialchars($dbDatabase) . "</td></tr>";
echo "<tr><td>Username</td><td>" . htmlspecialchars($dbUsername) . "</td></tr>";
echo "<tr><td>Password</td><td>" . (empty($dbPassword) ? "<span class='error'>Not set</span>" : "********") . "</td></tr>";
echo "</table>";

// Try to connect to the database
echo "<h2>Connection Test</h2>";
try {
    $dsn = "{$dbConnection}:host={$dbHost};port={$dbPort};dbname={$dbDatabase}";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, $dbUsername, $dbPassword, $options);
    echo "<p class='success'>Successfully connected to the database!</p>";
    
    // Check if migrations have been run
    echo "<h2>Database Tables</h2>";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "<p>Found " . count($tables) . " tables in the database:</p>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . htmlspecialchars($table) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p class='error'>No tables found in the database. Migrations may not have been run yet.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p class='error'>Failed to connect to the database: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Please check your database configuration in the .env file.</p>";
}

echo "</body></html>";
