<?php
/**
 * BEM Style Testing Tool
 * 
 * This script helps you test the new BEM-based styles against the original styles
 * to ensure the migration doesn't break the appearance of your website.
 * 
 * Usage:
 * 1. Copy this file to your website root or a publicly accessible directory
 * 2. Access it via browser with a URL like: style-tester.php?page=home
 * 3. Use the toggle switch to compare original vs BEM styles
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug mode
$debugMode = isset($_GET['debug']) && $_GET['debug'] === '1';

// Function to output debug information
function debugPrint($message, $data = null) {
    global $debugMode;
    
    $output = "<!-- DEBUG: $message";
    if ($data !== null) {
        $output .= ": " . print_r($data, true);
    }
    $output .= " -->\n";
    
    if ($debugMode) {
        echo "<div style='background: #f8f8f8; border: 1px solid #ddd; padding: 10px; margin: 10px 0; font-family: monospace;'>";
        echo "<strong>DEBUG:</strong> " . htmlspecialchars($message);
        if ($data !== null) {
            echo "<pre>" . htmlspecialchars(print_r($data, true)) . "</pre>";
        }
        echo "</div>";
    } else {
        echo $output;
    }
}

// Load central configuration
$config = require_once __DIR__ . '/bem-src/config/src/config/src/config/config.php';
debugPrint("Loaded configuration", $config);

// Get query parameters
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$style = isset($_GET['style']) ? $_GET['style'] : 'original';

debugPrint("Request parameters", ["page" => $page, "style" => $style]);

// Validate page parameter
if (!array_key_exists($page, $config['urls']['pages'])) {
    die('Invalid page specified. Available pages: ' . implode(', ', array_keys($config['urls']['pages'])));
}

// Get the target page URL
$pageUrl = $config['urls']['pages'][$page];
debugPrint("Target page URL", $pageUrl);

// Prepare the content
$pageContent = '';
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$fullUrl = $baseUrl . $pageUrl;

debugPrint("Full URL for content", $fullUrl);

// Function to get page content
function getPageContent($url) {
    global $debugMode;
    debugPrint("Fetching content from", $url);
    
    // Create a stream context to handle potential SSL certificate issues
    $context = stream_context_create([
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
        'http' => [
            'ignore_errors' => true,
        ]
    ]);
    
    $content = @file_get_contents($url, false, $context);
    
    if ($content === false) {
        debugPrint("file_get_contents failed", error_get_last());
        
        // Try CURL as a fallback
        if (function_exists('curl_init')) {
            debugPrint("Trying CURL as fallback");
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $content = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            debugPrint("CURL result", ["HTTP Code" => $httpCode, "Error" => $curlError]);
            
            if ($httpCode >= 400) {
                return "Error loading page: HTTP code $httpCode";
            }
            
            return $content;
        } else {
            return "Error loading page: " . error_get_last()['message'];
        }
    }
    
    debugPrint("Content fetched successfully", [
        "Length" => strlen($content), 
        "First 100 chars" => substr($content, 0, 100) . "..."
    ]);
    
    return $content;
}

// Function to check if a file exists and is readable
function checkCSSFile($path, $type) {
    global $baseUrl;
    debugPrint("Checking CSS file", ["path" => $path, "type" => $type]);
    
    // Check if it's a URL or a file path
    if (strpos($path, 'http') === 0) {
        // It's a full URL, try to fetch it
        $headers = @get_headers($path);
        if ($headers && strpos($headers[0], '200') !== false) {
            debugPrint("CSS URL is accessible", $path);
            return true;
        }
        
        debugPrint("CSS URL is NOT accessible", ["path" => $path, "headers" => $headers]);
        return false;
    } else {
        // Check for file with leading slash (absolute path from document root)
        $absolutePath = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($path, '/');
        if (file_exists($absolutePath)) {
            debugPrint("CSS file exists on server (absolute path)", $absolutePath);
            return true;
        }
        
        // Also check for file with relative path from current script
        $scriptDir = dirname($_SERVER['SCRIPT_FILENAME']);
        $relativeBasePath = dirname(dirname(dirname($scriptDir))); // Up to project root
        $relativePath = $relativeBasePath . '/' . ltrim($path, '/');
        if (file_exists($relativePath)) {
            debugPrint("CSS file exists on server (relative path)", $relativePath);
            return true;
        }
        
        // Try to resolve from the base URL for web access
        $fullUrl = $baseUrl . '/' . ltrim($path, '/');
        $headers = @get_headers($fullUrl);
        if ($headers && strpos($headers[0], '200') !== false) {
            debugPrint("CSS URL is accessible via web", $fullUrl);
            return true;
        }
        
        debugPrint("CSS file NOT found on server or via web", [
            "absolute" => $absolutePath,
            "relative" => $relativePath,
            "web" => $fullUrl
        ]);
        return false;
    }
}

// Get content
$pageContent = getPageContent($fullUrl);

if (empty($pageContent)) {
    die("Failed to load the content from URL: $fullUrl. Please check that the page exists and is accessible.");
}

// Check for CSS links in the page
$foundOriginalCss = false;
$originalCssLink = '';
$bemCssLink = '';

// Original CSS web URL
$originalCssWeb = $config['urls']['css']['original']; 
$bemCssWeb = $config['urls']['css']['bem'];

debugPrint("CSS URLs from config", [
    "original" => $originalCssWeb,
    "bem" => $bemCssWeb
]);

// Check if CSS files exist and are accessible
$originalCssExists = checkCSSFile($originalCssWeb, "original");
$bemCssExists = checkCSSFile($bemCssWeb, "bem");

debugPrint("CSS files existence check", [
    "original" => $originalCssExists ? "Exists" : "Not found",
    "bem" => $bemCssExists ? "Exists" : "Not found"
]);

// Try to find the CSS link in the content
if (preg_match_all('/<link[^>]+rel=[\'"]stylesheet[\'"][^>]+href=[\'"]([^\'"]+)[\'"][^>]*>/', $pageContent, $matches)) {
    debugPrint("Found CSS links in page", $matches[1]);
    
    foreach ($matches[1] as $cssPath) {
        // Only use CSS files, not external resources
        if (strpos($cssPath, 'http') !== 0 && (strpos($cssPath, '.css') !== false)) {
            $originalCssLink = $cssPath;
            $foundOriginalCss = true;
            debugPrint("Selected CSS from page", $cssPath);
            break;
        }
    }
}

// If original CSS wasn't found in the page, use the configured path
if (!$foundOriginalCss) {
    $originalCssLink = $originalCssWeb;
    debugPrint("Using configured original CSS", $originalCssLink);
}

// If BEM CSS wasn't found in the filesystem, fallback to the configured path
if (empty($bemCssLink)) {
    $bemCssLink = $bemCssWeb;
    debugPrint("Using configured BEM CSS", $bemCssLink);
}

// Add diagnostic information
debugPrint("Diagnostic Information", [
    "Original CSS Found" => ($foundOriginalCss ? 'Yes' : 'No'),
    "Original CSS Link" => $originalCssLink,
    "BEM CSS Link" => $bemCssLink,
    "Style Mode" => $style
]);

// Function to add inline CSS to ensure the controls are visible
function getInlineCSS() {
    return "<style>
    #style-tester-controls {
        position: fixed !important;
        top: 10px !important;
        right: 10px !important;
        background: #fff !important;
        border: 1px solid #ccc !important;
        padding: 10px !important;
        border-radius: 5px !important;
        z-index: 9999 !important;
        box-shadow: 0 0 10px rgba(0,0,0,0.2) !important;
        font-family: Arial, sans-serif !important;
        color: #333 !important;
    }
    #style-tester-controls h3 {
        margin-top: 0 !important;
        font-size: 16px !important;
    }
    #style-tester-controls a {
        text-decoration: none !important;
        display: inline-block !important;
        padding: 5px 10px !important;
        border-radius: 3px !important;
    }
    </style>";
}

// Modify the content to use the selected stylesheet
if ($style === 'bem') {
    // Instead of replacing the link, let's inline the BEM CSS content
    debugPrint("Using BEM CSS (inlining content instead of linking)");
    
    // Try to read the BEM CSS file from disk
    $bemCssContent = '';
    foreach ($config['css']['bem'] as $bemCssPath) {
        if (file_exists($bemCssPath)) {
            $bemCssContent = file_get_contents($bemCssPath);
            debugPrint("Read BEM CSS content from", $bemCssPath);
            break;
        }
    }
    
    if (!empty($bemCssContent)) {
        // Insert the CSS content as an inline style in head
        $inlineStyleTag = "<style id=\"inline-bem-css\">\n/* Inlined BEM CSS content */\n" . $bemCssContent . "\n</style>";
        
        // Replace original CSS link with empty link to prevent loading
        if ($foundOriginalCss && strpos($pageContent, $originalCssLink) !== false) {
            // First, disable the original CSS by setting media to "none"
            $pageContent = preg_replace('/<link[^>]+href=[\'"]' . preg_quote($originalCssLink, '/') . '[\'"][^>]*>/', 
                '<link rel="stylesheet" href="' . $originalCssLink . '" media="none" data-disabled="true">', 
                $pageContent);
            debugPrint("Disabled original CSS link");
        }
        
        // Add our inline style to the head
        $pageContent = str_replace('</head>', $inlineStyleTag . "\n</head>", $pageContent);
        debugPrint("Added inline BEM CSS style tag to head");
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border: 1px solid #f5c6cb; border-radius: 5px;'>";
        echo "<strong>Error:</strong> Could not find or read BEM CSS file content. Check file permissions.";
        echo "</div>";
    }
    
    // Add inline CSS for controls
    $pageContent = str_replace('</head>', getInlineCSS() . '</head>', $pageContent);
} else {
    // We're in original style mode
    debugPrint("Using original CSS (keeping original link)");
    
    // In case the original CSS link is broken, try to inline the original CSS too
    $originalCssContent = '';
    foreach ($config['css']['original'] as $origCssPath) {
        if (file_exists($origCssPath)) {
            $originalCssContent = file_get_contents($origCssPath);
            debugPrint("Read original CSS content from", $origCssPath);
            break;
        }
    }
    
    if (!empty($originalCssContent) && !$foundOriginalCss) {
        // Only inline if we couldn't find a link to the original CSS
        $inlineStyleTag = "<style id=\"inline-original-css\">\n/* Inlined original CSS content */\n" . $originalCssContent . "\n</style>";
        $pageContent = str_replace('</head>', $inlineStyleTag . "\n</head>", $pageContent);
        debugPrint("Added inline original CSS style tag to head (no link found)");
    }
    
    // Add inline CSS for controls
    $pageContent = str_replace('</head>', getInlineCSS() . '</head>', $pageContent);
}

