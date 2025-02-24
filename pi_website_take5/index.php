<?php
require_once 'config.php';
require_once 'includes/functions.php';
require_once 'includes/language.php';
require_once 'includes/cache.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$lang = isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'he']) ? $_GET['lang'] : 'en';

// Set the language
setLanguage($lang);

// Generate cache key
$cacheKey = $lang . '_' . $page . '_' . (isset($_GET['id']) ? $_GET['id'] : '');

// Try to get cached content
$cachedContent = getCache($cacheKey);

if ($cachedContent === false) {
    // Start output buffering
    ob_start();

    // Load the header
    include 'components/header.php';

    // Load the page content
    if (file_exists("pages/{$page}.php")) {
        include "pages/{$page}.php";
    } else {
        include 'pages/404.php';
    }

    // Load the footer
    include 'components/footer.php';

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

