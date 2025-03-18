<?php
/**
 * BEM SCSS Compiler
 * 
 * This script compiles the BEM-based SCSS files into a CSS file
 * that can be used for testing.
 * 
 * Requirements:
 * - PHP must have exec() enabled
 * - SASS/SCSS compiler must be installed (e.g., via npm: npm install -g sass)
 * 
 * Usage:
 * 1. Run this script: php compile-bem.php
 * 2. The compiled CSS will be output to various potential locations to ensure it's found
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load configuration
$config = require_once __DIR__ . '/bem-src/config/src/config/src/config/config.php';

// Helper functions
function ensureDirectoryExists($path) {
    if (!file_exists($path)) {
        if (!mkdir($path, 0755, true)) {
            echo "Error: Failed to create directory: $path\n";
            return false;
        }
        echo "Created directory: $path\n";
    }
    return true;
}

/**
 * Finds the SASS compiler based on configuration
 * 
 * @return array|false Compiler information or false if not found
 */
function findSassCompiler($config) {
    // If auto-detection is disabled, use the configured path
    if (!$config['scss']['compiler']['auto_detect']) {
        $sassPath = $config['scss']['compiler']['path'];
        
        // Check if the path exists and is executable
        if (file_exists($sassPath)) {
            // Special handling for PowerShell scripts (.ps1)
            if (substr($sassPath, -4) === '.ps1') {
                $command = "powershell.exe -Command \"& '" . str_replace('\\', '/', $sassPath) . "' --version\"";
                echo "Using configured SASS path: $sassPath\n";
                
                $output = [];
                $returnVar = -1;
                exec($command, $output, $returnVar);
                
                if ($returnVar === 0) {
                    echo "✓ SASS compiler verified: $sassPath\n";
                    echo "Version: " . implode("\n", $output) . "\n\n";
                    return ["type" => "ps1", "path" => $sassPath];
                } else {
                    echo "✗ Configured SASS compiler failed verification\n";
                }
            } else {
                // Regular executable
                $command = escapeshellcmd($sassPath) . " --version";
                echo "Using configured SASS path: $sassPath\n";
                
                $output = [];
                $returnVar = -1;
                exec($command, $output, $returnVar);
                
                if ($returnVar === 0) {
                    echo "✓ SASS compiler verified: $sassPath\n";
                    echo "Version: " . implode("\n", $output) . "\n\n";
                    return ["type" => "direct", "path" => $sassPath];
                } else {
                    echo "✗ Configured SASS compiler failed verification\n";
                }
            }
        } else {
            echo "✗ Configured SASS compiler not found: $sassPath\n";
        }
    }
    
    // If we reach here, either auto-detection is enabled or the configured path failed
    echo "Attempting to auto-detect SASS compiler...\n\n";
    
    // List of possible locations
    $possibleLocations = [
        // Specific path from user's system
        'C:\\Users\\RonenBitman\\AppData\\Roaming\\npm\\sass.ps1',
        
        // Global command
        'sass',
        
        // Windows-specific locations
        getenv('APPDATA') . '\\npm\\sass.cmd',
        getenv('APPDATA') . '\\npm\\sass',
        getenv('APPDATA') . '\\npm\\sass.ps1',
        getenv('USERPROFILE') . '\\AppData\\Roaming\\npm\\sass.cmd',
        getenv('USERPROFILE') . '\\AppData\\Roaming\\npm\\sass',
        getenv('USERPROFILE') . '\\AppData\\Roaming\\npm\\sass.ps1',
        
        // Project-specific locations
        dirname(dirname(dirname(__DIR__))) . '/node_modules/.bin/sass',
        dirname(dirname(dirname(dirname(__DIR__)))) . '/node_modules/.bin/sass',
        __DIR__ . '/../../../../node_modules/.bin/sass',
    ];
    
    echo "Checking for SASS in these locations:\n\n";
    
    foreach ($possibleLocations as $sassPath) {
        echo $sassPath . "\n";
        
        // Special handling for PowerShell scripts (.ps1)
        if (substr($sassPath, -4) === '.ps1') {
            $command = "powershell.exe -Command \"& '" . str_replace('\\', '/', $sassPath) . "' --version\"";
        } else {
            $command = escapeshellcmd($sassPath) . " --version 2>&1";
        }
        
        $output = [];
        $returnVar = -1;
        
        echo "Trying command: $command\n\n";
        exec($command, $output, $returnVar);
        
        if ($returnVar === 0) {
            echo "✓ FOUND! SASS compiler found at: $sassPath\n";
            echo "Version: " . implode("\n", $output) . "\n\n";
            
            // Return the path with the appropriate execution method
            if (substr($sassPath, -4) === '.ps1') {
                return ["type" => "ps1", "path" => $sassPath];
            } else {
                return ["type" => "direct", "path" => $sassPath];
            }
        } else {
            echo "Not found at this location. Error code: $returnVar\n\n";
        }
    }
    
    // Windows PowerShell fallback if direct exec fails
    echo "Trying Windows PowerShell fallback...\n";
    $psCommand = "powershell.exe -Command \"sass --version\"";
    $output = [];
    $returnVar = -1;
    
    exec($psCommand, $output, $returnVar);
    
    if ($returnVar === 0) {
        echo "✓ FOUND! SASS compiler available through PowerShell\n";
        echo "Version: " . implode("\n", $output) . "\n\n";
        return ["type" => "powershell", "path" => "sass"];
    } else {
        echo "PowerShell fallback failed. Error code: $returnVar\n\n";
    }
    
    // Windows CMD fallback if PowerShell fails
    echo "Trying Windows CMD fallback...\n";
    $cmdCommand = "cmd.exe /c sass --version";
    $output = [];
    $returnVar = -1;
    
    exec($cmdCommand, $output, $returnVar);
    
    if ($returnVar === 0) {
        echo "✓ FOUND! SASS compiler available through CMD\n";
        echo "Version: " . implode("\n", $output) . "\n\n";
        return ["type" => "cmd", "path" => "sass"];
    } else {
        echo "CMD fallback failed. Error code: $returnVar\n\n";
    }
    
    // If we reach here, we couldn't find SASS
    echo "✗ SASS compiler not found in any of the checked locations\n\n";
    echo "To install SASS, run: npm install -g sass or npm install sass --save-dev in your project\n\n";
    return false;
}

