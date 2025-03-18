<?php
/**
 * CSS Finder and Link Diagnostic Tool
 * 
 * This tool helps diagnose CSS link issues and find available CSS files
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load configuration
$config = require_once __DIR__ . '/bem-src/config/src/config/src/config/config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>CSS Finder and Diagnostic Tool</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; }
        h1, h2, h3 { color: #2C5282; }
        .section { margin-bottom: 30px; border: 1px solid #ccc; padding: 20px; border-radius: 5px; }
        .file-found { background-color: #d4edda; padding: 10px; border-radius: 3px; margin: 5px 0; }
        .file-missing { background-color: #f8d7da; padding: 10px; border-radius: 3px; margin: 5px 0; }
        pre { background: #f5f5f5; padding: 10px; overflow: auto; border-radius: 3px; }
        .code-sample { border: 1px solid #ddd; padding: 10px; background: #f9f9f9; margin: 10px 0; }
        button { background: #4A6B9F; color: white; border: none; padding: 8px 15px; border-radius: 3px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .tools { margin-top: 20px; padding: 10px; background: #f4f4f4; border-radius: 5px; }
        .tools a { margin-right: 15px; color: #4A6B9F; text-decoration: none; }
    </style>
</head>
<body>
    <h1>CSS Finder and Link Diagnostic Tool</h1>";

// Get information about the server and paths
echo "<div class='section'>
    <h2>Server Information</h2>
    <table>
        <tr><th>Item</th><th>Value</th></tr>
        <tr><td>Server Name</td><td>{$_SERVER['SERVER_NAME']}</td></tr>
        <tr><td>Document Root</td><td>{$_SERVER['DOCUMENT_ROOT']}</td></tr>
        <tr><td>Script Path</td><td>{$_SERVER['SCRIPT_FILENAME']}</td></tr>
        <tr><td>PHP Self</td><td>{$_SERVER['PHP_SELF']}</td></tr>
        <tr><td>Request URI</td><td>{$_SERVER['REQUEST_URI']}</td></tr>
    </table>
</div>";

// Function to check if a file exists and display its details
function checkFile($path, $label = '') {
    echo "<div>";
    if (file_exists($path)) {
        echo "<div class='file-found'>✓ Found: $path";
        if (!empty($label)) {
            echo " ($label)";
        }
        
        // Show file size and modification time
        echo "<br>Size: " . filesize($path) . " bytes";
        echo "<br>Last Modified: " . date("Y-m-d H:i:s", filemtime($path));
        
        // Show a snippet of the content
        $content = file_get_contents($path);
        echo "<br>Content sample:";
        echo "<pre>" . htmlspecialchars(substr($content, 0, 200)) . "...</pre>";
        echo "</div>";
        return true;
    } else {
        echo "<div class='file-missing'>✗ Not found: $path";
        if (!empty($label)) {
            echo " ($label)";
        }
        echo "</div>";
        return false;
    }
    echo "</div>";
}

echo "<div class='section'>
    <h2>Configuration</h2>
    <p>Using configuration from: <code>bem-src/config/src/config/src/config/config.php</code></p>
    
    <h3>SCSS Configuration</h3>
    <p>Source SCSS: <code>" . $config['scss']['source'] . "</code></p>
    
    <h3>CSS Configuration</h3>
    <p>Original CSS Files:</p>
    <ul>";
foreach ($config['css']['original'] as $path) {
    echo "<li><code>$path</code></li>";
}
echo "</ul>
    
    <p>BEM CSS Files:</p>
    <ul>";
foreach ($config['css']['bem'] as $path) {
    echo "<li><code>$path</code></li>";
}
echo "</ul>
    
    <h3>URL Configuration</h3>
    <p>CSS URLs:</p>
    <ul>
        <li>Original: <code>" . $config['urls']['css']['original'] . "</code></li>
        <li>BEM: <code>" . $config['urls']['css']['bem'] . "</code></li>
    </ul>
</div>";

echo "<div class='section'>
    <h2>CSS File Check</h2>
    <p>Checking for CSS files in configured locations:</p>";

$foundOriginalCss = false;
$foundBemCss = false;
$originalCssPath = '';
$bemCssPath = '';

// Check original CSS paths
echo "<h3>Original CSS Files</h3>";
foreach ($config['css']['original'] as $path) {
    $result = checkFile($path);
    if ($result) {
        $foundOriginalCss = true;
        $originalCssPath = $path;
    }
}

// Check BEM CSS paths
echo "<h3>BEM CSS Files</h3>";
foreach ($config['css']['bem'] as $path) {
    $result = checkFile($path);
    if ($result) {
        $foundBemCss = true;
        $bemCssPath = $path;
    }
}

echo "</div>";

// Check index.php for CSS references
echo "<div class='section'>
    <h2>CSS References in index.php</h2>";

$indexPath = '';
foreach ($config['urls']['pages'] as $pageName => $pageUrl) {
    if ($pageName === 'home') {
        $indexPath = $_SERVER['DOCUMENT_ROOT'] . $pageUrl;
        break;
    }
}

if (empty($indexPath)) {
    $indexPath = $_SERVER['DOCUMENT_ROOT'] . '/index.php';
}

$foundIndex = false;

if (file_exists($indexPath)) {
    $foundIndex = true;
    echo "<p>Found index.php at: $indexPath</p>";
    
    // Search for CSS links
    $content = file_get_contents($indexPath);
    echo "<p>Searching for CSS links in the index file:</p>";
    
    // Extract CSS links
    preg_match_all('/<link[^>]+rel=[\'"]stylesheet[\'"][^>]+href=[\'"]([^\'"]+)[\'"]/', $content, $matches);
    
    if (!empty($matches[1])) {
        echo "<ul>";
        foreach ($matches[1] as $cssLink) {
            echo "<li>CSS Link found: <code>$cssLink</code></li>";
            
            // Try to resolve the CSS path
            if (strpos($cssLink, '/') === 0) {
                // Absolute path from web root
                $cssPath = $_SERVER['DOCUMENT_ROOT'] . $cssLink;
            } else {
                // Relative path from index.php
                $cssPath = dirname($indexPath) . '/' . $cssLink;
            }
            
            checkFile($cssPath, "resolved from link");
        }
        echo "</ul>";
    } else {
        echo "<p>No CSS links found in index.php</p>";
    }
} else {
    echo "<p>Could not find index.php at $indexPath</p>";
    
    // Try searching for any PHP files
    $phpFiles = glob($_SERVER['DOCUMENT_ROOT'] . '/*.php');
    if (!empty($phpFiles)) {
        echo "<p>Found these PHP files in document root:</p>";
        echo "<ul>";
        foreach ($phpFiles as $phpFile) {
            echo "<li>" . basename($phpFile) . "</li>";
        }
        echo "</ul>";
    }
}

echo "</div>";

// Calculate web paths for CSS files
$webUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
$originalCssWebPath = $config['urls']['css']['original'];
$bemCssWebPath = $config['urls']['css']['bem'];

echo "<div class='section'>
    <h2>Diagnostic Results & Recommendations</h2>";

if (!$foundOriginalCss) {
    echo "<p><strong>Problem:</strong> Original CSS file not found. This will cause style-tester.php to fail loading original styles.</p>";
    echo "<p><strong>Solution:</strong> Ensure your main CSS file exists in one of the expected locations. You can:</p>";
    echo "<ol>
        <li>Create test CSS files using <a href='create-test-css.php'>create-test-css.php</a></li>
        <li>Update bem-src/config/src/config/src/config/config.php with the correct paths for your environment</li>
    </ol>";
}

if (!$foundBemCss) {
    echo "<p><strong>Problem:</strong> BEM CSS file not found. This will cause style-tester.php to fail loading BEM styles.</p>";
    echo "<p><strong>Solution:</strong> Create or compile your BEM CSS file:</p>";
    echo "<ol>
        <li>Run <a href='compile-bem.php'>compile-bem.php</a> to compile your SCSS files</li>
        <li>Use <a href='create-test-css.php'>create-test-css.php</a> to create placeholder files</li>
    </ol>";
}

if ($foundOriginalCss && $foundBemCss) {
    echo "<p class='file-found'><strong>✓ CSS files look good!</strong> Both original and BEM CSS files were found.</p>";
    echo "<p>Original CSS: $originalCssPath</p>";
    echo "<p>BEM CSS: $bemCssPath</p>";
    
    echo "<p>If your style-tester.php still isn't working, try:</p>";
    echo "<ol>
        <li>Check if the web URLs in bem-src/config/src/config/src/config/config.php are correct</li>
        <li>Clear your browser cache or try with incognito/private mode</li>
        <li>Check for JavaScript errors in the browser console</li>
    </ol>";

    echo "<p>Web URLs to test:</p>";
    echo "<ul>
        <li><a href='$webUrl$originalCssWebPath' target='_blank'>Test Original CSS Link</a></li>
        <li><a href='$webUrl$bemCssWebPath' target='_blank'>Test BEM CSS Link</a></li>
    </ul>";
}

echo "</div>";

// Troubleshooting section
echo "<div class='section'>
    <h2>Common Issues & Solutions</h2>
    
    <h3>1. CSS Files Not Found</h3>
    <p>If CSS files are not found in the expected locations:</p>
    <ul>
        <li>Run <a href='create-test-css.php'>create-test-css.php</a> to generate test CSS files</li>
        <li>Update paths in bem-src/config/src/config/src/config/config.php to match your server structure</li>
    </ul>
    
    <h3>2. JavaScript Errors</h3>
    <p>JavaScript errors in the browser console may be unrelated to CSS:</p>
    <ul>
        <li>These are often due to MIME type issues or missing files</li>
        <li>They won't directly affect CSS loading but may indicate other problems</li>
    </ul>
    
    <h3>3. Relative Path Issues</h3>
    <p>If CSS paths work in one environment but not another:</p>
    <ul>
        <li>Update bem-src/config/src/config/src/config/config.php with absolute paths relative to the document root</li>
        <li>Make sure all CSS URLs start with a forward slash (/)</li>
    </ul>
</div>";

// Tools section
echo "<div class='tools'>
    <h3>BEM Tools</h3>
    <a href='style-tester.php'>Style Tester</a>
    <a href='style-visual-diff.php'>Visual Diff</a>
    <a href='create-test-css.php'>Create Test CSS</a>
    <a href='compile-bem.php'>Compile BEM SCSS</a>
</div>";

echo "</body>
</html>"; 