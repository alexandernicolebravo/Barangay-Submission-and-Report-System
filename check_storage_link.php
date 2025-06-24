<?php
/**
 * Storage Link Checker and Creator for GoDaddy Hosting
 * This script checks and creates the storage symbolic link when artisan is not available
 */

$basePath = __DIR__;
$publicStoragePath = $basePath . '/public/storage';
$storageAppPublicPath = $basePath . '/storage/app/public';

echo "Checking storage link configuration...\n\n";

// Check if storage/app/public exists
if (!is_dir($storageAppPublicPath)) {
    echo "✗ Storage app/public directory does not exist: $storageAppPublicPath\n";
    echo "Creating storage/app/public directory...\n";
    mkdir($storageAppPublicPath, 0755, true);
    echo "✓ Created storage/app/public directory\n";
} else {
    echo "✓ Storage app/public directory exists\n";
}

// Check if public/storage exists
if (is_link($publicStoragePath)) {
    echo "✓ Storage link exists\n";
    $linkTarget = readlink($publicStoragePath);
    echo "  Link target: $linkTarget\n";
    
    // Check if link is correct
    $expectedTarget = $storageAppPublicPath;
    if (realpath($linkTarget) === realpath($expectedTarget)) {
        echo "✓ Storage link is correctly configured\n";
    } else {
        echo "✗ Storage link target is incorrect\n";
        echo "  Expected: $expectedTarget\n";
        echo "  Actual: $linkTarget\n";
        
        // Remove incorrect link
        unlink($publicStoragePath);
        echo "✓ Removed incorrect storage link\n";
        
        // Create correct link
        if (symlink($storageAppPublicPath, $publicStoragePath)) {
            echo "✓ Created correct storage link\n";
        } else {
            echo "✗ Failed to create storage link\n";
        }
    }
} elseif (is_dir($publicStoragePath)) {
    echo "✗ public/storage exists as directory instead of symbolic link\n";
    echo "This might cause issues. Consider removing it and creating a proper symbolic link.\n";
} else {
    echo "✗ Storage link does not exist\n";
    echo "Creating storage link...\n";
    
    if (symlink($storageAppPublicPath, $publicStoragePath)) {
        echo "✓ Created storage link successfully\n";
    } else {
        echo "✗ Failed to create storage link\n";
        echo "You may need to create it manually or contact your hosting provider.\n";
    }
}

// Check if announcements directory exists in storage
$announcementsPath = $storageAppPublicPath . '/announcements';
if (!is_dir($announcementsPath)) {
    echo "\nCreating announcements directory...\n";
    mkdir($announcementsPath, 0755, true);
    echo "✓ Created announcements directory\n";
} else {
    echo "\n✓ Announcements directory exists\n";
}

// List some files to verify
$files = glob($announcementsPath . '/*');
if (count($files) > 0) {
    echo "✓ Found " . count($files) . " files in announcements directory\n";
    echo "Sample files:\n";
    for ($i = 0; $i < min(3, count($files)); $i++) {
        echo "  - " . basename($files[$i]) . "\n";
    }
} else {
    echo "! No files found in announcements directory\n";
}

echo "\nStorage link check completed!\n";
?>
