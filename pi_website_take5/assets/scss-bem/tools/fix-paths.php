<?php
/**
 * Fix Paths and Setup CSS
 * 
 * This tool helps in troubleshooting path issues and making sure
 * the CSS files are in the right location for testing.
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>BEM Path and CSS Setup Tool</h1>";
echo "<p>This tool diagnoses potential issues with paths and helps prepare the environment for testing.</p>";

// Get server info
echo "<h2>Server Information</h2>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Script Path: " . $_SERVER['SCRIPT_FILENAME'] . "</p>";
echo "<p>Server Name: " . $_SERVER['SERVER_NAME'] . "</p>";
echo "<p>Server Protocol: " . $_SERVER['SERVER_PROTOCOL'] . "</p>";
echo "<p>HTTP Host: " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<p>Web Root Relative Path: " . dirname($_SERVER['PHP_SELF']) . "</p>";

// Base paths for potential CSS locations
$webRootRelative = dirname($_SERVER['PHP_SELF']);
$cssLocations = [
    // Relative to this script
    __DIR__ . '/../../css/style-bem.css',              // Main location: /assets/css/style-bem.css
    dirname(__DIR__ . '/../..') . '/css/style-bem.css', // One level up
    
    // Relative to web root
    $_SERVER['DOCUMENT_ROOT'] . '/assets/css/style-bem.css',
    $_SERVER['DOCUMENT_ROOT'] . '/css/style-bem.css',
    
    // Other potential locations
    dirname(dirname(__DIR__)) . '/css/style-bem.css'
];

// Check for CSS files
echo "<h2>CSS File Location Check</h2>";
$foundCss = false;

foreach ($cssLocations as $index => $cssPath) {
    echo "<div style='margin-bottom: 10px; padding: 5px; border: 1px solid #ccc;'>";
    echo "<p><strong>Location #" . ($index + 1) . ":</strong> " . $cssPath . "</p>";
    
    if (file_exists($cssPath)) {
        echo "<p style='color: green;'>✓ CSS file exists at this location</p>";
        $foundCss = true;
        
        // Check file permissions and size
        echo "<p>Size: " . filesize($cssPath) . " bytes</p>";
        echo "<p>Permissions: " . substr(sprintf('%o', fileperms($cssPath)), -4) . "</p>";
        
        // Show the first few lines
        $cssContent = file_get_contents($cssPath);
        $firstLines = implode("\n", array_slice(explode("\n", $cssContent), 0, 5));
        echo "<p>First few lines:</p>";
        echo "<pre style='background: #f5f5f5; padding: 10px; overflow: auto;'>" . htmlspecialchars($firstLines) . "...</pre>";
    } else {
        echo "<p style='color: red;'>✗ CSS file not found at this location</p>";
        
        // Check if the directory exists
        $cssDir = dirname($cssPath);
        if (file_exists($cssDir)) {
            echo "<p>Directory exists: " . $cssDir . "</p>";
        } else {
            echo "<p>Directory doesn't exist: " . $cssDir . "</p>";
            echo "<p>Attempting to create directory...</p>";
            
            if (mkdir($cssDir, 0755, true)) {
                echo "<p style='color: green;'>✓ Directory created successfully!</p>";
            } else {
                echo "<p style='color: red;'>✗ Failed to create directory</p>";
            }
        }
    }
    echo "</div>";
}

if (!$foundCss) {
    echo "<div style='padding: 10px; background: #ffeeee; border: 1px solid #ffaaaa; margin: 10px 0;'>";
    echo "<h3 style='color: red;'>No CSS Files Found!</h3>";
    echo "<p>We need to create a CSS file for testing. Let's do that now.</p>";
    echo "</div>";
    
    // Try to create a CSS file
    $targetCssPath = $cssLocations[0]; // Use the first location
    $cssDir = dirname($targetCssPath);
    
    if (!file_exists($cssDir)) {
        if (mkdir($cssDir, 0755, true)) {
            echo "<p style='color: green;'>Created directory: " . $cssDir . "</p>";
        } else {
            echo "<p style='color: red;'>Failed to create directory: " . $cssDir . "</p>";
        }
    }
    
    $placeholderCss = "/* Placeholder BEM CSS for testing */\n";
    $placeholderCss .= "/* Created by fix-paths.php */\n\n";
    $placeholderCss .= ".site-header {\n  background-color: #4A6B9F;\n  color: white;\n}\n\n";
    $placeholderCss .= ".home__hero {\n  background-color: #2C5282;\n  color: white;\n  height: 100vh;\n}\n";
    
    if (file_put_contents($targetCssPath, $placeholderCss)) {
        echo "<p style='color: green;'>✓ Created placeholder CSS file at: " . $targetCssPath . "</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create CSS file at: " . $targetCssPath . "</p>";
    }
}

