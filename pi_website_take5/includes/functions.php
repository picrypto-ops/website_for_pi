<?php
// Ensure all translation class files are included
require_once __DIR__ . '/classes/translation/TranslatableInterface.php';
require_once __DIR__ . '/classes/translation/AbstractTranslatable.php';
require_once __DIR__ . '/classes/translation/GeneralTranslatable.php';
require_once __DIR__ . '/classes/translation/PageTranslatable.php';
require_once __DIR__ . '/classes/translation/ProductTranslatable.php';
require_once __DIR__ . '/classes/translation/SegmentTranslatable.php';
require_once __DIR__ . '/classes/translation/TeamTranslatable.php';
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
 * Get translation for a key
 * 
 * @param string $key The translation key
 * @return string The translated text or the key itself if not found
 */
function getTranslation($key) {
    global $translations;
    return isset($translations[$key]) ? $translations[$key] : $key;
}

/**
 * Generate breadcrumbs for navigation
 * 
 * @param string $page Current page
 * @return string HTML for breadcrumbs
 */
function generateBreadcrumbs($page) {
    global $lang;
    $generalData = loadJsonData('general');
    $breadcrumbs = '<div class="breadcrumbs">';
    
    // Home link
    $homeTranslatable = TranslatableFactory::create('general', $generalData['home']);
    $breadcrumbs .= '<a href="?page=home&lang=' . $lang . '">' . $homeTranslatable->getContent($lang, 'label') . '</a>';
    
    if ($page !== 'home') {
        $breadcrumbs .= ' &gt; ';
        if (isset($generalData[$page])) {
            $pageTranslatable = TranslatableFactory::create('general', $generalData[$page]);
            $breadcrumbs .= $pageTranslatable->getContent($lang, 'label');
        } else {
            // Handle subpages like product or segment
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                if ($page === 'segment') {
                    $segments = loadJsonData('segments');
                    if (isset($segments[$id])) {
                        $whatWeDoTranslatable = TranslatableFactory::create('general', $generalData['what_we_do']);
                        $segmentTranslatable = TranslatableFactory::create('segment', $segments[$id]);
                        
                        $breadcrumbs .= '<a href="?page=what-we-do&lang=' . $lang . '">' . 
                                      $whatWeDoTranslatable->getContent($lang, 'label') . '</a> &gt; ' .
                                      $segmentTranslatable->getContent($lang, 'name');
                    }
                } elseif ($page === 'product') {
                    $products = loadJsonData('products');
                    foreach ($products as $segmentSlug => $segmentProducts) {
                        if (isset($segmentProducts[$id])) {
                            $segments = loadJsonData('segments');
                            $whatWeDoTranslatable = TranslatableFactory::create('general', $generalData['what_we_do']);
                            $segmentTranslatable = TranslatableFactory::create('segment', $segments[$segmentSlug]);
                            $productTranslatable = TranslatableFactory::create('product', $segmentProducts[$id]);
                            
                            $breadcrumbs .= '<a href="?page=what-we-do&lang=' . $lang . '">' . 
                                          $whatWeDoTranslatable->getContent($lang, 'label') . '</a> &gt; ' .
                                          '<a href="?page=segment&id=' . $segmentSlug . '&lang=' . $lang . '">' .
                                          $segmentTranslatable->getContent($lang, 'name') . '</a> &gt; ' .
                                          $productTranslatable->getContent($lang, 'name');
                            break;
                        }
                    }
                } elseif ($page === 'team-member') {
                    $team = loadJsonData('team');
                    $found = false;
                    foreach ($team as $groupKey => $members) {
                        foreach ($members as $member) {
                            if (isset($member['name_slug']) && $member['name_slug'] === $id) {
                                $teamTranslatable = TranslatableFactory::create('general', $generalData['team']);
                                $memberTranslatable = TranslatableFactory::create('team', $member);
                                
                                $breadcrumbs .= '<a href="?page=our-team&lang=' . $lang . '">' . 
                                              $teamTranslatable->getContent($lang, 'label') . '</a> &gt; ' .
                                              $memberTranslatable->getContent($lang, 'name');
                                $found = true;
                                break;
                            }
                        }
                        if ($found) break;
                    }
                }
            } else {
                $breadcrumbs .= ucfirst(str_replace('-', ' ', $page));
            }
        }
    }
    
    $breadcrumbs .= '</div>';
    return $breadcrumbs;
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
 * @param string $currentPage Current page
 * @param string $menuItem Menu item to check
 * @return string 'active' if current page matches menu item, empty string otherwise
 */
