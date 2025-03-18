<?php
/**
 * Language Switcher Demo
 * 
 * This file demonstrates how to use the renderLanguageSwitcher function
 * in different contexts and with different styling options.
 */

// Include required files
require_once dirname(__DIR__) . '/src/config/config.php';
require_once dirname(__DIR__) . '/src/utility/functions.php';
require_once dirname(__DIR__) . '/src/utility/language.php';

// Get language from query parameter, default to English
$lang = isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'he']) ? $_GET['lang'] : 'en';

// Set the language
setLanguage($lang);

// Set the page direction based on language
// ... existing code ... 