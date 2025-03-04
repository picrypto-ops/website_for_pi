<?php
/**
 * Translation Check Utility
 * 
 * This script helps identify missing translations in the general.json file
 * by comparing keys used in the codebase against those defined in the JSON.
 */

// Add error handling to help debug include issues
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Display working directory info
echo "<h1>Translation Check Utility</h1>";
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
    TranslatableFactory::$debug = true; // Enable debug output
    echo "<p>TranslatableFactory initialized</p>";

    // List of keys used in the codebase
    $keysUsedInCode = [
    ];

    // Check which keys are missing
    echo "<h2>Keys Used in Code But Missing from general.json:</h2>";
    echo "<ul>";
    $missingKeys = 0;

    foreach ($keysUsedInCode as $key) {
        if (!TranslatableFactory::KeyExists(file:'general', $key)) {
            echo "<li><strong>{$key}</strong></li>";
            $missingKeys++;
        }
    }

    if ($missingKeys === 0) {
        echo "<li>No missing keys found!</li>";
    }

    echo "</ul>";

    // Check for values in general.json that might be empty
    echo "<h2>Keys with Empty Values in English:</h2>";
    echo "<ul>";
    $emptyValues = 0;

    $generalData = TranslatableFactory::loadData('general');
    foreach ($generalData as $key => $data) {
        if (isset($data['language_slug']) && 
            isset($data['language_slug']['en']) &&
            isset($data['language_slug']['en']['label']) &&
            empty($data['language_slug']['en']['label'])) {
            echo "<li><strong>{$key}</strong></li>";
            $emptyValues++;
        }
    }

    if ($emptyValues === 0) {
        echo "<li>No empty values found!</li>";
    }

    echo "</ul>";

    echo "<p>Check complete!</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>ERROR: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
} 