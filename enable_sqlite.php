<?php
// Get the path to the php.ini file
$phpIniPath = php_ini_loaded_file();
echo "PHP INI Path: " . $phpIniPath . "\n";

// Read the current php.ini file
$phpIniContent = file_get_contents($phpIniPath);

// Check if the extensions are already enabled
$sqlite3Enabled = strpos($phpIniContent, 'extension=sqlite3') !== false || 
                 strpos($phpIniContent, 'extension=php_sqlite3.dll') !== false;
$pdoSqliteEnabled = strpos($phpIniContent, 'extension=pdo_sqlite') !== false || 
                   strpos($phpIniContent, 'extension=php_pdo_sqlite.dll') !== false;

echo "SQLite3 Extension: " . ($sqlite3Enabled ? "Enabled" : "Disabled") . "\n";
echo "PDO SQLite Extension: " . ($pdoSqliteEnabled ? "Enabled" : "Disabled") . "\n";

// Create a backup of the php.ini file
$backupPath = $phpIniPath . '.bak';
if (!file_exists($backupPath)) {
    if (copy($phpIniPath, $backupPath)) {
        echo "Created backup of php.ini at: " . $backupPath . "\n";
    } else {
        echo "Failed to create backup of php.ini\n";
        exit(1);
    }
}

// Enable the extensions if they are not already enabled
$modified = false;

if (!$sqlite3Enabled) {
    // Find the extensions section
    $pattern = '/;extension=/';
    $replacement = "extension=php_sqlite3.dll\n;extension=";
    $phpIniContent = preg_replace($pattern, $replacement, $phpIniContent, 1);
    $modified = true;
    echo "Enabled SQLite3 extension\n";
}

if (!$pdoSqliteEnabled) {
    // Find the extensions section
    $pattern = '/;extension=pdo_/';
    $replacement = "extension=php_pdo_sqlite.dll\n;extension=pdo_";
    $phpIniContent = preg_replace($pattern, $replacement, $phpIniContent, 1);
    $modified = true;
    echo "Enabled PDO SQLite extension\n";
}

// Write the modified content back to the php.ini file
if ($modified) {
    if (file_put_contents($phpIniPath, $phpIniContent)) {
        echo "Successfully updated php.ini\n";
    } else {
        echo "Failed to update php.ini\n";
        exit(1);
    }
} else {
    echo "No changes needed to php.ini\n";
}

echo "Please restart your web server for the changes to take effect.\n";