/**
 * Runs the compilation command
 */
function compileSass($sassCompiler, $source, $output, $additionalOutputs = []) {
    echo "Compiling $source to $output\n";
    
    // Ensure output directory exists
    ensureDirectoryExists(dirname($output));
    
    // Set up the command based on the type
    if ($sassCompiler['type'] === 'ps1') {
        // Direct PS1 script - with correct format using colon between input:output
        $command = sprintf(
            'powershell.exe -Command "& \'%s\' \'%s:%s\' --style=expanded"',
            str_replace('\\', '/', $sassCompiler['path']),
            str_replace('\\', '/', $source),
            str_replace('\\', '/', $output)
        );
    } elseif ($sassCompiler['type'] === 'powershell') {
        // Generic PowerShell
        $command = sprintf(
            'powershell.exe -Command "sass \'%s:%s\' --style=expanded"',
            str_replace('\\', '/', $source),
            str_replace('\\', '/', $output)
        );
    } elseif ($sassCompiler['type'] === 'cmd') {
        // CMD execution
        $command = sprintf(
            'cmd.exe /c sass "%s:%s" --style=expanded',
            $source,
            $output
        );
    } else {
        // Direct execution
        $command = sprintf(
            '%s "%s:%s" --style=expanded',
            escapeshellarg($sassCompiler['path']),
            $source,
            $output
        );
    }
    
    echo "Running command: $command\n";
    $cmdOutput = [];
    $returnVar = -1;
    
    exec($command, $cmdOutput, $returnVar);
    
    if ($returnVar === 0) {
        echo "✓ Compilation successful!\n";
        
        // Verify the file exists
        if (file_exists($output)) {
            echo "File created: $output (" . filesize($output) . " bytes)\n";
            
            // Copy to additional output locations if specified
            if (!empty($additionalOutputs)) {
                foreach ($additionalOutputs as $additionalOutput) {
                    echo "Copying to additional location: $additionalOutput\n";
                    ensureDirectoryExists(dirname($additionalOutput));
                    
                    if (copy($output, $additionalOutput)) {
                        echo "✓ Successfully copied to $additionalOutput\n";
                    } else {
                        echo "✗ Failed to copy to $additionalOutput\n";
                    }
                }
            }
            
            return true;
        } else {
            echo "✗ Warning: Output file not found after compilation: $output\n";
            return false;
        }
    } else {
        echo "✗ Compilation failed! Error code: $returnVar\n";
        echo "Output: " . implode("\n", $cmdOutput) . "\n";
        
        // Additional troubleshooting help
        echo "\nTry running manually:\n";
        if ($sassCompiler['type'] === 'ps1') {
            echo "powershell.exe -Command \"& '" . str_replace('\\', '/', $sassCompiler['path']) . 
                "' '" . $source . ":" . $output . "' --style=expanded\"\n";
        }
        
        return false;
    }
}

