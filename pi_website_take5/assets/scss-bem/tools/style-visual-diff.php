<?php
/**
 * BEM Style Visual Diff Tool
 * 
 * This script provides a side-by-side comparison of the original styles
 * and the new BEM-based styles to help identify visual differences.
 * 
 * Usage:
 * 1. Copy this file to your website root or publicly accessible directory
 * 2. Access it via browser: style-visual-diff.php?page=home
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug mode
$debugMode = isset($_GET['debug']) && $_GET['debug'] === '1';

// Function to output debug information
function debugPrint($message, $data = null) {
    global $debugMode;
    
    if ($debugMode) {
        echo "<div style='background: #f8f8f8; border: 1px solid #ddd; padding: 10px; margin: 10px 0; font-family: monospace;'>";
        echo "<strong>DEBUG:</strong> " . htmlspecialchars($message);
        if ($data !== null) {
            echo "<pre>" . htmlspecialchars(print_r($data, true)) . "</pre>";
        }
        echo "</div>";
    }
}

// Load configuration
$config = require_once __DIR__ . '/bem-config.php';
debugPrint("Loaded configuration", $config);

// Get query parameters
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
debugPrint("Page parameter", $page);

// Validate page parameter
if (!array_key_exists($page, $config['urls']['pages'])) {
    die('Invalid page specified. Available pages: ' . implode(', ', array_keys($config['urls']['pages'])));
}

// Get the target page URL
$pageUrl = $config['urls']['pages'][$page];
debugPrint("Target page URL", $pageUrl);

// Get base URL for the site
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
debugPrint("Base URL", $baseUrl);

// Read CSS content from files
$originalCssContent = '';
$bemCssContent = '';

// Read original CSS
foreach ($config['css']['original'] as $path) {
    if (file_exists($path)) {
        $originalCssContent = file_get_contents($path);
        debugPrint("Read original CSS from", $path);
        break;
    }
}

// Read BEM CSS
foreach ($config['css']['bem'] as $path) {
    if (file_exists($path)) {
        $bemCssContent = file_get_contents($path);
        debugPrint("Read BEM CSS from", $path);
        break;
    }
}

// Standalone HTML templates for original and BEM
$originalTemplate = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Original Style - {$page}</title>
    <style>
    {$originalCssContent}
    </style>
</head>
<body class="visual-diff-frame original-style">
    <iframe src="{$baseUrl}{$pageUrl}" style="width:100%; height:100%; border:none;"></iframe>
</body>
</html>
HTML;

$bemTemplate = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BEM Style - {$page}</title>
    <style>
    {$bemCssContent}
    </style>
</head>
<body class="visual-diff-frame bem-style">
    <iframe src="{$baseUrl}{$pageUrl}" style="width:100%; height:100%; border:none;"></iframe>
</body>
</html>
HTML;

// Create temporary files for the templates
$tempDir = sys_get_temp_dir();
$originalTempFile = $tempDir . '/original_' . md5(time() . rand()) . '.html';
$bemTempFile = $tempDir . '/bem_' . md5(time() . rand()) . '.html';

file_put_contents($originalTempFile, $originalTemplate);
file_put_contents($bemTempFile, $bemTemplate);

debugPrint("Created temp files", [
    "original" => $originalTempFile,
    "bem" => $bemTempFile
]);

// Generate URLs for the frames
$originalUrl = $originalTempFile;
$bemUrl = $bemTempFile;

// Check for CSS content
if (empty($originalCssContent)) {
    $cssWarning = "⚠️ Original CSS content could not be loaded. Check file paths.";
} else {
    $cssWarning = "";
}

if (empty($bemCssContent)) {
    $bemWarning = "⚠️ BEM CSS content could not be loaded. Check file paths.";
} else {
    $bemWarning = "";
}

// Output the HTML
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BEM Style Visual Diff - <?php echo ucfirst($page); ?></title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            font-family: Arial, sans-serif;
        }
        .container {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        .header {
            background: #4A6B9F;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 1.2rem;
        }
        .controls {
            display: flex;
            align-items: center;
        }
        .controls label {
            margin-right: 10px;
        }
        .controls select {
            padding: 5px;
            margin-right: 15px;
        }
        .frames {
            display: flex;
            flex: 1;
            overflow: hidden;
        }
        .frame-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #ccc;
        }
        .frame-container:last-child {
            border-right: none;
        }
        .frame-title {
            background: #f4f4f4;
            padding: 8px 15px;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        iframe {
            flex: 1;
            border: none;
            width: 100%;
        }
        .sync-scroll {
            margin-left: 15px;
            display: flex;
            align-items: center;
        }
        button {
            padding: 5px 10px;
            background: #2C5282;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin-left: 10px;
        }
        .tools {
            padding: 5px 10px;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            border-top: 1px solid #ddd;
        }
        .tools a {
            margin: 0 10px;
            color: #4A6B9F;
            text-decoration: none;
            font-size: 14px;
        }
        .warning {
            color: #856404;
            background-color: #fff3cd;
            padding: 5px 10px;
            font-size: 12px;
            text-align: center;
            border-bottom: 1px solid #ffeeba;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>BEM Style Visual Comparison - <?php echo ucfirst($page); ?></h1>
            <div class="controls">
                <label for="page-selector">Test Page:</label>
                <select id="page-selector">
                    <?php foreach ($config['urls']['pages'] as $pageName => $pageLink): ?>
                        <option value="<?php echo $pageName; ?>" <?php echo ($pageName === $page ? 'selected' : ''); ?>>
                            <?php echo ucfirst($pageName); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <div class="sync-scroll">
                    <input type="checkbox" id="sync-scrolling" checked>
                    <label for="sync-scrolling">Sync Scrolling</label>
                </div>
                
                <button id="reload-frames">Reload</button>
                <?php if ($debugMode): ?>
                    <a href="?page=<?php echo $page; ?>" style="color: white; margin-left: 10px;">Exit Debug</a>
                <?php else: ?>
                    <a href="?page=<?php echo $page; ?>&debug=1" style="color: white; margin-left: 10px;">Debug</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="frames">
            <div class="frame-container">
                <div class="frame-title">Original Styles</div>
                <?php if (!empty($cssWarning)): ?>
                    <div class="warning"><?php echo $cssWarning; ?></div>
                <?php endif; ?>
                <iframe src="file://<?php echo $originalUrl; ?>" id="original-frame"></iframe>
            </div>
            <div class="frame-container">
                <div class="frame-title">BEM Styles</div>
                <?php if (!empty($bemWarning)): ?>
                    <div class="warning"><?php echo $bemWarning; ?></div>
                <?php endif; ?>
                <iframe src="file://<?php echo $bemUrl; ?>" id="bem-frame"></iframe>
            </div>
        </div>
        <div class="tools">
            <a href="css-finder.php">CSS Diagnostic Tool</a>
            <a href="create-test-css.php">Create Test CSS Files</a>
            <a href="compile-bem.php">Compile BEM SCSS</a>
            <a href="style-tester.php?page=<?php echo $page; ?>">Style Tester</a>
        </div>
    </div>

    <script>
        document.getElementById('page-selector').addEventListener('change', function() {
            const page = this.value;
            const debug = <?php echo $debugMode ? 'true' : 'false'; ?>;
            window.location.href = 'style-visual-diff.php?page=' + page + (debug ? '&debug=1' : '');
        });

        document.getElementById('reload-frames').addEventListener('click', function() {
            document.getElementById('original-frame').contentWindow.location.reload();
            document.getElementById('bem-frame').contentWindow.location.reload();
        });

        // Synchronized scrolling
        const originalFrame = document.getElementById('original-frame');
        const bemFrame = document.getElementById('bem-frame');
        const syncScrolling = document.getElementById('sync-scrolling');
        let isScrolling = false;

        originalFrame.addEventListener('load', setupScrollSync);
        bemFrame.addEventListener('load', setupScrollSync);

        function setupScrollSync() {
            // Make sure both frames are loaded
            if (!originalFrame.contentWindow || !bemFrame.contentWindow) return;
            
            try {
                originalFrame.contentWindow.addEventListener('scroll', function() {
                    if (syncScrolling.checked && !isScrolling) {
                        isScrolling = true;
                        const scrollRatio = this.scrollY / (this.document.body.scrollHeight - this.innerHeight);
                        const targetScroll = scrollRatio * (bemFrame.contentWindow.document.body.scrollHeight - bemFrame.contentWindow.innerHeight);
                        bemFrame.contentWindow.scrollTo(0, targetScroll);
                        setTimeout(function() { isScrolling = false; }, 50);
                    }
                });

                bemFrame.contentWindow.addEventListener('scroll', function() {
                    if (syncScrolling.checked && !isScrolling) {
                        isScrolling = true;
                        const scrollRatio = this.scrollY / (this.document.body.scrollHeight - this.innerHeight);
                        const targetScroll = scrollRatio * (originalFrame.contentWindow.document.body.scrollHeight - originalFrame.contentWindow.innerHeight);
                        originalFrame.contentWindow.scrollTo(0, targetScroll);
                        setTimeout(function() { isScrolling = false; }, 50);
                    }
                });
            } catch (e) {
                console.error("Error setting up scroll sync:", e);
            }
        }
    </script>
</body>
</html>

<?php
// Clean up temp files on shutdown
register_shutdown_function(function() use ($originalTempFile, $bemTempFile) {
    if (file_exists($originalTempFile)) {
        unlink($originalTempFile);
    }
    if (file_exists($bemTempFile)) {
        unlink($bemTempFile);
    }
});
?> 