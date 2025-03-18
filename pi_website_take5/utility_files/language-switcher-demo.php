<?php
/**
 * Language Switcher Demo
 * 
 * This file demonstrates how to use the renderLanguageSwitcher function
 * in different contexts and with different styling options.
 */

// Include required files
require_once 'src/config/config.php';
require_once 'src/utility/functions.php';
require_once 'src/utility/language.php';

// Get language from query parameter, default to English
$lang = isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'he']) ? $_GET['lang'] : 'en';

// Set the language
setLanguage($lang);

// Set the page direction based on language
$dir = $lang === 'he' ? 'rtl' : 'ltr';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Language Switcher Demo</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <script src="assets/js/language-switcher.js" defer></script>
    <style>
        .demo-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .demo-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .demo-section h2 {
            margin-top: 0;
        }
        .custom-language-switcher {
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 5px;
            display: inline-block;
        }
        .floating-language-switcher {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 5px;
            padding: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .code-example {
            background-color: #f8f8f8;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="demo-container">
        <h1>Language Switcher Demo</h1>
        <p>This page demonstrates different ways to use the language switcher function.</p>
        
        <div class="demo-section">
            <h2>Basic Usage</h2>
            <p>The most basic implementation of the language switcher:</p>
            <?php echo renderLanguageSwitcher($lang); ?>
            
            <div class="code-example">
                <pre>&lt;?php echo renderLanguageSwitcher($lang); ?&gt;</pre>
            </div>
        </div>
        
        <div class="demo-section">
            <h2>With Custom CSS Class</h2>
            <p>Adding custom CSS classes to style the language switcher:</p>
            <?php echo renderLanguageSwitcher($lang, 'custom-language-switcher'); ?>
            
            <div class="code-example">
                <pre>&lt;?php echo renderLanguageSwitcher($lang, 'custom-language-switcher'); ?&gt;</pre>
            </div>
        </div>
        
        <div class="demo-section">
            <h2>In Different Positions</h2>
            <p>The language switcher can be placed in various positions on your page:</p>
            
            <h3>Inline with Text</h3>
            <p>This is some text with a language switcher <?php echo renderLanguageSwitcher($lang); ?> right in the middle of a paragraph.</p>
            
            <h3>Floating Position</h3>
            <div class="floating-language-switcher">
                <?php echo renderLanguageSwitcher($lang); ?>
            </div>
            <p>A floating language switcher appears in the top right corner of this page.</p>
            
            <div class="code-example">
                <pre>&lt;div class="floating-language-switcher"&gt;
    &lt;?php echo renderLanguageSwitcher($lang); ?&gt;
&lt;/div&gt;</pre>
            </div>
        </div>
        
        <div class="demo-section">
            <h2>Integration with Layout</h2>
            <p>This demonstrates how the language switcher fits into a typical layout:</p>
            
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; background-color: #333; color: white;">
                <div>Logo</div>
                <nav style="display: flex; gap: 20px;">
                    <a href="#" style="color: white;">Home</a>
                    <a href="#" style="color: white;">About</a>
                    <a href="#" style="color: white;">Contact</a>
                </nav>
                <?php echo renderLanguageSwitcher($lang); ?>
            </div>
            
            <div class="code-example">
                <pre>&lt;div style="display: flex; justify-content: space-between; align-items: center;"&gt;
    &lt;div&gt;Logo&lt;/div&gt;
    &lt;nav&gt;
        &lt;a href="#"&gt;Home&lt;/a&gt;
        &lt;a href="#"&gt;About&lt;/a&gt;
        &lt;a href="#"&gt;Contact&lt;/a&gt;
    &lt;/nav&gt;
    &lt;?php echo renderLanguageSwitcher($lang); ?&gt;
&lt;/div&gt;</pre>
            </div>
        </div>
        
        <div class="demo-section">
            <h2>Without Wrapper Div</h2>
            <p>Use this option when you already have a container for the language switcher:</p>
            
            <div style="display: inline-block; padding: 10px; background-color: #f0f0f0; border-radius: 5px;">
                <?php echo renderLanguageSwitcher($lang, '', false); ?>
            </div>
            
            <div class="code-example">
                <pre>&lt;div style="display: inline-block; padding: 10px; background-color: #f0f0f0;"&gt;
    &lt;?php echo renderLanguageSwitcher($lang, '', false); ?&gt;
&lt;/div&gt;</pre>
            </div>
            
            <p>This is very useful for hero sections or custom containers where you need direct control over the wrapper:</p>
            
            <div class="code-example">
                <pre>&lt;div class="hero-language-switcher &lt;?php echo $lang === 'he' ? 'right-aligned' : 'left-aligned'; ?&gt;"&gt;
    &lt;?php echo renderLanguageSwitcher($lang, '', false); ?&gt;
&lt;/div&gt;</pre>
            </div>
        </div>
    </div>
</body>
</html> 