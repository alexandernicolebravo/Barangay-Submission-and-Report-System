<?php
/**
 * Clear Cache Script for GoDaddy Hosting
 * This script clears various Laravel caches when artisan commands are not available
 */

// Set the base path
$basePath = __DIR__;

echo "Starting cache clearing process...\n";

// Clear view cache
$viewCachePath = $basePath . '/storage/framework/views';
if (is_dir($viewCachePath)) {
    $files = glob($viewCachePath . '/*.php');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "✓ View cache cleared\n";
} else {
    echo "✗ View cache directory not found\n";
}

// Clear compiled cache
$compiledPath = $basePath . '/storage/framework/compiled.php';
if (file_exists($compiledPath)) {
    unlink($compiledPath);
    echo "✓ Compiled cache cleared\n";
}

// Clear config cache
$configCachePath = $basePath . '/bootstrap/cache/config.php';
if (file_exists($configCachePath)) {
    unlink($configCachePath);
    echo "✓ Config cache cleared\n";
}

// Clear route cache
$routeCachePath = $basePath . '/bootstrap/cache/routes-v7.php';
if (file_exists($routeCachePath)) {
    unlink($routeCachePath);
    echo "✓ Route cache cleared\n";
}

// Clear services cache
$servicesCachePath = $basePath . '/bootstrap/cache/services.php';
if (file_exists($servicesCachePath)) {
    unlink($servicesCachePath);
    echo "✓ Services cache cleared\n";
}

// Clear session files (optional - be careful with this)
$sessionPath = $basePath . '/storage/framework/sessions';
if (is_dir($sessionPath)) {
    $files = glob($sessionPath . '/*');
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== '.gitignore') {
            unlink($file);
        }
    }
    echo "✓ Session files cleared\n";
}

echo "\nCache clearing completed!\n";
echo "Please update your APP_URL in the .env file to your actual domain URL.\n";
echo "For GoDaddy subdirectory setup, APP_URL should be: https://yourdomain.com/DILG_system/MyProject\n";
echo "(Replace 'yourdomain.com' with your actual domain)\n";
?>
