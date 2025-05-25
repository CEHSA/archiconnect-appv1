<?php
// Create ZIP file for deployment
$zip = new ZipArchive();
$filename = "deployment.zip";

if ($zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    exit("Cannot open <$filename>\n");
}

// List of directories to include
$directories = [
    'app',
    'bootstrap',
    'config',
    'database',
    'public',
    'resources',
    'routes',
    'storage',
    'vendor'
];

// List of files to include
$files = [
    'artisan',
    'composer.json',
    'composer.lock',
    '.htaccess',
    '.env.production',
    '.cpanel.yml'
];

// Function to add files recursively
function addFilesToZip($zip, $directory, $zipPath = '') {
    $handle = opendir($directory);
    while (($file = readdir($handle)) !== false) {
        if ($file != '.' && $file != '..') {
            $filePath = $directory . '/' . $file;
            if (is_file($filePath)) {
                if (strpos($file, '.git') === false && 
                    strpos($file, '.env.example') === false &&
                    strpos($file, 'phpunit') === false &&
                    strpos($file, '.log') === false) {
                    $zip->addFile($filePath, $zipPath . $file);
                }
            } elseif (is_dir($filePath)) {
                if (strpos($file, 'node_modules') === false && 
                    strpos($file, '.git') === false &&
                    strpos($file, 'tests') === false) {
                    $zip->addEmptyDir($zipPath . $file);
                    addFilesToZip($zip, $filePath, $zipPath . $file . '/');
                }
            }
        }
    }
    closedir($handle);
}

// Add directories
foreach ($directories as $directory) {
    if (is_dir($directory)) {
        $zip->addEmptyDir($directory);
        addFilesToZip($zip, $directory, $directory . '/');
    }
}

// Add individual files
foreach ($files as $file) {
    if (file_exists($file)) {
        $zip->addFile($file, basename($file));
    }
}

// Create required empty directories
$emptyDirs = [
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/framework/cache',
    'storage/logs',
    'bootstrap/cache'
];

foreach ($emptyDirs as $dir) {
    $zip->addEmptyDir($dir);
}

$zip->close();

echo "Created deployment.zip successfully\n";
?>
