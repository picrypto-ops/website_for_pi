<?php
/**
 * PI Website Functions
 * 
 * This file contains various helper functions used throughout the website.
 * 
 * Key functionalities:
 * - Translation handling (via TranslatableFactory)
 * - UI/UX helper functions
 * - Input sanitization
 * - Language switching (renderLanguageSwitcher)
 * - Responsive image generation
 */

// Ensure all translation class files are included
require_once __DIR__ . '/classes/translation/TranslatableInterface.php';
require_once __DIR__ . '/classes/translation/AbstractTranslatable.php';
require_once __DIR__ . '/classes/translation/GeneralTranslatable.php';
require_once __DIR__ . '/classes/translation/PageTranslatable.php';
require_once __DIR__ . '/classes/translation/ProductTranslatable.php';
require_once __DIR__ . '/classes/translation/SegmentTranslatable.php';
require_once __DIR__ . '/classes/translation/TeamTranslatable.php';
require_once __DIR__ . '/classes/translation/TeamMemberEnhanced.php';
require_once __DIR__ . '/classes/translation/TranslatableFactory.php';

/**
 * Load JSON data from file with improved error handling
 * 
 * @param string $file The name of the JSON file without extension
 * @return array|null The decoded JSON data or empty array on error
 * @deprecated Use TranslatableFactory methods directly instead
 */
function loadJsonData($file) {
    return TranslatableFactory::loadData($file);
}


/**
 * Sanitize user input
 * 
 * @param string $input The input to sanitize
 * @return string Sanitized input
 */
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

/**
 * Check if a menu item is active
 *
 * @param string $currentPage Current page slug (e.g., 'home', 'about', 'segment', 'product')
 * @param string $menuItem Menu item slug to check (e.g., 'home', 'investment_banking', 'team')
 * @return string ' is-active' if current page matches menu item, empty string otherwise
 */
function isActiveMenu($currentPage, $menuItem) {
    $activeClass = ' is-active'; // Use the state helper class with a leading space
    $currentId = isset($_GET['id']) ? sanitizeInput($_GET['id']) : ''; // Sanitize input

    // Direct match (e.g., 'about' page matches 'about' menu item)
    if ($currentPage === $menuItem) {
        return $activeClass;
    }

    // Special case for team pages (page is 'our-team', menu item is 'team' or 'our-team')
    if ($currentPage === 'our-team' && ($menuItem === 'team' || $menuItem === 'our-team')) {
         return $activeClass;
    }
     // Special case for team member page, highlight "Our Team"
     if ($currentPage === 'team-member' && ($menuItem === 'team' || $menuItem === 'our-team')) {
        return $activeClass;
    }

    // Check if it's a segment page and the ID matches the menu item slug
    if ($currentPage === 'segment' && $currentId === $menuItem) {
        return $activeClass;
    }

    // Check if it's a product page and its segment matches the menu item slug
    // Assumes product URLs include ?segment=... parameter
    $currentSegment = isset($_GET['segment']) ? sanitizeInput($_GET['segment']) : '';
    if ($currentPage === 'product' && $currentSegment === $menuItem) {
        return $activeClass;
    }

    // No match found
    return '';
}

/**
 * Generate language switcher HTML
 * 
 * @param string $lang Current language (en or he)
 * @param array $additionalClasses Additional CSS classes for the language switcher container
 * @param bool $includeWrapper Whether to include the outer wrapper div (default true)
 * @return string HTML for the language switcher
 */
