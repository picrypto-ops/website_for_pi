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
    
    // Home helpers specific debug information
    echo "<h2>Home Sections Debug Information</h2>";
    
    // Products Debug
    echo "<h3>Products Debug</h3>";
    $productData = TranslatableFactory::getData('products');
    echo '<div class="debug-container" style="background:#f8f8f8;padding:10px;margin:10px;font-size:12px;">';
    echo '<p>Product data structure:</p>';
    echo '<pre>';
    print_r($productData); // Show full data structure instead of just keys
    echo '</pre>';
    echo '</div>';
    
    // Extract featured products
    echo "<h4>Featured Products Debug</h4>";
    $featuredProducts = [];
    $counter = 0;
    
    if (is_array($productData)) {
        foreach ($productData as $categorySlug => $categoryProducts) {
            if (!is_array($categoryProducts)) continue;
            
            foreach ($categoryProducts as $productSlug => $product) {
                if (!is_array($product)) continue;
                
                if (isset($product['is_featured']) && $product['is_featured'] === true) {
                    // Store both the product and its slug
                    $featuredProducts[] = [
                        'slug' => $productSlug,
                        'data' => $product,
                        'category' => $categorySlug
                    ];
                    $counter++;
                    
                    // Limit to 3 featured products
                    if ($counter >= 3) break;
                }
            }
            if ($counter >= 3) break;
        }
    }

    echo '<div class="debug-container" style="background:#f8f8f8;padding:10px;margin:10px;font-size:12px;">';
    echo '<p>Found ' . count($featuredProducts) . ' featured products:</p>';
    echo '<pre>';
    print_r($featuredProducts);
    echo '</pre>';
    echo '</div>';
    
    // Individual product translation debug
    echo "<h4>Product Translation Debug</h4>";
    if (!empty($featuredProducts)) {
        foreach ($featuredProducts as $productInfo) {
            $productSlug = $productInfo['slug'];
            $product = $productInfo['data'];
            
            // Create a proper translatable for this product
            $productTranslatable = TranslatableFactory::product($productSlug);
            
            echo '<div class="debug-container" style="background:#f8f8f8;padding:10px;margin:10px;font-size:12px;">';
            echo '<h4>Product Translation Debug: ' . $productSlug . '</h4>';
            echo '<p>Product data:</p>';
            echo '<pre>';
            print_r($product);
            echo '</pre>';
            echo '<p>Translated content for en:</p>';
            echo '<ul>';
            echo '<li>Name: ' . $productTranslatable->getContent('en', 'name') . '</li>';
            echo '<li>Slogan: ' . $productTranslatable->getContent('en', 'slogan') . '</li>';
            echo '</ul>';
            echo '</div>';
        }
    }
    
    // Team Debug
    echo "<h3>Team Debug</h3>";
    $teamData = TranslatableFactory::getData('team');
    echo '<div class="debug-container" style="background:#f8f8f8;padding:10px;margin:10px;font-size:12px;">';
    echo '<p>Team data structure:</p>';
    echo '<pre>';
    print_r(array_keys($teamData));
    echo '</pre>';
    echo '<p>Team members with display_in_home_page = true:</p>';
    $count = 0;
    foreach ($teamData as $groupSlug => $members) {
        if (!is_array($members)) continue;
        
        foreach ($members as $member) {
            if (isset($member['display_in_home_page']) && $member['display_in_home_page'] === true) {
                echo '<p>' . (isset($member['name_slug']) ? $member['name_slug'] : 'Unknown') . '</p>';
                $count++;
            }
        }
    }
    echo '<p>Total: ' . $count . ' featured members</p>';
    echo '</div>';
    
    // Contact Debug
    echo "<h3>Contact Debug</h3>";
    $pagesData = TranslatableFactory::getData('pages');
    $contactData = isset($pagesData['contact']) ? $pagesData['contact'] : [];
    echo '<div class="debug-container" style="background:#f8f8f8;padding:10px;margin:10px;font-size:12px;">';
    echo '<p>Contact data:</p>';
    echo '<pre>';
    print_r($contactData);
    echo '</pre>';
    echo '</div>';
    
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