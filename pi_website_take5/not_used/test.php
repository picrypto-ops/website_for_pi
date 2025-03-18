<?php
// Turn on error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include required files
require_once dirname(__DIR__) . '/src/utility/classes/translation/TranslatableInterface.php';
require_once dirname(__DIR__) . '/src/utility/classes/translation/AbstractTranslatable.php';
require_once dirname(__DIR__) . '/src/utility/classes/translation/GeneralTranslatable.php';
require_once dirname(__DIR__) . '/src/utility/classes/translation/PageTranslatable.php';
require_once dirname(__DIR__) . '/src/utility/classes/translation/ProductTranslatable.php';
require_once dirname(__DIR__) . '/src/utility/classes/translation/SegmentTranslatable.php';
require_once dirname(__DIR__) . '/src/utility/classes/translation/TeamTranslatable.php';
require_once dirname(__DIR__) . '/src/utility/classes/translation/TranslatableFactory.php';

// Load JSON data
function loadJsonData($file) {
    $jsonFile = dirname(__DIR__) . "/data/{$file}.json";
    // ... existing code ...
} 