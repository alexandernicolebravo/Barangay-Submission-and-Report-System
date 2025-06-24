<?php
/**
 * Image Display Diagnostic Script for GoDaddy
 * This script helps diagnose why announcement images are not displaying
 */

// Include Laravel bootstrap to access models and helpers
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>DILG Image Display Diagnostic</h2>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;} pre{background:#f5f5f5;padding:10px;border-radius:5px;}</style>";

// Check environment configuration
echo "<h3>1. Environment Configuration</h3>";
echo "<strong>APP_URL:</strong> " . env('APP_URL') . "<br>";
echo "<strong>APP_ENV:</strong> " . env('APP_ENV') . "<br>";
echo "<strong>Current URL:</strong> " . (isset($_SERVER['HTTP_HOST']) ? 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : 'CLI') . "<br>";

// Check storage paths
echo "<h3>2. Storage Path Configuration</h3>";
$storagePath = storage_path('app/public');
$publicStoragePath = public_path('storage');

echo "<strong>Storage app/public path:</strong> $storagePath<br>";
echo "<strong>Public storage path:</strong> $publicStoragePath<br>";
echo "<strong>Storage app/public exists:</strong> " . (is_dir($storagePath) ? '<span class="success">✓ Yes</span>' : '<span class="error">✗ No</span>') . "<br>";
echo "<strong>Public storage link exists:</strong> " . (file_exists($publicStoragePath) ? '<span class="success">✓ Yes</span>' : '<span class="error">✗ No</span>') . "<br>";

if (is_link($publicStoragePath)) {
    echo "<strong>Storage link target:</strong> " . readlink($publicStoragePath) . "<br>";
    echo "<strong>Link is valid:</strong> " . (is_dir(readlink($publicStoragePath)) ? '<span class="success">✓ Yes</span>' : '<span class="error">✗ No</span>') . "<br>";
}

// Check announcements directory
echo "<h3>3. Announcements Directory</h3>";
$announcementsPath = $storagePath . '/announcements';
$publicAnnouncementsPath = $publicStoragePath . '/announcements';

echo "<strong>Announcements storage path:</strong> $announcementsPath<br>";
echo "<strong>Announcements public path:</strong> $publicAnnouncementsPath<br>";
echo "<strong>Announcements directory exists:</strong> " . (is_dir($announcementsPath) ? '<span class="success">✓ Yes</span>' : '<span class="error">✗ No</span>') . "<br>";
echo "<strong>Public announcements accessible:</strong> " . (is_dir($publicAnnouncementsPath) ? '<span class="success">✓ Yes</span>' : '<span class="error">✗ No</span>') . "<br>";

// List announcement files
if (is_dir($announcementsPath)) {
    $files = glob($announcementsPath . '/*');
    echo "<strong>Files in storage:</strong> " . count($files) . "<br>";
    if (count($files) > 0) {
        echo "<strong>Sample files:</strong><br>";
        for ($i = 0; $i < min(3, count($files)); $i++) {
            $filename = basename($files[$i]);
            $filesize = filesize($files[$i]);
            echo "- $filename (" . round($filesize/1024, 2) . " KB)<br>";
        }
    }
}

// Test asset URL generation
echo "<h3>4. Asset URL Generation</h3>";
if (function_exists('asset')) {
    $testImagePath = 'announcements/test.jpg';
    $assetUrl = asset('storage/' . $testImagePath);
    echo "<strong>Test asset URL:</strong> $assetUrl<br>";
    
    // Check if the URL structure looks correct
    $expectedBase = env('APP_URL') . '/storage/';
    echo "<strong>Expected base URL:</strong> $expectedBase<br>";
    echo "<strong>URL structure correct:</strong> " . (strpos($assetUrl, $expectedBase) === 0 ? '<span class="success">✓ Yes</span>' : '<span class="error">✗ No</span>') . "<br>";
}

// Check database announcements
echo "<h3>5. Database Announcements</h3>";
try {
    $announcements = \App\Models\Announcement::where('image_path', '!=', null)->take(3)->get();
    echo "<strong>Announcements with images:</strong> " . $announcements->count() . "<br>";
    
    if ($announcements->count() > 0) {
        echo "<strong>Sample announcements:</strong><br>";
        foreach ($announcements as $announcement) {
            echo "- ID: {$announcement->id}, Title: {$announcement->title}<br>";
            echo "  Image path: {$announcement->image_path}<br>";
            echo "  Image URL: {$announcement->image_url}<br>";
            
            // Check if file exists
            $fullPath = storage_path('app/public/' . $announcement->image_path);
            echo "  File exists: " . (file_exists($fullPath) ? '<span class="success">✓ Yes</span>' : '<span class="error">✗ No</span>') . "<br>";
            
            // Check if publicly accessible
            $publicPath = public_path('storage/' . $announcement->image_path);
            echo "  Publicly accessible: " . (file_exists($publicPath) ? '<span class="success">✓ Yes</span>' : '<span class="error">✗ No</span>') . "<br>";
            echo "<br>";
        }
    }
} catch (Exception $e) {
    echo '<span class="error">Error accessing database: ' . $e->getMessage() . '</span><br>';
}

// Test image display
echo "<h3>6. Image Display Test</h3>";
try {
    $testAnnouncement = \App\Models\Announcement::where('image_path', '!=', null)->first();
    if ($testAnnouncement) {
        echo "<strong>Testing image display for:</strong> {$testAnnouncement->title}<br>";
        echo "<strong>Image URL:</strong> {$testAnnouncement->image_url}<br>";
        echo "<strong>Image preview:</strong><br>";
        echo "<img src='{$testAnnouncement->image_url}' alt='Test Image' style='max-width:200px;max-height:200px;border:1px solid #ccc;' onerror=\"this.style.display='none'; this.nextSibling.style.display='block';\">";
        echo "<div style='display:none;color:red;'>❌ Image failed to load</div><br>";
    } else {
        echo "<span class='info'>No announcements with images found in database</span><br>";
    }
} catch (Exception $e) {
    echo '<span class="error">Error testing image display: ' . $e->getMessage() . '</span><br>';
}

echo "<h3>7. Recommendations</h3>";
echo "<ul>";
echo "<li>Make sure your APP_URL in .env matches your actual domain: <code>https://yourdomain.com/DILG_system/MyProject</code></li>";
echo "<li>Clear view cache by running the clear_cache.php script</li>";
echo "<li>Verify storage link is working by running check_storage_link.php</li>";
echo "<li>Check file permissions on storage directories (should be 755)</li>";
echo "</ul>";

echo "<p><strong>Next steps:</strong> Run <a href='clear_cache.php'>clear_cache.php</a> and <a href='check_storage_link.php'>check_storage_link.php</a></p>";
?>
