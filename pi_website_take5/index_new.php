<?php
// Turn on error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'src/config/config.php';
require_once 'src/utility/functions.php';
require_once 'src/utility/language.php';
require_once 'src/utility/cache.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$lang = isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'he']) ? $_GET['lang'] : 'en';

// Set the language
setLanguage($lang);

// Generate cache key
$cacheKey = $lang . '_' . $page . '_' . (isset($_GET['id']) ? $_GET['id'] : '') . '_new';

// Try to get cached content
$cachedContent = getCache($cacheKey);

if ($cachedContent === false) {
    // Start output buffering
    ob_start();

    // Load the header with new SCSS
    $useNewScss = true;
    include 'src/components/header.php';

    // Load the page content
    if (file_exists("src/pages/{$page}.php")) {
        include "src/pages/{$page}.php";
    } else {
        include 'src/pages/404.php';
    }

    // Load the footer
    include 'src/components/footer.php';

    // Get the buffered content
    $content = ob_get_clean();

    // Cache the content
    setCache($cacheKey, $content);

    // Output the content
    echo $content;
} else {
    // Output the cached content
    echo $cachedContent;
} 