function isActiveMenu($currentPage, $menuItem) {
    return $currentPage === $menuItem ? 'active' : '';
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
 * Get localized content from data structure
 * 
 * @param array $data Data array
 * @param string $lang Language code
 * @param string $key Content key
 * @return string Localized content or key if not found
 */
function getLocalizedContent($data, $lang, $key) {
    if (isset($data['language_slug'][$lang][$key])) {
        return $data['language_slug'][$lang][$key];
    } elseif (isset($data[$lang][$key])) {
        return $data[$lang][$key];
    } else {
        return $key;
    }
}

/**
 * Get translated content from a data structure using the new OO approach
 * 
 * @param array $data The data structure containing translations
 * @param string $lang The language code
 * @param string $key The content key
 * @return string The translated content or empty string if not found
 */
function getTranslatedContent($data, $lang, $key) {
    // First check if data is null or not an array to avoid errors
    if ($data === null || !is_array($data)) {
        if (TranslatableFactory::$debug) error_log("getTranslatedContent: Data is null or not an array");
        return '';
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
    
    // Use our new factory to create the appropriate translatable object
    $translatable = TranslatableFactory::createFromData($data);
    
    // Get the content using the translatable object
    $result = $translatable->getContent($lang, $key);
    
    if (TranslatableFactory::$debug) {
        error_log("getTranslatedContent: Result for lang: $lang, key: $key is: " . substr($result, 0, 50) . (strlen($result) > 50 ? "..." : ""));
    }
    
    return $result;
}

/**
 * Get content from a specific data structure type using OO approach
 * 
 * @param string $type Data type (segment, product, team, etc.)
 * @param string $id Item ID
 * @param string $lang Language code
 * @param string $key Content key
 * @return string|null Content or null if not found
 */
function getContentByType($type, $id, $lang, $key) {
    switch ($type) {
        case 'segment':
            $segments = loadJsonData('segments');
            if (isset($segments[$id])) {
                $translatable = TranslatableFactory::create('segment', $segments[$id]);
                return $translatable->getContent($lang, $key);
            }
            break;
            
        case 'product':
            $products = loadJsonData('products');
            foreach ($products as $segmentProducts) {
                if (isset($segmentProducts[$id])) {
                    $translatable = TranslatableFactory::create('product', $segmentProducts[$id]);
                    return $translatable->getContent($lang, $key);
                }
            }
            break;
            
        case 'team':
            $team = loadJsonData('team');
            foreach ($team as $group) {
                foreach ($group as $member) {
                    if (isset($member['name_slug']) && $member['name_slug'] === $id) {
                        $translatable = TranslatableFactory::create('team', $member);
                        return $translatable->getContent($lang, $key);
                    }
                }
            }
            break;
            
        case 'page':
            $pages = loadJsonData('pages');
            if (isset($pages[$id])) {
                $translatable = TranslatableFactory::create('page', $pages[$id]);
                return $translatable->getContent($lang, $key);
            }
            break;
            
        case 'general':
            $general = loadJsonData('general');
            if (isset($general[$id])) {
                $translatable = TranslatableFactory::create('general', $general[$id]);
                return $translatable->getContent($lang, $key);
            }
            break;
    }
    
    return null;
}

/**
 * Get all items of a specific type
 * 
 * @param string $type Data type (segments, products, team, etc.)
 * @return array Array of items
 */
function getAllItems($type) {
    switch ($type) {
        case 'segments':
            return loadJsonData('segments');
            
        case 'products':
            return loadJsonData('products');
            
        case 'team':
            return loadJsonData('team');
            
        case 'pages':
            return loadJsonData('pages');
            
        default:
            return [];
    }
}

/**
 * Get featured items of a specific type
 * 
 * @param string $type Data type (products, team, etc.)
 * @return array Array of featured items
 */
function getFeaturedItems($type) {
    $result = [];
    
    switch ($type) {
        case 'products':
            $products = loadJsonData('products');
            foreach ($products as $segmentSlug => $segmentProducts) {
                foreach ($segmentProducts as $productSlug => $product) {
                    if (isset($product['is_featured']) && $product['is_featured']) {
                        $result[$productSlug] = $product;
                    }
                }
            }
            break;
            
        case 'team':
            $team = loadJsonData('team');
            if (isset($team['founders'])) {
                $result = array_slice($team['founders'], 0, 3); // First 3 founders
            }
            break;
    }
    
    return $result;
}

/**
 * Get specific page content
 * 
 * @param string $pageSlug The page slug
 * @param string $lang The language code
 * @param string $key The content key
 * @return string|null The page content or null if not found
 */
function getPageContent($pageSlug, $lang, $key) {
    $pages = loadJsonData('pages');
    
    if (isset($pages[$pageSlug])) {
        return getTranslatedContent($pages[$pageSlug], $lang, $key);
    }
    
    return null;
}

