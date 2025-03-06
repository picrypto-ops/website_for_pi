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
require_once 'includes/classes/translation/TeamMemberEnhanced.php';
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
    'TeamMemberEnhanced',
    'TranslatableFactory'
];

    // Debug function to print team member content
    function debugTeamMemberContent() {
        echo "<h3>Team Member Enhanced Debug</h3>";
        $teamData = TranslatableFactory::getData('team');
        
        if (!$teamData || !is_array($teamData)) {
            echo "<p>No team data available</p>";
            return;
        }
        
        echo "<div style='max-height: 400px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
        foreach ($teamData as $groupKey => $members) {
            if (!is_array($members)) continue;
            
            echo "<h4>Group: " . htmlspecialchars($groupKey) . "</h4>";
            
            foreach ($members as $member) {
                if (!is_array($member) || !isset($member['name_slug'])) continue;
                
                $memberTranslatable = TranslatableFactory::teamMemberEnhanced($member['name_slug']);
                echo "<pre>";
                print_r($memberTranslatable);
                echo "</pre>";
                echo "<div style='margin-bottom: 15px; padding: 10px; background: #f5f5f5;'>";
                echo "<h5>Member: " . htmlspecialchars($member['name_slug']) . "</h5>";
                
                // Display basic properties
                echo "<ul>";
                echo "<li>Name (EN): " . htmlspecialchars($memberTranslatable->getContent('en', 'name', 'N/A')) . "</li>";
                echo "<li>Position (EN): " . htmlspecialchars($memberTranslatable->getContent('en', 'position', 'N/A')) . "</li>";
                echo "<li>Bio (EN): " . substr(htmlspecialchars($memberTranslatable->getContent('en', 'bio', 'N/A')), 0, 100) . "...</li>";
                
                // Display product roles if available
                $displayProductRoles = $memberTranslatable->getContent('en', 'display_product_roles', false);
                $productRoles = $memberTranslatable->getContent('en', 'product_roles', []);
                
                if ($displayProductRoles && !empty($productRoles)) {
                    echo "<li>Product Roles: " . count($productRoles) . " roles found</li>";
                    echo "<ul style='margin-left: 20px;'>";
                    foreach ($productRoles as $role) {
                        echo "<li>";
                        echo "Role in multiple languages:";
                        echo "<ul style='margin-left: 20px;'>";
                        
                        if (isset($role['language_slug']) && is_array($role['language_slug'])) {
                            foreach ($role['language_slug'] as $langCode => $langData) {
                                echo "<li><strong>" . htmlspecialchars($langCode) . "</strong>: ";
                                
                                // Product name in this language
                                $productName = isset($role['product_data']['language_slug'][$langCode]['name']) 
                                    ? htmlspecialchars($role['product_data']['language_slug'][$langCode]['name']) 
                                    : 'N/A';
                                
                                // Role title in this language
                                $roleTitle = isset($langData['title']) 
                                    ? htmlspecialchars($langData['title']) 
                                    : 'N/A';
                                
                                echo "Product: <em>" . $productName . "</em>, ";
                                echo "Title: <em>" . $roleTitle . "</em>";
                                echo "</li>";
                            }
                        } else {
                            echo "<li>No language data available</li>";
                        }
                        
                        echo "</ul>";
                        echo "</li>";
                    }
                    echo "</ul>";
                }
                
                echo "</ul>";
                echo "</div>";
            }
        }
        echo "</div>";
    }

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

// Debug team member enhanced content
// debugTeamMemberContent();