/**
 * Creates a simple HTML test file
 */
function createTestHtmlFile($filePath, $title, $cssPath) {
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$title</title>
    <link rel="stylesheet" href="$cssPath">
</head>
<body>
    <h1>$title</h1>
    <p>This is a test page for comparing CSS styles.</p>
    
    <div class="container">
        <header class="site-header">
            <h2>Site Header</h2>
            <nav class="main-nav">
                <ul class="nav__list">
                    <li class="nav__item"><a href="#" class="nav__link">Home</a></li>
                    <li class="nav__item"><a href="#" class="nav__link">About</a></li>
                    <li class="nav__item"><a href="#" class="nav__link">Contact</a></li>
                </ul>
            </nav>
        </header>
        
        <section class="hero hero--home">
            <h2>Hero Section</h2>
            <p>This is the main hero section with some text content.</p>
            <button class="button button--primary">Call to Action</button>
        </section>
    </div>
</body>
</html>
HTML;

    echo "Creating test HTML file: $filePath\n";
    if (file_put_contents($filePath, $html)) {
        echo "✓ Test HTML file created successfully\n";
        return true;
    } else {
        echo "✗ Failed to create test HTML file\n";
        return false;
    }
}

/**
 * Creates a README file with usage instructions
 */
function createReadmeFile($dirPath) {
    $readmeContent = <<<MARKDOWN
# BEM Testing Files

These HTML files help test the BEM styling directly:

1. **original.html** - Uses the original CSS
2. **bem.html** - Uses the BEM CSS

## Testing Procedure

1. Open each file in a browser
2. Compare the visual appearance
3. They should look identical if the BEM implementation is correct
4. If there are differences, check the browser console for errors

## CSS Paths

If the styles aren't loading, check that the CSS paths are correct. You might need to adjust:

- The relative paths in the `<link>` tags
- Ensure the CSS files have been compiled successfully
- Check if file permissions allow reading the CSS files

---

For more comprehensive testing, use the style-tester.php and style-visual-diff.php tools.
MARKDOWN;

    file_put_contents($dirPath . '/README.md', $readmeContent);
}

// Main script execution
echo "BEM SCSS Compiler\n";
echo "================\n\n";

// Find SASS compiler
$sassCompiler = findSassCompiler($config);

if (!$sassCompiler) {
    echo "Cannot proceed without SASS compiler.\n";
    exit(1);
}

// Source path
$sourcePath = $config['scss']['source'];

// Set up CSS output paths
$outputs = [];

// Primary output (first in the 'bem' list)
$primaryOutput = $config['css']['bem'][0];
$outputs[] = $primaryOutput;

// Additional outputs (rest of the 'bem' list)
$additionalOutputs = array_slice($config['css']['bem'], 1);

// Compile SCSS to CSS
$success = compileSass($sassCompiler, $sourcePath, $primaryOutput, $additionalOutputs);

if ($success) {
    // Create test directory for HTML files
    $testDir = dirname($primaryOutput) . '/bem-test';
    ensureDirectoryExists($testDir);
    
    // Create test HTML files
    createTestHtmlFile(
        $testDir . '/original.html',
        'Original CSS Test',
        '../' . basename($config['css']['original'][0])
    );
    
    createTestHtmlFile(
        $testDir . '/bem.html',
        'BEM CSS Test',
        '../' . basename($config['css']['bem'][0])
    );
    
    createReadmeFile($testDir);
    
    echo "\nDone! BEM CSS files have been compiled successfully.\n";
    echo "Test HTML files created in $testDir\n";
    echo "You can now use the following tools:\n";
    echo "- style-tester.php: For comparing styles in the actual website\n";
    echo "- style-visual-diff.php: For side-by-side comparison\n";
    echo "- css-finder.php: To diagnose any CSS path issues\n";
} else {
    echo "\nCompilation failed. Please check the errors above.\n";
}
?> 