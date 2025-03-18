<?php
/**
 * Create Test CSS Files
 * 
 * This script creates placeholder CSS files in common locations
 * to help with testing the style-tester.php tool
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load configuration
$config = require_once __DIR__ . '/bem-src/config/src/config/src/config/config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Create Test CSS Files</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0 auto; max-width: 800px; padding: 20px; }
        h1 { color: #2C5282; }
        .success { color: green; }
        .error { color: red; }
        pre { background: #f5f5f5; padding: 10px; overflow: auto; }
        .path { font-family: monospace; background: #eee; padding: 2px 5px; }
        .tools { margin-top: 20px; padding: 10px; background: #f4f4f4; border-radius: 5px; }
        .tools a { margin-right: 15px; color: #4A6B9F; text-decoration: none; }
    </style>
</head>
<body>
    <h1>Create Test CSS Files</h1>
    <p>This tool creates placeholder CSS files to test the style-tester.php tool.</p>";

// Create a function to create CSS file
function createCssFile($path, $type) {
    // Ensure directory exists
    $directory = dirname($path);
    if (!file_exists($directory)) {
        if (!mkdir($directory, 0755, true)) {
            echo "<p class='error'>Failed to create directory: <span class='path'>$directory</span></p>";
            return false;
        }
        echo "<p>Created directory: <span class='path'>$directory</span></p>";
    }
    
    // Sample CSS content
    $content = '';
    
    if ($type === 'original') {
        $content = "/* Original CSS (main.css or style.css) */
/* Created by test-css-creator.php */

body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    color: #333;
}

header {
    background-color: #4A6B9F;
    color: white;
    padding: 20px;
}

.hero {
    background-color: #2C5282;
    color: white;
    padding: 50px 20px;
}

h1, h2, h3 {
    margin-top: 0;
}

nav ul {
    list-style: none;
    padding: 0;
}

nav li {
    display: inline-block;
    margin-right: 20px;
}

a {
    color: #4A6B9F;
    text-decoration: none;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}
";
    } else {
        $content = "/* BEM CSS (style-bem.css or main-bem.css) */
/* Created by test-css-creator.php */

body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    color: #333;
}

.site-header {
    background-color: #4A6B9F;
    color: white;
    padding: 20px;
}

.hero {
    background-color: #2C5282;
    color: white;
    padding: 50px 20px;
}

.hero--home {
    height: 50vh;
}

.heading {
    margin-top: 0;
}

.nav__list {
    list-style: none;
    padding: 0;
}

.nav__item {
    display: inline-block;
    margin-right: 20px;
}

.link {
    color: #4A6B9F;
    text-decoration: none;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}
";
    }
    
    // Write the content to the file
    if (file_put_contents($path, $content)) {
        echo "<p class='success'>Created CSS file: <span class='path'>$path</span> (" . strlen($content) . " bytes)</p>";
        return true;
    } else {
        echo "<p class='error'>Failed to create CSS file: <span class='path'>$path</span></p>";
        return false;
    }
}

// Create any missing CSS files
$createdOriginalCss = false;
$createdBemCss = false;

echo "<h2>Creating Original CSS Files</h2>";
foreach ($config['css']['original'] as $path) {
    if (!file_exists($path)) {
        $createdOriginalCss = createCssFile($path, 'original');
        if ($createdOriginalCss) {
            break;
        }
    } else {
        echo "<p>Original CSS already exists: <span class='path'>$path</span></p>";
        $createdOriginalCss = true;
        break;
    }
}

echo "<h2>Creating BEM CSS Files</h2>";
foreach ($config['css']['bem'] as $path) {
    if (!file_exists($path)) {
        $createdBemCss = createCssFile($path, 'bem');
        if ($createdBemCss) {
            break;
        }
    } else {
        echo "<p>BEM CSS already exists: <span class='path'>$path</span></p>";
        $createdBemCss = true;
        break;
    }
}

// Summary
echo "<h2>Summary</h2>";
if ($createdOriginalCss && $createdBemCss) {
    echo "<p class='success'>✓ All necessary CSS files have been created!</p>";
    echo "<p>You can now use the tools below to test your styles.</p>";
} else {
    echo "<p class='error'>✗ There were problems creating some CSS files.</p>";
    echo "<p>Please check the messages above for details.</p>";
}

// Calculate web paths for displaying
$webRoot = $_SERVER['DOCUMENT_ROOT'];
$originalCssWebPath = '';
$bemCssWebPath = '';

foreach ($config['css']['original'] as $path) {
    if (file_exists($path)) {
        $originalCssWebPath = str_replace($webRoot, '', $path);
        break;
    }
}

foreach ($config['css']['bem'] as $path) {
    if (file_exists($path)) {
        $bemCssWebPath = str_replace($webRoot, '', $path);
        break;
    }
}

// Show config
echo "<h2>Configuration</h2>";
echo "<p>Original CSS URL: <span class='path'>" . $config['urls']['css']['original'] . "</span></p>";
echo "<p>BEM CSS URL: <span class='path'>" . $config['urls']['css']['bem'] . "</span></p>";

// Tools section
echo "<div class='tools'>
    <h3>BEM Tools</h3>
    <p>Use these tools to further test and manage your BEM styles:</p>
    <div>
        <a href='style-tester.php'>Style Tester</a>
        <a href='style-visual-diff.php'>Visual Diff</a>
        <a href='css-finder.php'>CSS Finder</a>
        <a href='compile-bem.php'>Compile BEM SCSS</a>
    </div>
</div>";

// Troubleshooting section
echo "<h2>Troubleshooting</h2>";
echo "<p>If you're still having issues with the style-tester.php tool:</p>";
echo "<ol>
    <li>Check that the CSS files exist in the locations shown above</li>
    <li>Ensure that the URLs in bem-src/config/src/config/src/config/config.php are correct for your server</li>
    <li>Use the CSS Finder tool to diagnose any CSS path issues</li>
    <li>Try clearing your browser cache</li>
    <li>Check the browser console for any JavaScript errors</li>
</ol>";

echo "</body>
</html>"; 