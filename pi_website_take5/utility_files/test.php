<?php
// Turn on error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include required files
require_once 'src/utility/classes/translation/TranslatableInterface.php';
require_once 'src/utility/classes/translation/AbstractTranslatable.php';
require_once 'src/utility/classes/translation/GeneralTranslatable.php';
require_once 'src/utility/classes/translation/PageTranslatable.php';
require_once 'src/utility/classes/translation/ProductTranslatable.php';
require_once 'src/utility/classes/translation/SegmentTranslatable.php';
require_once 'src/utility/classes/translation/TeamTranslatable.php';
require_once 'src/utility/classes/translation/TranslatableFactory.php';

// Load JSON data
function loadJsonData($file) {
    $jsonFile = "data/{$file}.json";
    
    // Check if file exists
    if (!file_exists($jsonFile)) {
        echo "JSON file not found: {$jsonFile}<br>";
        return [];
    }
    
    // Get file contents
    $jsonContent = file_get_contents($jsonFile);
    if ($jsonContent === false) {
        echo "Could not read JSON file: {$jsonFile}<br>";
        return [];
    }
    
    // Decode JSON
    $data = json_decode($jsonContent, true);
    
    // Check for JSON decode errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "JSON decode error for file {$jsonFile}: " . json_last_error_msg() . "<br>";
        return [];
    }
    
    // Return data
    return $data;
}

// Simple test
echo "<h1>Testing translation system</h1>";

try {
    $generalData = loadJsonData('general');
    echo "<p>Loaded general data: " . (is_array($generalData) ? "Yes" : "No") . "</p>";
    
    if (is_array($generalData) && !empty($generalData)) {
        $firstKey = array_key_first($generalData);
        echo "<p>First key in general data: " . $firstKey . "</p>";
        
        $translatable = TranslatableFactory::create('general', $generalData[$firstKey]);
        echo "<p>Created translatable object: " . (($translatable instanceof TranslatableInterface) ? "Yes" : "No") . "</p>";
        
        if ($translatable instanceof TranslatableInterface) {
            echo "<p>Content for label: " . $translatable->getContent('en', 'label', 'Default Label') . "</p>";
        }
    }
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . " on line " . $e->getLine() . "</p>";
} 