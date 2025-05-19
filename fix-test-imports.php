<?php

// Script to fix duplicate PHPUnit\Framework\Attributes\Test imports
$testsDir = __DIR__ . '/tests';

// Function to recursively find all PHP files in a directory
function findPhpFiles($dir) {
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }
    
    return $files;
}

// Function to fix a file's imports
function fixFile($filePath) {
    $content = file_get_contents($filePath);
    
    // Fix duplicate Test imports
    $pattern = '/use\s+PHPUnit\\\\Framework\\\\Attributes\\\\Test;\s*use\s+PHPUnit\\\\Framework\\\\Attributes\\\\Test;/';
    $content = preg_replace($pattern, 'use PHPUnit\\Framework\\Attributes\\Test;', $content);
    
    // Fix inline Test imports
    $pattern = '/use\s+PHPUnit\\\\Framework\\\\Attributes\\\\Test;\s*(?=\s*class|\s*function|\s*test\()/';
    $content = preg_replace($pattern, '', $content);
    
    // Fix use statements in the middle of code
    $pattern = '/(\s+)use\s+PHPUnit\\\\Framework\\\\Attributes\\\\Test;/';
    $content = preg_replace($pattern, '$1', $content);
    
    file_put_contents($filePath, $content);
    
    return true;
}

// Find all PHP files in the tests directory
$files = findPhpFiles($testsDir);
$updatedCount = 0;

// Update each file
foreach ($files as $file) {
    if (fixFile($file)) {
        $updatedCount++;
        echo "Fixed: $file\n";
    }
}

echo "Fixed $updatedCount files.\n";
