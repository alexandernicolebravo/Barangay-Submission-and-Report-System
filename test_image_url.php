<?php
/**
 * Quick Image URL Test for GoDaddy
 * This script tests what URLs are being generated for announcement images
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>Image URL Test</h2>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>";

// Test basic asset function
echo "<h3>1. Basic Configuration</h3>";
echo "<strong>APP_URL:</strong> " . env('APP_URL') . "<br>";
echo "<strong>Current Request URL:</strong> " . (isset($_SERVER['HTTP_HOST']) ? 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : 'CLI') . "<br>";

// Test asset URL generation
echo "<h3>2. Asset URL Generation Test</h3>";
$testPath = 'storage/announcements/test.jpg';
$assetUrl = asset($testPath);
echo "<strong>Test asset URL:</strong> $assetUrl<br>";

// Test with a real announcement
echo "<h3>3. Real Announcement Test</h3>";
try {
    $announcement = \App\Models\Announcement::where('image_path', '!=', null)->first();
    if ($announcement) {
        echo "<strong>Announcement:</strong> {$announcement->title}<br>";
        echo "<strong>Image Path:</strong> {$announcement->image_path}<br>";
        echo "<strong>Generated URL (asset):</strong> " . asset('storage/' . $announcement->image_path) . "<br>";
        echo "<strong>Generated URL (model):</strong> {$announcement->image_url}<br>";
        
        // Check if file exists
        $storagePath = storage_path('app/public/' . $announcement->image_path);
        $publicPath = public_path('storage/' . $announcement->image_path);
        
        echo "<strong>Storage file exists:</strong> " . (file_exists($storagePath) ? '<span class="success">✓ Yes</span>' : '<span class="error">✗ No</span>') . "<br>";
        echo "<strong>Public file accessible:</strong> " . (file_exists($publicPath) ? '<span class="success">✓ Yes</span>' : '<span class="error">✗ No</span>') . "<br>";
        
        // Test the actual image
        echo "<h3>4. Image Display Test</h3>";
        echo "<strong>Direct Image Test:</strong><br>";
        echo "<img src='" . asset('storage/' . $announcement->image_path) . "' alt='Test' style='max-width:200px;border:1px solid #ccc;' onerror=\"this.style.border='2px solid red'; this.alt='FAILED TO LOAD';\">";
        
        // Show the raw HTML that would be generated
        echo "<h3>5. Raw HTML Output</h3>";
        echo "<pre>";
        echo htmlspecialchars("background-image: url('" . asset('storage/' . $announcement->image_path) . "');");
        echo "</pre>";
        
    } else {
        echo "<span class='error'>No announcements with images found</span><br>";
    }
} catch (Exception $e) {
    echo '<span class="error">Error: ' . $e->getMessage() . '</span><br>';
}

// Test direct file access
echo "<h3>6. Direct File Access Test</h3>";
$testFiles = glob(public_path('storage/announcements/*'));
if (count($testFiles) > 0) {
    $testFile = basename($testFiles[0]);
    $directUrl = env('APP_URL') . '/storage/announcements/' . $testFile;
    echo "<strong>Direct file URL:</strong> $directUrl<br>";
    echo "<strong>Test direct access:</strong><br>";
    echo "<img src='$directUrl' alt='Direct Test' style='max-width:200px;border:1px solid #ccc;' onerror=\"this.style.border='2px solid red'; this.alt='DIRECT ACCESS FAILED';\">";
}

echo "<h3>7. Troubleshooting</h3>";
echo "<p>If images are not loading, check:</p>";
echo "<ul>";
echo "<li>Make sure APP_URL in .env is correct: <code>https://yourdomain.com/DILG_system/MyProject</code></li>";
echo "<li>Clear cache by running clear_cache.php</li>";
echo "<li>Verify storage link with check_storage_link.php</li>";
echo "<li>Check browser developer tools for 404 errors</li>";
echo "</ul>";
?>
