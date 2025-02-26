<?php
// Turn on error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Force output to browser
header('Content-Type: text/html; charset=utf-8');

// Show any potential errors directly in browser
function exception_error_handler($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
}
set_error_handler("exception_error_handler");

// Include required files
require_once 'includes/classes/translation/TranslatableInterface.php';
require_once 'includes/classes/translation/AbstractTranslatable.php';
require_once 'includes/classes/translation/GeneralTranslatable.php';
require_once 'includes/classes/translation/PageTranslatable.php';
require_once 'includes/classes/translation/ProductTranslatable.php';
require_once 'includes/classes/translation/SegmentTranslatable.php';
require_once 'includes/classes/translation/TeamTranslatable.php';
require_once 'includes/classes/translation/TranslatableFactory.php';

// Enable direct browser output for TranslatableFactory
TranslatableFactory::$debug = true;

echo "<h1>TranslatableFactory Debug Tool</h1>";
echo "<p>This page tests the TranslatableFactory and displays detailed debug information.</p>";

echo "<h2>PHP Version</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";

echo "<h2>Class Availability Check</h2>";
$classes = [
    'TranslatableInterface',
    'AbstractTranslatable',
    'GeneralTranslatable',
    'PageTranslatable',
    'ProductTranslatable',
    'SegmentTranslatable',
    'TeamTranslatable',
    'TranslatableFactory'
];

echo "<ul>";
foreach ($classes as $class) {
    echo "<li>" . $class . ": " . (class_exists($class) ? "<span style='color:green'>Available</span>" : "<span style='color:red'>Not Available</span>") . "</li>";
}
echo "</ul>";

echo "<h2>Testing New Transparent Data Loading</h2>";

try {
    echo "<h3>1. Initializing Factory</h3>";
    $startTime = microtime(true);
    TranslatableFactory::initialize(['general', 'pages']);
    $endTime = microtime(true);
    echo "<p>Time taken: " . number_format(($endTime - $startTime) * 1000, 2) . " ms</p>";
    
    echo "<h3>2. Testing Specialized Content Methods</h3>";
    
    // Testing general content
    echo "<h4>General Content:</h4>";
    $learnMore = TranslatableFactory::general()->getContent('en', 'learn_more');
    echo "<p>Content for 'learn_more' in English: " . htmlspecialchars($learnMore) . "</p>";
    
    // Testing specific page content
    echo "<h4>Page Content:</h4>";
    $homeTitle = TranslatableFactory::page('home')->getContent('en', 'title');
    echo "<p>Home page title: " . htmlspecialchars($homeTitle) . "</p>";
    
    // Test data refresh
    echo "<h3>3. Testing Data Refresh</h3>";
    echo "<p>Current home title: " . htmlspecialchars(TranslatableFactory::page('home')->getContent('en', 'title')) . "</p>";
    echo "<p>Requesting data refresh...</p>";
    
    // Force refresh
    TranslatableFactory::refreshData();
    
    // Get data again after refresh
    $homeTitleAfterRefresh = TranslatableFactory::page('home')->getContent('en', 'title');
    echo "<p>Home title after refresh: " . htmlspecialchars($homeTitleAfterRefresh) . "</p>";
    
    echo "<h3>4. Backwards Compatibility Test</h3>";
    $generalData = TranslatableFactory::getData('general');
    echo "<p>Successfully obtained general data: " . (is_array($generalData) ? count($generalData) . " items" : "No") . "</p>";
    
    // Still works with direct data access
    if (is_array($generalData) && !empty($generalData)) {
        $firstKey = array_key_first($generalData);
        $translatable = TranslatableFactory::create('general', $generalData[$firstKey]);
        echo "<p>First key: " . $firstKey . "</p>";
        echo "<p>Created translatable with direct data: " . (($translatable instanceof TranslatableInterface) ? "Yes" : "No") . "</p>";
    }
    
} catch (Exception $e) {
    echo "<div style='color:red; padding:10px; border:1px solid red; margin: 10px 0;'>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . " on line " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}

echo "<h2>PHP Error Log Path</h2>";
echo "<p>Error log path: " . ini_get('error_log') . "</p>";
echo "<p>This is where detailed debug logs from TranslatableFactory are being written.</p>"; 