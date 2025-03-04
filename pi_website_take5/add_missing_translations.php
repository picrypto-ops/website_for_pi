<?php
/**
 * Translation Update Utility
 * 
 * This script adds missing translations to general.json
 */

// Add error handling to help debug include issues
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Display working directory info
echo "<h1>Translation Update Utility</h1>";
echo "<p>Current working directory: " . getcwd() . "</p>";
echo "<p>Script directory: " . __DIR__ . "</p>";

try {
    // Include necessary files
    require_once 'config.php';
    echo "<p>Successfully included config.php</p>";
    
    require_once 'includes/classes/translation/TranslatableInterface.php';
    echo "<p>Successfully included TranslatableInterface.php</p>";
    
    require_once 'includes/classes/translation/AbstractTranslatable.php';
    echo "<p>Successfully included AbstractTranslatable.php</p>";
    
    require_once 'includes/classes/translation/GeneralTranslatable.php';
    echo "<p>Successfully included GeneralTranslatable.php</p>";
    
    require_once 'includes/classes/translation/TranslatableFactory.php';
    echo "<p>Successfully included TranslatableFactory.php</p>";

    // Check if we can access the general.json file
    $generalJsonPath = 'data/general.json';
    if (file_exists($generalJsonPath)) {
        echo "<p>general.json exists at: {$generalJsonPath}</p>";
    } else {
        echo "<p style='color: red;'>WARNING: general.json not found at: {$generalJsonPath}</p>";
    }

    // Initialize factory
    TranslatableFactory::initialize(['general']);
    echo "<p>TranslatableFactory initialized</p>";

    // Get current general.json data
    $generalData = TranslatableFactory::loadData('general');
    echo "<p>Loaded existing general.json data</p>";

    // List of missing keys with their default values
    $missingTranslations = [
        // From pages/home.php
        'learn_more2' => 'Learn More',
        
        // From team_section.php
        'our_team' => 'Our Team',
        'team_description' => 'Meet our dedicated professionals who provide exceptional service.',
        'meet_our_team' => 'Meet Our Team',
        
        // From contact_section.php
        'address' => 'Address',
        'phone' => 'Phone',
        'email' => 'Email',
        'website' => 'Website',
        'get_in_touch' => 'Get In Touch',
        
        // From pages/team-member.php
        'previous_member' => 'Previous Member',
        'next_member' => 'Next Member',
        
        // From pages/segment.php
        'no_products' => 'No products available for this segment.',
        
        // From pages/product.php
        'product_founders' => 'Founders',
        'product_team' => 'Product Team',
        
        // From pages/our-team.php
        'our_team_title' => 'Our Team',
        'our_team_description' => 'Meet the people who make it all happen.',
        'no_team_members' => 'No team members available.',
        
        // From components/footer.php
        'all_rights_reserved' => 'All rights reserved',
        
        // From products_section.php
        'featured_products' => 'Featured Products',
        'featured_products_description' => 'Discover our premium financial products designed to meet your needs.'
    ];

    // Check existing keys before adding missing ones
    echo "<h2>Current Keys in general.json:</h2>";
    echo "<ul>";
    $existingCount = 0;
    foreach ($generalData as $key => $data) {
        echo "<li>{$key}</li>";
        $existingCount++;
    }
    echo "</ul>";
    echo "<p>Total existing keys: {$existingCount}</p>";

    // Add missing translations to the data structure
    echo "<h2>Adding Missing Translations:</h2>";
    $addedCount = 0;
    foreach ($missingTranslations as $key => $defaultEnglishValue) {
        if (!isset($generalData[$key])) {
            $generalData[$key] = [
                'term_slug' => $key,
                'language_slug' => [
                    'en' => [
                        'label' => $defaultEnglishValue
                    ],
                    'he' => [
                        'label' => 'HEBREW_' . strtoupper($key) . '_NEEDED' // Placeholder for Hebrew
                    ]
                ]
            ];
            $addedCount++;
            echo "<p style='color: green;'>Added translation for: {$key}</p>";
        } else {
            echo "<p>Existing translation found for: {$key}</p>";
        }
    }

    // Save the updated data back to the file
    if ($addedCount > 0) {
        $jsonData = json_encode($generalData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $writeResult = file_put_contents('data/general.json', $jsonData);
        
        if ($writeResult !== false) {
            echo "<p style='color: green;'>Successfully added {$addedCount} new translations to general.json ({$writeResult} bytes written)</p>";
        } else {
            echo "<p style='color: red;'>ERROR: Failed to write to general.json</p>";
            echo "<p>Please check file permissions for 'data/general.json'</p>";
        }
    } else {
        echo "<p>No new translations were needed.</p>";
    }

    echo "<p>Update complete!</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>ERROR: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
} 