// Original CSS file check
echo "<h2>Original CSS File Check</h2>";
$originalCssLocations = [
    // Relative to this script
    __DIR__ . '/../../css/main.css',                   // Main location with correct name
    dirname(__DIR__ . '/../..') . '/css/main.css',      // One level up with correct name
    
    // Relative to web root
    $_SERVER['DOCUMENT_ROOT'] . '/assets/css/main.css',
    $_SERVER['DOCUMENT_ROOT'] . '/css/main.css',
    
    // Fallbacks with style.css name
    __DIR__ . '/../../css/style.css',
    $_SERVER['DOCUMENT_ROOT'] . '/assets/css/style.css'
];

$foundOriginalCss = false;

foreach ($originalCssLocations as $index => $cssPath) {
    echo "<div style='margin-bottom: 10px; padding: 5px; border: 1px solid #ccc;'>";
    echo "<p><strong>Location #" . ($index + 1) . ":</strong> " . $cssPath . "</p>";
    
    if (file_exists($cssPath)) {
        echo "<p style='color: green;'>✓ Original CSS file exists at this location</p>";
        $foundOriginalCss = true;
        
        // Show file info
        echo "<p>Size: " . filesize($cssPath) . " bytes</p>";
        echo "<p>Last modified: " . date("Y-m-d H:i:s", filemtime($cssPath)) . "</p>";
        
        // Show first few lines
        $cssContent = file_get_contents($cssPath);
        $firstLines = implode("\n", array_slice(explode("\n", $cssContent), 0, 5));
        echo "<p>First few lines:</p>";
        echo "<pre style='background: #f5f5f5; padding: 10px; overflow: auto;'>" . htmlspecialchars($firstLines) . "...</pre>";
    } else {
        echo "<p style='color: red;'>✗ Original CSS file not found at this location</p>";
    }
    echo "</div>";
}

if (!$foundOriginalCss) {
    echo "<div style='padding: 10px; background: #ffeeee; border: 1px solid #ffaaaa; margin: 10px 0;'>";
    echo "<h3 style='color: red;'>No Original CSS Files Found!</h3>";
    echo "<p>We should create a placeholder original CSS file for testing.</p>";
    echo "</div>";
    
    // Try to create a CSS file
    $targetCssPath = $originalCssLocations[0]; // Use the first location with main.css
    $cssDir = dirname($targetCssPath);
    
    if (!file_exists($cssDir)) {
        if (mkdir($cssDir, 0755, true)) {
            echo "<p style='color: green;'>Created directory: " . $cssDir . "</p>";
        } else {
            echo "<p style='color: red;'>Failed to create directory: " . $cssDir . "</p>";
        }
    }
    
    $placeholderCss = "/* Placeholder Original CSS for testing */\n";
    $placeholderCss .= "/* Created by fix-paths.php */\n\n";
    $placeholderCss .= "header {\n  background-color: #4A6B9F;\n  color: white;\n}\n\n";
    $placeholderCss .= ".hero {\n  background-color: #2C5282;\n  color: white;\n  height: 100vh;\n}\n";
    
    if (file_put_contents($targetCssPath, $placeholderCss)) {
        echo "<p style='color: green;'>✓ Created placeholder Original CSS file at: " . $targetCssPath . "</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create Original CSS file at: " . $targetCssPath . "</p>";
    }
}

// Check for SASS/SCSS compiler
echo "<h2>SASS Compiler Check</h2>";
$sassInstalled = false;

