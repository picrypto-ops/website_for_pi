<?php
function setLanguage($lang) {
    $langFile = "lang/{$lang}.json";
    if (file_exists($langFile)) {
        $translations = json_decode(file_get_contents($langFile), true);
        global $translations;
    } else {
        // Fallback to default language
        $defaultLangFile = "lang/" . DEFAULT_LANG . ".json";
        if (file_exists($defaultLangFile)) {
            $translations = json_decode(file_get_contents($defaultLangFile), true);
            global $translations;
        } else {
            // If default language file doesn't exist, use an empty array
            $translations = [];
        }
    }
}

function t($key) {
    global $translations;
    return isset($translations[$key]) ? $translations[$key] : $key;
}

