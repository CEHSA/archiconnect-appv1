<?php
$extensions = get_loaded_extensions();
$sqliteExtensions = array_filter($extensions, function($ext) {
    return stripos($ext, 'sqlite') !== false;
});

echo "SQLite Extensions:\n";
foreach ($sqliteExtensions as $ext) {
    echo "- $ext\n";
}

echo "\nPDO Drivers:\n";
if (extension_loaded('pdo')) {
    $drivers = PDO::getAvailableDrivers();
    foreach ($drivers as $driver) {
        echo "- $driver\n";
    }
} else {
    echo "PDO extension is not loaded\n";
}