function renderLanguageSwitcher($lang, $additionalClasses = '', $includeWrapper = true) {
    // Check if multiple languages are available, if not return empty string
    if (count(AVAILABLE_LANGUAGES) <= 1) {
        return '';
    }
    
    // Validate language
    $lang = in_array($lang, AVAILABLE_LANGUAGES) ? $lang : DEFAULT_LANG;
    
    // Get current URL parameters
    $currentParams = $_GET;
    
    // Build inner HTML for each available language
    $innerHtml = '';
    $separatorCount = 0;
    
    foreach (AVAILABLE_LANGUAGES as $langCode) {
        // Create language link with all current parameters except for lang
        $langParams = $currentParams;
        $langParams['lang'] = $langCode;
        $langLink = '?' . http_build_query($langParams);
        
        // Add separator if not the first language
        if ($separatorCount > 0) {
            $innerHtml .= '<span class="separator">|</span>';
        }
        
        // Add language switch link
        $langDisplay = $langCode === 'en' ? 'EN' : 'עב';
        $innerHtml .= '<a href="' . $langLink . '" class="lang-switch ' . ($lang === $langCode ? 'active' : '') . '" data-lang="' . $langCode . '">' . $langDisplay . '</a>';
        $separatorCount++;
    }
    
    // Return content with or without wrapper
    if ($includeWrapper) {
        // Additional classes for the container
        $classAttribute = 'language-switcher';
        if (!empty($additionalClasses)) {
            $classAttribute .= ' ' . trim($additionalClasses);
        }
        
        return '<div class="' . $classAttribute . '">' . $innerHtml . '</div>';
    } else {
        return $innerHtml;
    }
}

// Add this function to your existing functions.php file

/**
 * Generate language switcher HTML with BEM classes
 *
 * @param string $lang Current language (en or he)
 * @param string $additionalClasses Additional CSS classes for the main block (site-header__lang-switcher) - often not needed here
 * @param bool $includeWrapper Whether to include the outer block div (usually true when called from PHP)
 * @return string HTML for the language switcher
 */
function renderLanguageSwitcherBem($lang, $additionalClasses = '', $includeWrapper = true) {
    // Check if multiple languages are available, if not return empty string
    if (!defined('AVAILABLE_LANGUAGES') || count(AVAILABLE_LANGUAGES) <= 1) {
        return '';
    }

    // Validate language
    $currentLang = in_array($lang, AVAILABLE_LANGUAGES) ? $lang : (defined('DEFAULT_LANG') ? DEFAULT_LANG : 'en');

    // Get current URL parameters
    $currentParams = $_GET;

    // Build inner HTML for each available language
    $innerHtml = '';
    $separatorCount = 0;

    foreach (AVAILABLE_LANGUAGES as $langCode) {
        // Create language link with all current parameters except for lang
        $langParams = $currentParams;
        $langParams['lang'] = $langCode;
        // Prevent empty 'id' or other params from creating trailing '='
        $langParams = array_filter($langParams, function($value) { return $value !== '' && $value !== null; });
        $langLink = '?' . http_build_query($langParams);

        // Add separator if not the first language
        if ($separatorCount > 0) {
            // Add BEM class to separator
            $innerHtml .= '<span class="site-header__lang-separator">|</span>';
        }

        // Add language switch link with BEM class and state class
        $langDisplay = ($langCode === 'he') ? 'עב' : strtoupper($langCode); // Display HE as עב
        $activeClass = ($currentLang === $langCode) ? ' is-active' : ''; // Use state helper class
        // Add BEM class to link
        $innerHtml .= '<a href="' . htmlspecialchars($langLink) . '" class="site-header__lang-link' . $activeClass . '" data-lang="' . $langCode . '">' . $langDisplay . '</a>';
        $separatorCount++;
    }

    // Return content with or without wrapper block element
    // NOTE: The calling PHP in header.php already creates the wrapper div with the correct BEM class,
    // so typically we don't need the wrapper here. Setting $includeWrapper=false by default might be better.
    // Let's keep it matching the call in header.php for now ($includeWrapper=true).
    if ($includeWrapper) {
        $classAttribute = 'site-header__lang-switcher'; // This is the Block__Element class
        if (!empty($additionalClasses)) {
            $classAttribute .= ' ' . trim($additionalClasses);
        }
        // It's unusual to have the *same* BEM class on wrapper and inner content.
        // The PHP in header.php already creates <div class="site-header__lang-switcher">
        // So this function should probably just return the $innerHtml directly.
        // Let's adjust the call in header.php instead.

        return $innerHtml;

    } else {
         // This case might not be used if header.php handles the wrapper.
        return $innerHtml;
    }
}
/**
 * Generate responsive image HTML
 * 
 * @param string $src Image source
 * @param string $alt Alt text
 * @param string $class CSS class
 * @return string HTML for responsive image
 */
