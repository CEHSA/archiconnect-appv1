<?php

// Script to update PHPUnit doc-comments to attributes
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

// Function to update a file's doc-comments to attributes
function updateFile($filePath) {
    $content = file_get_contents($filePath);
    
    // Add the Test attribute import if not already present
    if (!preg_match('/use\s+PHPUnit\\\\Framework\\\\Attributes\\\\Test;/', $content)) {
        $content = preg_replace(
            '/(use\s+[^;]+;\n)(?!use\s+PHPUnit\\\\Framework\\\\Attributes\\\\Test;)/s',
            "$1use PHPUnit\\Framework\\Attributes\\Test;\n",
            $content
        );
    }
    
    // Replace /** @test */ with #[Test]
    $content = preg_replace('/\s+\/\*\*\s+@test\s+\*\/\s+public\s+function/s', "\n    #[Test]\n    public function", $content);
    
    file_put_contents($filePath, $content);
    
    return true;
}

// Find all PHP files in the tests directory
$files = findPhpFiles($testsDir);
$updatedCount = 0;

// Update each file
foreach ($files as $file) {
    if (updateFile($file)) {
        $updatedCount++;
        echo "Updated: $file\n";
    }
}

echo "Updated $updatedCount files.\n";
