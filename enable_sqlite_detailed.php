<?php
// Get the path to the php.ini file
$phpIniPath = php_ini_loaded_file();
echo "PHP INI Path: " . $phpIniPath . "\n";

// Read the current php.ini file
$phpIniContent = file_get_contents($phpIniPath);

// Create a backup of the php.ini file
$backupPath = $phpIniPath . '.bak' . time();
if (copy($phpIniPath, $backupPath)) {
    echo "Created backup of php.ini at: " . $backupPath . "\n";
} else {
    echo "Failed to create backup of php.ini\n";
    exit(1);
}

// Find the extension directory
preg_match('/extension_dir\s*=\s*"([^"]+)"/', $phpIniContent, $matches);
$extensionDir = isset($matches[1]) ? $matches[1] : 'ext';
echo "Extension directory: " . $extensionDir . "\n";

// Check if the extension files exist
$sqlite3Path = $extensionDir . DIRECTORY_SEPARATOR . 'php_sqlite3.dll';
$pdoSqlitePath = $extensionDir . DIRECTORY_SEPARATOR . 'php_pdo_sqlite.dll';

echo "SQLite3 extension file: " . (file_exists($sqlite3Path) ? "Exists" : "Not found") . "\n";
echo "PDO SQLite extension file: " . (file_exists($pdoSqlitePath) ? "Exists" : "Not found") . "\n";

// Uncomment or add the extension lines
$lines = explode("\n", $phpIniContent);
$sqlite3Added = false;
$pdoSqliteAdded = false;

foreach ($lines as $i => $line) {
    // Uncomment SQLite3 extension
    if (preg_match('/^\s*;?\s*extension\s*=\s*(php_)?sqlite3(\.dll)?/', $line)) {
        $lines[$i] = 'extension=php_sqlite3.dll';
        $sqlite3Added = true;
        echo "Uncommented SQLite3 extension\n";
    }
    
    // Uncomment PDO SQLite extension
    if (preg_match('/^\s*;?\s*extension\s*=\s*(php_)?pdo_sqlite(\.dll)?/', $line)) {
        $lines[$i] = 'extension=php_pdo_sqlite.dll';
        $pdoSqliteAdded = true;
        echo "Uncommented PDO SQLite extension\n";
    }
}

// If the extensions weren't found, add them
if (!$sqlite3Added) {
    // Find the extensions section
    $extensionSectionIndex = -1;
    foreach ($lines as $i => $line) {
        if (preg_match('/^\s*;\s*Windows\s+Extensions\s*$/', $line)) {
            $extensionSectionIndex = $i;
            break;
        }
    }
    
    if ($extensionSectionIndex >= 0) {
        array_splice($lines, $extensionSectionIndex + 1, 0, 'extension=php_sqlite3.dll');
        echo "Added SQLite3 extension\n";
    } else {
        echo "Could not find Windows Extensions section\n";
    }
}

if (!$pdoSqliteAdded) {
    // Find the extensions section
    $extensionSectionIndex = -1;
    foreach ($lines as $i => $line) {
        if (preg_match('/^\s*;\s*Windows\s+Extensions\s*$/', $line)) {
            $extensionSectionIndex = $i;
            break;
        }
    }
    
    if ($extensionSectionIndex >= 0) {
        array_splice($lines, $extensionSectionIndex + 1, 0, 'extension=php_pdo_sqlite.dll');
        echo "Added PDO SQLite extension\n";
    } else {
        echo "Could not find Windows Extensions section\n";
    }
}

// Write the modified content back to the php.ini file
$newPhpIniContent = implode("\n", $lines);
if (file_put_contents($phpIniPath, $newPhpIniContent)) {
    echo "Successfully updated php.ini\n";
} else {
    echo "Failed to update php.ini\n";
    exit(1);
}

echo "Please restart your web server for the changes to take effect.\n";
