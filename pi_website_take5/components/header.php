<?php
// Initialize data with error handling to prevent warnings
$generalData = TranslatableFactory::getData('general') ?: [];
$menuData = TranslatableFactory::getData('menus') ?: [];
$isHomePage = $page === 'home';

// Define a default page title and description in case data is not found
$defaultTitle = 'PI Group';
$defaultDescription = 'Financial Management Professionals';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'he' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title><?php echo isset($generalData['home']) ? getTranslatedContent($generalData['home'], $lang, 'label') : $defaultTitle; ?> - <?php echo isset($generalData[$page]) ? getTranslatedContent($generalData[$page], $lang, 'label') : ucfirst($page); ?></title>
    <meta name="description" content="<?php echo isset($generalData[$page]) ? getTranslatedContent($generalData[$page], $lang, 'description') : $defaultDescription; ?>">
    <link rel="canonical" href="<?php echo defined('BASE_URL') ? BASE_URL . '/' . $lang . '/' . $page : '/'; ?>">
    <meta property="og:title" content="<?php echo isset($generalData['home']) ? getTranslatedContent($generalData['home'], $lang, 'label') : $defaultTitle; ?> - <?php echo isset($generalData[$page]) ? getTranslatedContent($generalData[$page], $lang, 'label') : ucfirst($page); ?>">
    <meta property="og:description" content="<?php echo isset($generalData[$page]) ? getTranslatedContent($generalData[$page], $lang, 'description') : $defaultDescription; ?>">
    <meta property="og:url" content="<?php echo defined('BASE_URL') ? BASE_URL . '/' . $lang . '/' . $page : '/'; ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>/assets/images/og-image.jpg">
    <link rel="stylesheet" href="assets/css/main.css">
    <script type="module" src="assets/js/main.js"></script>
    <script src="assets/js/menu.js" defer></script>
    <script src="assets/js/language-switcher.js" defer></script>
    <?php if ($page === 'home'): ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script type="module" src="assets/js/pi_bg_wave.js"></script>
    <?php endif; ?>
</head>
<body class="<?php echo $page; ?>-page">
    <header id="site-header" class="<?php echo $isHomePage ? 'home-header initially-hidden' : ''; ?>">
        <div class="container">
            <div class="header-content">
                <!-- Mobile menu toggle button for screens < 768px -->
                <button class="mobile-menu-toggle" 
                    aria-label="Toggle navigation menu" 
                    aria-expanded="false">
                    <span class="menu-icon">
                        <span class="bar"></span>
                        <span class="bar"></span>
                        <span class="bar"></span>
                    </span>
                </button>
                
                <!-- Section 1: Logo & Group name -->
                <div class="logo-section">
                    <a href="?page=home&lang=<?php echo $lang; ?>" class="logo-link">
                        <div class="logo-container">
                            <img src="assets/images/logo_small.svg" alt="PI Group Logo">
                            <span class="group-name"><?php echo isset($generalData['group_name']) ? getTranslatedContent($generalData['group_name'], $lang, 'label') : 'PI Group'; ?></span>
                        </div>
                    </a>
                </div>
                
                <!-- Section 2: Main Navigation Menu -->
                <nav class="main-nav">
                    <ul>
                        <?php
                        if (isset($menuData['main_menu'])):
                            foreach ($menuData['main_menu'] as $item):
                                // Check if this is a segment page (investment_banking or asset_management)
                                $isSegment = in_array($item['main_page_slug'], ['investment_banking', 'asset_management']);
                                
                                // Check if this is the team page
                                $isTeam = $item['main_page_slug'] === 'team';
                                
                                // Check if this is the home page
                                $isHome = $item['main_page_slug'] === 'home';
                                
                                // Generate the correct URL
                                if ($isHome) {
                                    $url = "?page=home&lang={$lang}";
                                } elseif ($isSegment) {
                                    $url = "?page=segment&id={$item['main_page_slug']}&lang={$lang}";
                                } elseif ($isTeam) {
                                    $url = "?page=our-team&lang={$lang}";
                                } else {
                                    $url = strpos($item['url'], '/') === 0 ? "?page=".ltrim($item['url'], '/')."&lang={$lang}" : $item['url'];
                                }
                        ?>
                        <li class="<?php echo isActiveMenu($page, $item['main_page_slug']); ?>">
                            <a href="<?php echo $url; ?>">
                                <?php echo getTranslatedContent($item, $lang, 'label'); ?>
                            </a>
                        </li>
                        <?php 
                            endforeach;
                        endif;
                        ?>
                    </ul>
                </nav>
                
                <!-- Section 3: Language Switcher -->
                <div class="language-switcher">
                    <?php
                    // Get current URL parameters
                    $currentParams = $_GET;
                    
                    // Create EN link with all current parameters except for lang
                    $enParams = $currentParams;
                    $enParams['lang'] = 'en';
                    $enLink = '?' . http_build_query($enParams);
                    
                    // Create HE link with all current parameters except for lang
                    $heParams = $currentParams;
                    $heParams['lang'] = 'he';
                    $heLink = '?' . http_build_query($heParams);
                    ?>
                    <a href="<?php echo $enLink; ?>" class="lang-switch <?php echo $lang === 'en' ? 'active' : ''; ?>" data-lang="en">EN</a>
                    <span class="separator">|</span>
                    <a href="<?php echo $heLink; ?>" class="lang-switch <?php echo $lang === 'he' ? 'active' : ''; ?>" data-lang="he">עב</a>
                </div>
            </div>
        </div>
    </header>
    <main id="main-content">

