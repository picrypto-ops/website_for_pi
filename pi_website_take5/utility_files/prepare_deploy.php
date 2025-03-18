<?php
/**
 * Deployment Preparation Script
 * =============================
 * 
 * This script helps prepare the site for deployment by:
 * 1. Disabling debug files
 * 2. Ensuring error reporting is turned off
 * 3. Verifying security measures are in place
 */

echo "=== Deployment Preparation Script ===\n\n";

// Check if running in CLI
if (php_sapi_name() !== 'cli') {
    die("This script should only be run from the command line.\n");
}

// Files to check/rename
$debugFiles = [
    'debug.php',
    'test.php',
    'data_debug.php',
    'style-visual-diff.php',
    'style-tester.php'
];

echo "Checking for debug files...\n";
foreach ($debugFiles as $file) {
    if (file_exists($file)) {
        // Rename debug files by adding .disabled extension
        rename($file, $file . '.disabled');
        echo "✓ Disabled {$file}\n";
    } else if (file_exists($file . '.disabled')) {
        echo "✓ {$file} is already disabled\n";
    } else {
        echo "! {$file} not found\n";
    }
}

// Check index.php for error reporting
echo "\nChecking error reporting settings...\n";
$indexContent = file_get_contents('index.php');
if (strpos($indexContent, "ini_set('display_errors', 1)") !== false) {
    echo "! Warning: Error reporting is enabled in index.php\n";
    echo "  Please modify index.php to disable error reporting\n";
} else {
    echo "✓ Error reporting is disabled in index.php\n";
}

// Check .htaccess for security headers
echo "\nChecking security headers in .htaccess...\n";
$htaccessContent = file_get_contents('.htaccess');

$securityChecks = [
    'Content-Security-Policy' => strpos($htaccessContent, "Header set Content-Security-Policy") !== false && 
                                strpos($htaccessContent, "# Header set Content-Security-Policy") === false,
    'X-Frame-Options' => strpos($htaccessContent, "Header set X-Frame-Options") !== false,
    'X-Content-Type-Options' => strpos($htaccessContent, "Header set X-Content-Type-Options") !== false,
    'X-XSS-Protection' => strpos($htaccessContent, "Header set X-XSS-Protection") !== false,
    'Referrer-Policy' => strpos($htaccessContent, "Header set Referrer-Policy") !== false
];

foreach ($securityChecks as $header => $enabled) {
    echo $enabled ? "✓ {$header} is enabled\n" : "! {$header} is not enabled\n";
}

// Check for CSRF implementation
echo "\nChecking CSRF protection...\n";
if (file_exists('src/utility/csrf.php')) {
    echo "✓ CSRF protection file exists\n";
} else {
    echo "! CSRF protection is missing\n";
}

// Final instructions
echo "\nDeployment preparation complete!\n";
echo "Please address any warnings before deploying to production.\n";
echo "Remember to run your asset build process with: npm run build\n";