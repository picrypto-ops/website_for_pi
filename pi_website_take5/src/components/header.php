<?php
// Initialize data with error handling to prevent warnings
$generalData = TranslatableFactory::getData('general') ?: [];
$menuData = TranslatableFactory::getData('menus') ?: [];
$isHomePage = $page === 'home';

// Define a default page title and description in case data is not found
$defaultTitle = 'PI Group';
$defaultDescription = 'Financial Management Professionals';

// Set default value for $useNewScss if not defined
$useNewScss = isset($useNewScss) ? $useNewScss : false;
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
    <?php if ($useNewScss): ?>
    <link rel="stylesheet" href="assets/scss_new/main_new.css">
    <?php else: ?>
    <link rel="stylesheet" href="assets/css/main.css">
    <?php endif; ?>
    
    <?php if ($page === 'contact'): ?>
    <!-- Leaflet CSS for OpenStreetMap -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
          crossorigin=""/>
    <?php endif; ?>
    
    <!-- Load the new modular JavaScript main file -->
    <script type="module" src="assets/js/main.js"></script>
    <?php if ($page === 'home'): ?>
        <script 
            src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js" 
            integrity="sha384-CI3ELBVUz9XQO+97x6nwMDPosPR5XvsxW2ua7N1Xeygeh1IxtgqtCkGfQY9WWdHu" 
            crossorigin="anonymous">
        </script>
    <?php endif; ?>
    <?php if ($page === 'contact'): ?>
    <!-- Leaflet JS for OpenStreetMap -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
            crossorigin=""></script>
    <?php endif; ?>
    
    <?php
    // Add structured data for SEO
    require_once 'src/utility/structured-data.php';
    
    // Prepare data for structured data generation
    $structuredDataParams = [
        'lang' => $lang
    ];
    
    // Add page-specific data
    if ($page === 'contact') {
        $contactData = TranslatableFactory::getData('contact') ?: [];
        $structuredDataParams['phone'] = $contactData['phone'] ?? '';
        $structuredDataParams['email'] = $contactData['email'] ?? '';
    } 
    else if ($page === 'team-member' && isset($_GET['id'])) {
        $teamMembers = TranslatableFactory::getData('team') ?: [];
        $memberId = $_GET['id'];
        
        if (isset($teamMembers[$memberId])) {
            $member = $teamMembers[$memberId];
            $structuredDataParams['id'] = $memberId;
            $structuredDataParams['name'] = $member['name'] ?? '';
            $structuredDataParams['title'] = $member['title'] ?? '';
            $structuredDataParams['bio'] = $member['bio'] ?? '';
            $structuredDataParams['image'] = $member['image'] ?? '';
            $structuredDataParams['linkedin'] = $member['linkedin'] ?? '';
        }
    }
    else if ($page === 'segment' && isset($_GET['id'])) {
        $structuredDataParams['segment_id'] = $_GET['id'];
    }
    
    // Output the structured data
    echo outputStructuredData($page, $structuredDataParams);
    ?>
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
                                $isTeam = $item['main_page_slug'] === 'our-team';
                                
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
                <?php echo renderLanguageSwitcher($lang); ?>
            </div>
        </div>
    </header>
    <main id="main-content">