// Define potential SASS compiler locations
$sassLocations = [
    // Specific path from user's system
    'C:\\Users\\RonenBitman\\AppData\\Roaming\\npm\\sass.ps1',
    
    // Global installation
    'sass',
    
    // Windows-specific locations
    getenv('APPDATA') . '\\npm\\sass.cmd',
    getenv('APPDATA') . '\\npm\\sass',
    getenv('APPDATA') . '\\npm\\sass.ps1',
    getenv('USERPROFILE') . '\\AppData\\Roaming\\npm\\sass.cmd',
    getenv('USERPROFILE') . '\\AppData\\Roaming\\npm\\sass',
    getenv('USERPROFILE') . '\\AppData\\Roaming\\npm\\sass.ps1',
    
    // Local installation in node_modules
    dirname(dirname(dirname(__DIR__))) . '/node_modules/.bin/sass',
    dirname(dirname(dirname(dirname(__DIR__)))) . '/node_modules/.bin/sass',
    __DIR__ . '/../../../../node_modules/.bin/sass',
    
    // Specific to pi_website_take5
    dirname(dirname(dirname(__DIR__))) . '/pi_website_take5/node_modules/.bin/sass',
    str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']) . '/pi_website_take5/node_modules/.bin/sass'
];

echo "<p>Checking for SASS in these locations:</p>";
echo "<ul>";
foreach ($sassLocations as $sassPath) {
    echo "<li>" . htmlspecialchars($sassPath) . "</li>";
}
echo "</ul>";

// Try each SASS location
$sassBinary = null;
$sassVersion = null;
$sassType = null;

foreach ($sassLocations as $sassPath) {
    // Special handling for PowerShell scripts (.ps1)
    if (substr($sassPath, -4) === '.ps1') {
        $command = "powershell.exe -Command \"& '" . str_replace('\\', '/', $sassPath) . "' --version\"";
    } else {
        $command = escapeshellcmd($sassPath) . ' --version 2>&1';
    }
    
    echo "<p>Trying command: <code>" . htmlspecialchars($command) . "</code></p>";
    
    $output = [];
    $returnVar = 0;
    exec($command, $output, $returnVar);
    
    if ($returnVar === 0) {
        echo "<p style='color: green;'>✓ FOUND! SASS compiler found at: $sassPath</p>";
        echo "<p>Version: " . implode("<br>", $output) . "</p>";
        $sassInstalled = true;
        $sassBinary = $sassPath;
        $sassVersion = implode(", ", $output);
        $sassType = (substr($sassPath, -4) === '.ps1') ? 'ps1' : 'direct';
        break;
    } else {
        echo "<p style='color: #ff6600;'>Not found at this location. Error code: $returnVar</p>";
        echo "<p><small>Output: " . (empty($output) ? "None" : implode("<br>", $output)) . "</small></p>";
    }
}

// Try Windows PowerShell fallback if no Sass found
if (!$sassInstalled) {
    echo "<h3>Trying Windows PowerShell fallback</h3>";
    $psCommand = "powershell.exe -Command \"sass --version\"";
    $output = [];
    $returnVar = 0;
    
    echo "<p>Trying command: <code>" . htmlspecialchars($psCommand) . "</code></p>";
    exec($psCommand, $output, $returnVar);
    
    if ($returnVar === 0) {
        echo "<p style='color: green;'>✓ FOUND! SASS compiler available through PowerShell</p>";
        echo "<p>Version: " . implode("<br>", $output) . "</p>";
        $sassInstalled = true;
        $sassBinary = "sass";
        $sassVersion = implode(", ", $output);
        $sassType = 'powershell';
    } else {
        echo "<p style='color: red;'>PowerShell fallback failed. Error code: $returnVar</p>";
        echo "<p><small>Output: " . (empty($output) ? "None" : implode("<br>", $output)) . "</small></p>";
    }
}

// Try Windows CMD fallback if PowerShell fails
if (!$sassInstalled) {
    echo "<h3>Trying Windows CMD fallback</h3>";
    $cmdCommand = "cmd.exe /c sass --version";
    $output = [];
    $returnVar = 0;
    
    echo "<p>Trying command: <code>" . htmlspecialchars($cmdCommand) . "</code></p>";
    exec($cmdCommand, $output, $returnVar);
    
    if ($returnVar === 0) {
        echo "<p style='color: green;'>✓ FOUND! SASS compiler available through CMD</p>";
        echo "<p>Version: " . implode("<br>", $output) . "</p>";
        $sassInstalled = true;
        $sassBinary = "sass";
        $sassVersion = implode(", ", $output);
        $sassType = 'cmd';
    } else {
        echo "<p style='color: red;'>CMD fallback failed. Error code: $returnVar</p>";
        echo "<p><small>Output: " . (empty($output) ? "None" : implode("<br>", $output)) . "</small></p>";
    }
}

