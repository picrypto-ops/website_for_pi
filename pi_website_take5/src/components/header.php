<?php
// Initialize data with error handling to prevent warnings
$generalData = TranslatableFactory::getData('general') ?: [];
$menuData = TranslatableFactory::getData('menus') ?: [];
$isHomePage = ($page === 'home');

// Define a default page title and description in case data is not found
$defaultTitle = 'PI Group';
$defaultDescription = 'Financial Management Professionals';

// Set default value for $useNewScss if not defined
$useNewScss = isset($useNewScss) ? $useNewScss : true;

// Ensure functions.php is included
require_once __DIR__ . '/../utility/functions.php';

?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'he' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title><?php echo isset($generalData['home']) ? getTranslatedContent($generalData['home'], $lang, 'label', $defaultTitle) : $defaultTitle; ?> - <?php echo isset($generalData[$page]) ? getTranslatedContent($generalData[$page], $lang, 'label', ucfirst($page)) : ucfirst($page); ?></title>
    <meta name="description" content="<?php echo isset($generalData[$page]) ? getTranslatedContent($generalData[$page], $lang, 'description', $defaultDescription) : $defaultDescription; ?>">
    <link rel="canonical" href="<?php echo defined('BASE_URL') ? BASE_URL . '/' . $lang . '/' . $page : '/'; ?>">
    <meta property="og:title" content="<?php echo isset($generalData['home']) ? getTranslatedContent($generalData['home'], $lang, 'label', $defaultTitle) : $defaultTitle; ?> - <?php echo isset($generalData[$page]) ? getTranslatedContent($generalData[$page], $lang, 'label', ucfirst($page)) : ucfirst($page); ?>">
    <meta property="og:description" content="<?php echo isset($generalData[$page]) ? getTranslatedContent($generalData[$page], $lang, 'description', $defaultDescription) : $defaultDescription; ?>">
    <meta property="og:url" content="<?php echo defined('BASE_URL') ? BASE_URL . '/' . $lang . '/' . $page : '/'; ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>/assets/images/og-image.jpg">

    <?php /* Link to the compiled CSS file */ ?>
    <link rel="stylesheet" href="assets/css/main.css"> <?php /* Adjust path/name if needed */ ?>

    <?php if ($page === 'contact'): ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>
    <?php endif; ?>

    <?php /* Load JavaScript */ ?>
    <script type="module" src="assets/js/main.js"></script>
    <?php if ($page === 'home'): ?>
        <script
            src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"
            integrity="sha384-CI3ELBVUz9XQO+97x6nwMDPosPR5XvsxW2ua7N1Xeygeh1IxtgqtCkGfQY9WWdHu"
            crossorigin="anonymous">
        </script>
    <?php endif; ?>
    <?php if ($page === 'contact'): ?>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
    <?php endif; ?>

    <?php /* Structured Data Inclusion */ ?>
    <?php
    require_once __DIR__ . '/../utility/structured-data.php';
    $structuredDataParams = ['lang' => $lang];
    // Add page-specific data as before
    if ($page === 'contact') {
        $contactData = TranslatableFactory::getData('contact') ?: [];
        if (isset($contactData['contact']) && isset($contactData['contact']['contact_details'])) {
            $details = TranslatableFactory::createFromData($contactData['contact'])->getContent($lang, 'contact_details', []);
             if(!is_array($details)) $details = json_decode($details, true); // Ensure it's an array
             $structuredDataParams['phone'] = $details['phone'] ?? '';
             $structuredDataParams['email'] = $details['email'] ?? '';
        }
    }
    // ... (add other page specific data like team member, segment) ...
    echo outputStructuredData($page, $structuredDataParams);
    ?>
</head>
<body class="<?php echo $page; ?>-page">
    <?php // --- Site Header Block --- ?>
    <?php // *** ADDED id="site-header" BACK FOR JS TARGETING *** ?>
    <header id="site-header" class="site-header <?php echo $isHomePage ? 'home-header initially-hidden' : ''; ?>">
        <div class="container site-header__container">
            <div class="site-header__content">

                <?php // --- Mobile Menu Toggle Element --- ?>
                <button class="site-header__toggle"
                    aria-label="<?php echo TranslatableFactory::general()->getContent($lang, 'toggle_menu', 'Toggle navigation menu'); ?>"
                    aria-expanded="false"
                    aria-controls="main-nav-menu">
                    <span class="site-header__toggle-icon">
                        <span class="site-header__toggle-bar"></span>
                        <span class="site-header__toggle-bar"></span>
                        <span class="site-header__toggle-bar"></span>
                    </span>
                </button>

                <?php // --- Logo Element --- ?>
                <div class="site-header__logo">
                    <a href="?page=home&lang=<?php echo $lang; ?>" class="site-header__logo-link">
                        <div class="site-header__logo-container">
                            <img src="assets/images/logo_small.svg" alt="PI Group Logo" class="site-header__logo-image">
                            <span class="site-header__logo-text"><?php echo isset($generalData['group_name']) ? getTranslatedContent($generalData['group_name'], $lang, 'label', 'PI Group') : 'PI Group'; ?></span>
                        </div>
                    </a>
                </div>

                <?php // --- Navigation Element --- ?>
                <nav class="site-header__nav" id="main-nav-menu">
                    <ul class="site-header__nav-list">
                        <?php
                        if (isset($menuData['main_menu'])):
                            foreach ($menuData['main_menu'] as $item):
                                $itemSlug = $item['main_page_slug'] ?? '';
                                $isSegment = in_array($itemSlug, ['investment_banking', 'asset_management']);
                                $isTeam = $itemSlug === 'our-team';
                                $isHome = $itemSlug === 'home';

                                if ($isHome) { $url = "?page=home&lang={$lang}"; }
                                elseif ($isSegment) { $url = "?page=segment&id={$itemSlug}&lang={$lang}"; }
                                elseif ($isTeam) { $url = "?page=our-team&lang={$lang}"; }
                                else { $pageTarget = ltrim($item['url'] ?? $itemSlug, '/'); $url = "?page={$pageTarget}&lang={$lang}"; }
                        ?>
                        <?php // Add is-active class using the PHP function ?>
                        <li class="site-header__nav-item <?php echo isActiveMenu($page, $itemSlug); ?>">
                            <a href="<?php echo htmlspecialchars($url); ?>" class="site-header__nav-link">
                                <?php echo getTranslatedContent($item, $lang, 'label'); ?>
                            </a>
                        </li>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </ul>
                </nav>

                <?php // --- Language Switcher Element --- ?>
                <div class="site-header__lang-switcher">
                     <?php echo renderLanguageSwitcherBem($lang, '', false); ?>
                </div>

            </div> <?php // End site-header__content ?>
        </div> <?php // End container ?>
    </header> <?php // End site-header ?>
    <main id="main-content">