// Add specific debug for Buky Katzman
echo "<h2>Detailed Debug for Buky Katzman</h2>";
function debugBukyKatzman() {
    $memberKey = 'buky-katzman';
    echo "<h3>Detailed TeamMemberEnhanced Data for '{$memberKey}'</h3>";
    
    echo "<div style='margin: 20px 0; padding: 15px; background-color: #ffffcc; border-left: 5px solid #ffcc00;'>";
    echo "<strong>🛠️ Implementation Note:</strong> The code has been updated to fix the product_name association in the position attribute. ";
    echo "Now comparing the results before and after the changes.</div>";
    
    // Get TeamMemberEnhanced instance
    $memberTranslatable = TranslatableFactory::teamMemberEnhanced($memberKey);
    
    // Get raw team member data
    $allTeam = TranslatableFactory::getData('team');
    $memberData = null;
    
    // Find the member data
    foreach ($allTeam as $groupSlug => $members) {
        if (!is_array($members)) continue;
        
        foreach ($members as $member) {
            if (isset($member['name_slug']) && $member['name_slug'] === $memberKey) {
                $memberData = $member;
                echo "<p>Found in group: <strong>{$groupSlug}</strong></p>";
                break 2;
            }
        }
    }
    
    // Get team_products data
    $teamProducts = TranslatableFactory::getData('teams_products');
    $memberRoles = [];
    
    if (isset($teamProducts['team_products']) && is_array($teamProducts['team_products'])) {
        foreach ($teamProducts['team_products'] as $role) {
            if (isset($role['name_slug']) && $role['name_slug'] === $memberKey) {
                $memberRoles[] = $role;
            }
        }
    }
    
    // Get products data
    $productsData = TranslatableFactory::getData('products');
    
    echo '<div class="debug-container" style="background:#f5f5f5;padding:15px;margin:15px;border-radius:5px;">';
    
    // Show member data
    echo '<h4>Raw Member Data from team.json:</h4>';
    echo '<pre style="background:#fff;padding:10px;border-radius:3px;max-height:300px;overflow:auto;">';
    print_r($memberData);
    echo '</pre>';
    
    // Show team_products data for this member
    echo '<h4>Raw Roles Data from teams_products.json:</h4>';
    echo '<pre style="background:#fff;padding:10px;border-radius:3px;max-height:300px;overflow:auto;">';
    print_r($memberRoles);
    echo '</pre>';
    
    // Show relevant products data
    echo '<h4>Available Products Data:</h4>';
    $relevantProducts = [];
    foreach ($memberRoles as $role) {
        if (!isset($role['product_slug']) || $role['product_slug'] === null) continue;
        $productSlug = $role['product_slug'];
        $segmentSlug = $role['segment_slug'] ?? null;
        
        if ($segmentSlug && isset($productsData[$segmentSlug][$productSlug])) {
            $relevantProducts[$productSlug] = $productsData[$segmentSlug][$productSlug];
        }
    }
    
    echo '<pre style="background:#fff;padding:10px;border-radius:3px;max-height:300px;overflow:auto;">';
    print_r($relevantProducts);
    echo '</pre>';
    
    // Display processed data as used in templates
    echo '<h4>English Content:</h4>';
    echo '<div style="background:#fff;padding:10px;border-radius:3px;margin-bottom:15px;">';
    
    // Basic info
    echo '<p><strong>Name:</strong> ' . $memberTranslatable->getContent('en', 'name', 'N/A') . '</p>';
    echo '<p><strong>display_product_roles:</strong> ' . ($memberTranslatable->getContent('en', 'display_product_roles', false) ? 'true' : 'false') . '</p>';
    
    // Get the position attribute
    $position = $memberTranslatable->getContent('en', 'position', 'N/A');
    
    echo '<p><strong>Position attribute output:</strong></p>';
    echo '<pre style="background:#f8f8f8;padding:10px;border-radius:3px;">';
    var_export($position);
    echo '</pre>';
    
    // Get the full content
    echo '<p><strong>Full content:</strong></p>';
    echo '<pre style="background:#f8f8f8;padding:10px;border-radius:3px;">';
    print_r($memberTranslatable->getAllContent('en'));
    echo '</pre>';
    
    echo '</div>';
    
    // Display Hebrew content
    echo '<h4>Hebrew Content:</h4>';
    echo '<div style="background:#fff;padding:10px;border-radius:3px;">';
    
    // Basic info
    echo '<p><strong>Name:</strong> ' . $memberTranslatable->getContent('he', 'name', 'N/A') . '</p>';
    echo '<p><strong>display_product_roles:</strong> ' . ($memberTranslatable->getContent('he', 'display_product_roles', false) ? 'true' : 'false') . '</p>';
    
    // Get the position attribute
    $position = $memberTranslatable->getContent('he', 'position', 'N/A');
    
    echo '<p><strong>Position attribute output:</strong></p>';
    echo '<pre style="background:#f8f8f8;padding:10px;border-radius:3px;">';
    var_export($position);
    echo '</pre>';
    
    // Get the full content
    echo '<p><strong>Full content:</strong></p>';
    echo '<pre style="background:#f8f8f8;padding:10px;border-radius:3px;">';
    print_r($memberTranslatable->getAllContent('he'));
    echo '</pre>';
    
    echo '</div>';
    
    echo '</div>';
}

debugBukyKatzman();

echo "<p>Error log path: " . ini_get('error_log') . "</p>";
echo "<p>This is where detailed debug logs from TranslatableFactory are being written.</p>"; 