if (!$sassInstalled) {
    echo "<p style='color: red;'>✗ SASS compiler not found in any of the checked locations</p>";
    echo "<p>To install SASS, run: <code>npm install -g sass</code> or <code>npm install sass --save-dev</code> in your project</p>";
    
    // Try to give additional information
    echo "<h3>Troubleshooting</h3>";
    echo "<p>If SASS is installed but not found, try these steps:</p>";
    echo "<ol>";
    echo "<li>Check where sass is installed by running <code>where sass</code> in your terminal</li>";
    echo "<li>Add that path to the sassLocations array in this file</li>";
    echo "<li>Make sure the web server has permission to execute sass</li>";
    echo "</ol>";
} else {
    echo "<p style='color: green;'>✓ Using SASS compiler: $sassBinary ($sassVersion)</p>";
    
    // Test running the compilation if SASS is installed
    echo "<h3>Test Compilation</h3>";
    
    $sourcePath = __DIR__ . '/../main.scss';
    $outputPath = $cssLocations[0];
    
    if (file_exists($sourcePath)) {
        echo "<p>Source SCSS exists: " . $sourcePath . "</p>";
        
        // Set up the command based on the detected type
        if ($sassType === 'ps1') {
            // Direct PS1 script - need to fix command format with proper arguments
            $command = sprintf(
                'powershell.exe -Command "& \'%s\' \'%s:%s\' --style=expanded"',
                str_replace('\\', '/', $sassBinary),
                str_replace('\\', '/', $sourcePath),
                str_replace('\\', '/', $outputPath)
            );
        } elseif ($sassType === 'powershell') {
            // Generic PowerShell
            $command = sprintf(
                'powershell.exe -Command "sass \'%s:%s\' --style=expanded"',
                str_replace('\\', '/', $sourcePath),
                str_replace('\\', '/', $outputPath)
            );
        } elseif ($sassType === 'cmd') {
            // CMD execution
            $command = sprintf(
                'cmd.exe /c sass "%s:%s" --style=expanded',
                $sourcePath,
                $outputPath
            );
        } else {
            // Direct execution
            $command = sprintf(
                '%s "%s:%s" --style=expanded',
                escapeshellarg($sassBinary),
                $sourcePath,
                $outputPath
            );
        }
        
        echo "<p>Running command: <code>" . htmlspecialchars($command) . "</code></p>";
        
        $output = [];
        $returnVar = 0;
        exec($command, $output, $returnVar);
        
        if ($returnVar === 0) {
            echo "<p style='color: green;'>✓ Compilation successful!</p>";
            echo "<pre>" . implode("\n", $output) . "</pre>";
            
            // Check if the file was created
            if (file_exists($outputPath)) {
                echo "<p style='color: green;'>✓ CSS file created: " . $outputPath . " (" . filesize($outputPath) . " bytes)</p>";
                
                // Show first few lines
                $cssContent = file_get_contents($outputPath);
                $firstLines = implode("\n", array_slice(explode("\n", $cssContent), 0, 10));
                echo "<p>First few lines:</p>";
                echo "<pre style='background: #f5f5f5; padding: 10px; overflow: auto;'>" . htmlspecialchars($firstLines) . "...</pre>";
                
                echo "<p>You can now run the BEM compiler to generate all CSS files:</p>";
                echo "<pre>php " . __DIR__ . "/compile-bem.php</pre>";
            } else {
                echo "<p style='color: red;'>✗ CSS file was not created</p>";
                echo "<p>Check permissions for the directory: " . dirname($outputPath) . "</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Compilation failed!</p>";
            echo "<pre>" . implode("\n", $output) . "</pre>";
            
            echo "<h4>Additional Troubleshooting</h4>";
            echo "<p>Try running the sass command manually in your terminal:</p>";
            
            if ($sassType === 'ps1') {
                echo "<pre>powershell.exe -Command \"& '" . str_replace('\\', '/', $sassBinary) . "' '" . $sourcePath . ":" . $outputPath . "' --style=expanded\"</pre>";
            } elseif ($sassType === 'powershell') {
                echo "<pre>powershell.exe -Command \"sass '" . $sourcePath . ":" . $outputPath . "' --style=expanded\"</pre>";
            } elseif ($sassType === 'cmd') {
                echo "<pre>cmd.exe /c sass \"" . $sourcePath . ":" . $outputPath . "\" --style=expanded</pre>";
            } else {
                echo "<pre>" . $sassBinary . " \"" . $sourcePath . ":" . $outputPath . "\" --style=expanded</pre>";
            }
        }
    } else {
        echo "<p style='color: red;'>✗ Source SCSS file not found: " . $sourcePath . "</p>";
        echo "<p>Make sure the SCSS file exists and is readable.</p>";
    }
}

// Check all CSS link tags in index.php to find actual paths
echo "<h2>CSS Links in Index Page</h2>";

$indexPath = $_SERVER['DOCUMENT_ROOT'] . '/index.php';
if (file_exists($indexPath)) {
    $indexContent = file_get_contents($indexPath);
    preg_match_all('/<link[^>]+rel=[\'"]stylesheet[\'"][^>]+href=[\'"]([^\'"]+)[\'"][^>]*>/', $indexContent, $matches);
    
    if (isset($matches[1]) && count($matches[1]) > 0) {
        echo "<p>Found " . count($matches[1]) . " CSS link(s) in index.php:</p>";
        echo "<ul>";
        foreach ($matches[1] as $cssLink) {
            echo "<li>" . htmlspecialchars($cssLink) . " ";
            
            // Check if file exists
            $fullPath = $_SERVER['DOCUMENT_ROOT'] . $cssLink;
            if (file_exists($fullPath)) {
                echo "<span style='color:green'>(File exists)</span>";
            } else {
                echo "<span style='color:red'>(File NOT found)</span>";
            }
            
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No CSS link tags found in index.php</p>";
    }
} else {
    echo "<p>index.php not found at: " . $indexPath . "</p>";
    
    // Try alternative locations
    $alternateLocations = [
        dirname($_SERVER['DOCUMENT_ROOT']) . '/index.php',
        $_SERVER['DOCUMENT_ROOT'] . '/pi_website_take5/index.php'
    ];
    
    foreach ($alternateLocations as $altPath) {
        if (file_exists($altPath)) {
            echo "<p>Found index file at alternate location: " . $altPath . "</p>";
            $indexContent = file_get_contents($altPath);
            preg_match_all('/<link[^>]+rel=[\'"]stylesheet[\'"][^>]+href=[\'"]([^\'"]+)[\'"][^>]*>/', $indexContent, $matches);
            
            if (isset($matches[1]) && count($matches[1]) > 0) {
                echo "<ul>";
                foreach ($matches[1] as $cssLink) {
                    echo "<li>" . htmlspecialchars($cssLink) . "</li>";
                }
                echo "</ul>";
            }
            break;
        }
    }
}

// Check URLs for testing
echo "<h2>URL Configuration</h2>";

$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$scriptDir = dirname($_SERVER['PHP_SELF']);

$styleTestUrl = $baseUrl . $scriptDir . "/style-tester.php?page=home";
$styleDiffUrl = $baseUrl . $scriptDir . "/style-visual-diff.php?page=home";

echo "<p>Style Tester URL: <a href='$styleTestUrl' target='_blank'>$styleTestUrl</a></p>";
echo "<p>Style Diff URL: <a href='$styleDiffUrl' target='_blank'>$styleDiffUrl</a></p>";

// Check availability of style-tester.php and style-visual-diff.php files
if (file_exists(__DIR__ . "/style-tester.php")) {
    echo "<p style='color: green;'>✓ style-tester.php file exists</p>";
} else {
    echo "<p style='color: red;'>✗ style-tester.php file not found</p>";
}

if (file_exists(__DIR__ . "/style-visual-diff.php")) {
    echo "<p style='color: green;'>✓ style-visual-diff.php file exists</p>";
} else {
    echo "<p style='color: red;'>✗ style-visual-diff.php file not found</p>";
}

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Verify that the CSS files exist in the proper directories</li>";
echo "<li>If SASS compilation was successful, proceed to testing</li>";
echo "<li>Try accessing the Style Tester URL above</li>";
echo "<li>If the styling is still not loading, try adjusting the CSS paths in the style-tester.php file</li>";
echo "</ol>";

echo "<div style='margin-top: 20px; padding: 10px; background: #eeffee; border: 1px solid #aaffaa;'>";
echo "<h3>Troubleshooting Tip</h3>";
echo "<p>If the styles still don't load, use browser developer tools (F12) to check:</p>";
echo "<ol>";
echo "<li>Whether the CSS files are being found (404 errors in the Network tab)</li>";
echo "<li>What CSS paths are actually being used in the HTML (check the href attributes of link tags)</li>";
echo "<li>If there are any JavaScript errors preventing proper page loading</li>";
echo "</ol>";
echo "</div>";
?> 