// Add comparison controls
$controlsHtml = '
<div id="style-tester-controls">
    <h3>Style Tester</h3>
    <div style="margin-bottom: 10px;">
        <label style="margin-right: 5px; font-weight: bold;">Current: ' . ucfirst($style) . '</label>
        <div style="display: flex; align-items: center;">
            <a href="?page=' . $page . '&style=original" style="margin-right: 10px; background: ' . ($style === 'original' ? '#4A6B9F' : '#ccc') . '; color: white;">Original</a>
            <a href="?page=' . $page . '&style=bem" style="background: ' . ($style === 'bem' ? '#4A6B9F' : '#ccc') . '; color: white;">BEM</a>
        </div>
    </div>
    <div style="margin-bottom: 10px;">
        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Test Page:</label>
        <select id="page-selector" style="width: 100%; padding: 5px;">
';

foreach ($config['urls']['pages'] as $pageName => $pageLink) {
    $controlsHtml .= '<option value="' . $pageName . '" ' . ($pageName === $page ? 'selected' : '') . '>' . ucfirst($pageName) . '</option>';
}

$controlsHtml .= '
        </select>
    </div>
    <div>
        <button id="visual-diff" style="width: 100%; padding: 5px; background: #2C5282; color: white; border: none; border-radius: 3px; cursor: pointer;">Visual Diff</button>
    </div>
    <div style="margin-top: 10px; font-size: 12px; text-align: center;">
        <a href="css-finder.php" style="color: #4A6B9F; text-decoration: underline; display: inline !important; padding: 0 !important;">Diagnose CSS Issues</a>
        &nbsp;|&nbsp;
        <a href="?debug=1" style="color: #4A6B9F; text-decoration: underline; display: inline !important; padding: 0 !important;">Debug Mode</a>
    </div>