function responsiveImage($src, $alt, $class = '') {
    $webpSrc = ensureWebPVersion($src);
    $originalExt = pathinfo($src, PATHINFO_EXTENSION);
    
    $output = '<picture>';
    $output .= '<source srcset="' . $webpSrc . '" type="image/webp">';
    $output .= '<source srcset="' . $src . '" type="image/' . $originalExt . '">';
    $output .= '<img src="' . $src . '" alt="' . $alt . '" class="' . $class . '">';
    $output .= '</picture>';
    
    return $output;
}

/**
 * Ensure WebP version of image exists
 * 
 * @param string $src Original image source
 * @return string Path to WebP version
 */
function ensureWebPVersion($src) {
    $pathInfo = pathinfo($src);
    $webpPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
    
    // If WebP doesn't exist, try to create it (if GD is available)
    if (!file_exists($webpPath) && function_exists('imagewebp')) {
        $originalPath = $src;
        if (file_exists($originalPath)) {
            $image = null;
            switch (strtolower($pathInfo['extension'])) {
                case 'jpeg':
                case 'jpg':
                    $image = imagecreatefromjpeg($originalPath);
                    break;
                case 'png':
                    $image = imagecreatefrompng($originalPath);
                    break;
                case 'gif':
                    $image = imagecreatefromgif($originalPath);
                    break;
            }
            
            if ($image) {
                imagewebp($image, $webpPath, 80);
                imagedestroy($image);
            }
        }
    }
    
    return $webpPath;
}

/**
 * Get translated content from a data structure using the OO approach
 * 
 * @param array $data The data structure containing translations
 * @param string $lang The language code
 * @param string $key The content key
 * @param string $default Default value if no translation is found
 * @return string The translated content or default if not found
 */
function getTranslatedContent($data, $lang, $key, $default = '') {
    // First check if data is null or not an array to avoid errors
    if ($data === null || !is_array($data)) {
        if (TranslatableFactory::$debug) error_log("getTranslatedContent: Data is null or not an array");
        return $default;
    }
    
    // Debug data type
    if (TranslatableFactory::$debug) {
        $dataType = "unknown";
        if (isset($data['page_slug'])) $dataType = "page";
        elseif (isset($data['product_slug'])) $dataType = "product";
        elseif (isset($data['segment_slug'])) $dataType = "segment";
        elseif (isset($data['term_slug'])) $dataType = "general";
        elseif (isset($data['name_slug'])) $dataType = "team";
        error_log("getTranslatedContent: Detected data type: $dataType for key: $key");
    }
    
    // Use our factory to create the appropriate translatable object
    $translatable = TranslatableFactory::createFromData($data);
    
    // Get the content using the translatable object
    $result = $translatable->getContent($lang, $key, $default);
    
    if (TranslatableFactory::$debug) {
        error_log("getTranslatedContent: Result for lang: $lang, key: $key is: " . substr($result, 0, 50) . (strlen($result) > 50 ? "..." : ""));
    }
    
    return $result;
}

/**
 * Format content with HTML
 * 
 * This function ensures HTML content is properly displayed,
 * preserving all HTML tags (like <br>) and converting newlines to <br> tags
 * where appropriate.
 * 
 * @param string $content The content to format
 * @param bool $convertNewlines Whether to convert newlines to <br> tags
 * @return string Formatted content ready for display
 */
