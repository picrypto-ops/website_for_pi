<?php
function loadJsonData($file) {
    $jsonFile = "data/{$file}.json";
    if (file_exists($jsonFile)) {
        $data = json_decode(file_get_contents($jsonFile), true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $data;
        }
    }
    error_log("Error loading JSON file: {$jsonFile}");
    return null;
}

function getTranslation($key) {
    global $translations;
    return isset($translations[$key]) ? $translations[$key] : $key;
}

function generateBreadcrumbs($page) {
    // Implementation of breadcrumbs generation
    // This function will be implemented later
}

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function isActiveMenu($currentPage, $menuItem) {
    return $currentPage === $menuItem ? 'active' : '';
}

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

function getLocalizedContent($data, $lang, $key) {
    return isset($data['language_slug'][$lang][$key]) ? $data['language_slug'][$lang][$key] : $key;
}

function getTranslatedContent($data, $lang, $key) {
    return isset($data['language_slug'][$lang][$key]) ? $data['language_slug'][$lang][$key] : $key;
}

