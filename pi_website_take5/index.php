<?php
// Configure error reporting based on debug mode
if (defined('PHP_DEBUG_MODE') && PHP_DEBUG_MODE === true) {
    // Debug mode: Show all errors
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    // Production mode: Hide errors
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
    
    // Log errors instead of displaying them
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/src/logs/php_errors.log');
}

require_once 'src/config/config.php';
require_once 'src/utility/functions.php';
require_once 'src/utility/language.php';
require_once 'src/utility/cache.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Validate language from URL against available languages, or use default
$urlLang = isset($_GET['lang']) ? $_GET['lang'] : '';
$lang = in_array($urlLang, AVAILABLE_LANGUAGES) ? $urlLang : DEFAULT_LANG;

// Validate that default language exists in available languages
if (!in_array(DEFAULT_LANG, AVAILABLE_LANGUAGES) && count(AVAILABLE_LANGUAGES) > 0) {
    // If default is not in available languages, use the first available language
    $lang = AVAILABLE_LANGUAGES[0];
}

// Handle AJAX requests for process-contact
if ($page === 'process-contact' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    // Include the process-contact file directly
    include 'src/pages/process-contact.php';
    exit;
}

// Validate the page against whitelist
if (!isPageAllowed($page)) {
    // Redirect to 404 page if not allowed
    redirectToPage('404', ['original' => $page], $lang);
}

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