function formatHtmlContent($content, $convertNewlines = true) {
    if (empty($content)) {
        return '';
    }
    
    // Preserve HTML tags by not encoding them
    $formatted = $content;
    
    // Convert newlines to <br> tags if requested
    if ($convertNewlines) {
        // Replace newlines that aren't already preceded by a <br> tag
        $formatted = preg_replace('/(?<!\<br\>)\n/', '<br>', $formatted);
    }
    
    return $formatted;
}

/**
 * Generate the product logo path based on asset category and product slug
 * 
 * @param array $product The product data array containing 'asset_category' and 'product_slug'
 * @return string Path to the product logo
 */
function getProductLogoPath($product) {
    if (!isset($product['asset_category']) || !isset($product['product_slug'])) {
        return 'assets/images/products/pi-logo-icon.svg'; // Default fallback
    }
    
    $path = "assets/images/{$product['asset_category']}/{$product['product_slug']}/logo_large.svg";
    
    // Check if the file exists and return fallback if it doesn't
    if (!file_exists($path)) {
        return 'assets/images/products/pi-logo-icon.svg';
    }
    
    return $path;
}

/**
 * Generate a new CSRF token
 * 
 * @return string The generated token
 */
function generateCsrfToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Generate a new token
    $token = bin2hex(random_bytes(32));
    
    // Store the token and its timestamp in the session
    $_SESSION['csrf_token'] = $token;
    $_SESSION['csrf_token_time'] = time();
    
    return $token;
}

/**
 * Validate a CSRF token
 * 
 * @param string $token The token to validate
 * @param int $maxAge Maximum age of the token in seconds (default 1 hour)
 * @return bool True if the token is valid, false otherwise
 */
function validateCsrfToken($token, $maxAge = 3600) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if token exists in session
    if (empty($_SESSION['csrf_token']) || empty($_SESSION['csrf_token_time'])) {
        return false;
    }
    
    // Check if token has expired
    if (time() - $_SESSION['csrf_token_time'] > $maxAge) {
        // Token has expired, remove it
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
        return false;
    }
    
    // Compare tokens
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Create a CSRF token HTML input field
 * 
 * @return string HTML for a hidden input field with CSRF token
 */
function csrfTokenField() {
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Check if a page is in the whitelist of allowed pages
 * 
 * @param string $page The page name to check
 * @return bool True if the page is allowed, false otherwise
 */
function isPageAllowed($page) {
    // Load the whitelist
    $whitelist = require_once __DIR__ . '/../config/whitelist.php';
    
    // Check if the page exists in the whitelist
    if (isset($whitelist[$page])) {
        // For future: Check auth requirements if implemented
        $requiresAuth = $whitelist[$page];
        
        // If page requires authentication, implement auth check here
        // For now, we just check if it's in the whitelist
        return true;
    }
    
    return false;
}

/**
 * Safe redirect to a specified page
 * 
 * @param string $page The page to redirect to
 * @param array $params Additional URL parameters
 * @param string $lang The language code
 * @return void
 */
function redirectToPage($page, $params = [], $lang = null) {
    // If language isn't specified, use the current one or default
    if ($lang === null) {
        $lang = isset($_GET['lang']) ? $_GET['lang'] : DEFAULT_LANG;
    }
    
    // Ensure the language is valid from available languages
    if (!in_array($lang, AVAILABLE_LANGUAGES)) {
        $lang = DEFAULT_LANG;
        
        // If default isn't available either, use the first available language
        if (!in_array($lang, AVAILABLE_LANGUAGES) && count(AVAILABLE_LANGUAGES) > 0) {
            $lang = AVAILABLE_LANGUAGES[0];
        }
    }
    
    // Create the parameter array
    $redirectParams = array_merge([
        'page' => $page,
        'lang' => $lang
    ], $params);
    
    // Build the URL
    $url = '?' . http_build_query($redirectParams);
    
    // Perform the redirect
    header('Location: ' . $url);
    exit;
}

// todo: remove all the notused functions & why getTranslatedContent is used