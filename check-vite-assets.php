<?php
// Check for Vite build assets
$manifest_path = __DIR__ . '/public/build/manifest.json';
$missing_files = [];

echo "<h1>Deployment Asset Check</h1>";

if (file_exists($manifest_path)) {
    echo "<p style='color:green'>✓ Vite manifest file found at {$manifest_path}</p>";
    
    // Read the manifest to check for all referenced files
    $manifest = json_decode(file_get_contents($manifest_path), true);
    if ($manifest) {
        echo "<p>Checking manifest entries...</p>";
        echo "<ul>";
        
        foreach ($manifest as $entry => $details) {
            $file_path = __DIR__ . '/public' . $details['file'];
            if (file_exists($file_path)) {
                echo "<li style='color:green'>✓ {$details['file']} exists</li>";
            } else {
                echo "<li style='color:red'>✗ {$details['file']} MISSING</li>";
                $missing_files[] = $details['file'];
            }
            
            // Check CSS files referenced in JS entries
            if (isset($details['css']) && is_array($details['css'])) {
                foreach ($details['css'] as $css_file) {
                    $css_path = __DIR__ . '/public' . $css_file;
                    if (file_exists($css_path)) {
                        echo "<li style='color:green'>✓ {$css_file} exists</li>";
                    } else {
                        echo "<li style='color:red'>✗ {$css_file} MISSING</li>";
                        $missing_files[] = $css_file;
                    }
                }
            }
        }
        
        echo "</ul>";
    } else {
        echo "<p style='color:red'>✗ Could not parse manifest.json</p>";
    }
} else {
    echo "<p style='color:red'>✗ Vite manifest file NOT FOUND at {$manifest_path}</p>";
    $missing_files[] = '/build/manifest.json';
}

if (!empty($missing_files)) {
    echo "<h2 style='color:red'>Missing Files Summary</h2>";
    echo "<p>The following files are referenced but missing:</p>";
    echo "<ul>";
    foreach ($missing_files as $file) {
        echo "<li>{$file}</li>";
    }
    echo "</ul>";
    
    echo "<h2>Troubleshooting</h2>";
    echo "<ol>";
    echo "<li>Make sure you ran <code>npm run build</code> before deployment</li>";
    echo "<li>Check that public/build directory was included in the deployment</li>";
    echo "<li>Verify that the .htaccess file includes appropriate CSP headers</li>";
    echo "</ol>";
} else {
    echo "<h2 style='color:green'>All Vite assets verified successfully!</h2>";
}

// Environment check
echo "<h2>Environment Check</h2>";
echo "<ul>";
echo "<li>APP_ENV: " . (getenv('APP_ENV') ?: 'Not set') . "</li>";
echo "<li>APP_DEBUG: " . (getenv('APP_DEBUG') ?: 'Not set') . "</li>";
echo "</ul>";

// Show a reminder about cache commands
echo "<h2>Post-Deployment Commands</h2>";
echo "<p>Don't forget to run these commands:</p>";
echo "<pre>php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link</pre>";