</div>

<script>
document.getElementById("page-selector").addEventListener("change", function() {
    var selectedPage = this.value;
    var currentStyle = "' . $style . '";
    var debug = "' . ($debugMode ? '1' : '0') . '";
    window.location.href = "?page=" + selectedPage + "&style=" + currentStyle + "&debug=" + debug;
});

document.getElementById("visual-diff").addEventListener("click", function() {
    var currentPage = "' . $page . '";
    window.location.href = "style-visual-diff.php?page=" + currentPage;
});
</script>
';

// Insert controls before closing body tag
$pageContent = str_replace('</body>', $controlsHtml . '</body>', $pageContent);

// Direct URL check for CSS files
if ($debugMode) {
    echo "<h2>Direct CSS URL Tests</h2>";
    
    echo "<p>Testing direct access to CSS files:</p>";
    
    // Test original CSS
    echo "<p><strong>Original CSS (" . htmlspecialchars($originalCssWeb) . "):</strong> ";
    $fullOriginalUrl = $baseUrl . '/' . ltrim($originalCssWeb, '/');
    $headers = @get_headers($fullOriginalUrl);
    if ($headers && strpos($headers[0], '200') !== false) {
        echo "<span style='color: green;'>✓ Accessible</span>";
        echo " <a href='" . htmlspecialchars($fullOriginalUrl) . "' target='_blank'>(View)</a>";
    } else {
        echo "<span style='color: red;'>✗ Not accessible</span>";
        echo " <pre>" . htmlspecialchars(print_r($headers, true)) . "</pre>";
        
        // Try to help locate the file
        echo "<p>Trying alternate URLs:</p><ul>";
        
        // Try without leading slash
        $altUrl1 = $baseUrl . '/' . ltrim($originalCssWeb, '/');
        echo "<li>" . htmlspecialchars($altUrl1) . ": ";
        $headers = @get_headers($altUrl1);
        if ($headers && strpos($headers[0], '200') !== false) {
            echo "<span style='color: green;'>✓ Accessible</span>";
            echo " <a href='" . htmlspecialchars($altUrl1) . "' target='_blank'>(View)</a>";
        } else {
            echo "<span style='color: red;'>✗ Not accessible</span>";
        }
        echo "</li>";
        
        // Try relative from current location
        $scriptRelative = str_replace('/tools/', '/../css/', dirname($_SERVER['PHP_SELF']));
        $altUrl2 = $baseUrl . $scriptRelative . '/' . basename($originalCssWeb);
        echo "<li>" . htmlspecialchars($altUrl2) . ": ";
        $headers = @get_headers($altUrl2);
        if ($headers && strpos($headers[0], '200') !== false) {
            echo "<span style='color: green;'>✓ Accessible</span>";
            echo " <a href='" . htmlspecialchars($altUrl2) . "' target='_blank'>(View)</a>";
        } else {
            echo "<span style='color: red;'>✗ Not accessible</span>";
        }
        echo "</li>";
        
        echo "</ul>";
    }
    echo "</p>";
    
    // Test BEM CSS
    echo "<p><strong>BEM CSS (" . htmlspecialchars($bemCssWeb) . "):</strong> ";
    $fullBemUrl = $baseUrl . '/' . ltrim($bemCssWeb, '/');
    $headers = @get_headers($fullBemUrl);
    if ($headers && strpos($headers[0], '200') !== false) {
        echo "<span style='color: green;'>✓ Accessible</span>";
        echo " <a href='" . htmlspecialchars($fullBemUrl) . "' target='_blank'>(View)</a>";
    } else {
        echo "<span style='color: red;'>✗ Not accessible</span>";
        echo " <pre>" . htmlspecialchars(print_r($headers, true)) . "</pre>";
        
        // Try to help locate the file
        echo "<p>Trying alternate URLs:</p><ul>";
        
        // Try without leading slash
        $altUrl1 = $baseUrl . '/' . ltrim($bemCssWeb, '/');
        echo "<li>" . htmlspecialchars($altUrl1) . ": ";
        $headers = @get_headers($altUrl1);
        if ($headers && strpos($headers[0], '200') !== false) {
            echo "<span style='color: green;'>✓ Accessible</span>";
            echo " <a href='" . htmlspecialchars($altUrl1) . "' target='_blank'>(View)</a>";
        } else {
            echo "<span style='color: red;'>✗ Not accessible</span>";
        }
        echo "</li>";
        
        // Try relative from current location
        $scriptRelative = str_replace('/tools/', '/../css/', dirname($_SERVER['PHP_SELF']));
        $altUrl2 = $baseUrl . $scriptRelative . '/' . basename($bemCssWeb);
        echo "<li>" . htmlspecialchars($altUrl2) . ": ";
        $headers = @get_headers($altUrl2);
        if ($headers && strpos($headers[0], '200') !== false) {
            echo "<span style='color: green;'>✓ Accessible</span>";
            echo " <a href='" . htmlspecialchars($altUrl2) . "' target='_blank'>(View)</a>";
        } else {
            echo "<span style='color: red;'>✗ Not accessible</span>";
        }
        echo "</li>";
        
        echo "</ul>";
    }
    echo "</p>";
    
    // CSS file paths on disk
    echo "<h2>CSS File Paths on Disk</h2>";
    
    echo "<p>Checking file existence on disk:</p>";
    
    // Original CSS files
    echo "<p><strong>Original CSS Files:</strong></p>";
    echo "<ul>";
    foreach ($config['css']['original'] as $path) {
        echo "<li>" . htmlspecialchars($path) . ": ";
        if (file_exists($path)) {
            echo "<span style='color: green;'>✓ Exists (" . filesize($path) . " bytes)</span>";
        } else {
            echo "<span style='color: red;'>✗ Not found</span>";
        }
        echo "</li>";
    }
    echo "</ul>";
    
    // BEM CSS files
    echo "<p><strong>BEM CSS Files:</strong></p>";
    echo "<ul>";
    foreach ($config['css']['bem'] as $path) {
        echo "<li>" . htmlspecialchars($path) . ": ";
        if (file_exists($path)) {
            echo "<span style='color: green;'>✓ Exists (" . filesize($path) . " bytes)</span>";
        } else {
            echo "<span style='color: red;'>✗ Not found</span>";
        }
        echo "</li>";
    }
    echo "</ul>";
    
    // Provide fix instructions
    echo "<h2>Possible Solutions</h2>";
    echo "<p>If CSS files are not loading:</p>";
    echo "<ol>";
    echo "<li>Make sure CSS files exist - <a href='create-test-css.php'>Create test CSS files</a></li>";
    echo "<li>Update <code>bem-src/config/src/config/src/config/config.php</code> with correct paths</li>";
    echo "<li>Check URL mapping - CSS files must be accessible via web browser</li>";
    echo "<li>Try clearing browser cache</li>";
    echo "</ol>";
    
    echo "<hr>";
    echo "<p><a href='?debug=0'>Exit Debug Mode</a></p>";
}

// Output the modified page
if ($debugMode) {
    echo "<h2>Modified Page Content Preview</h2>";
    echo "<div style='background: #f5f5f5; padding: 10px; max-height: 300px; overflow: auto; font-family: monospace;'>";
    echo htmlspecialchars(substr($pageContent, 0, 2000)) . "...";
    echo "</div>";
} else {
    echo $pageContent;
}
?> 