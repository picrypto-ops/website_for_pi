<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'he' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('site_title'); ?> - <?php echo t($page . '_title'); ?></title>
    <meta name="description" content="<?php echo t($page . '_description'); ?>">
    <link rel="canonical" href="<?php echo BASE_URL . '/' . $lang . '/' . $page; ?>">
    <meta property="og:title" content="<?php echo t('site_title'); ?> - <?php echo t($page . '_title'); ?>">
    <meta property="og:description" content="<?php echo t($page . '_description'); ?>">
    <meta property="og:url" content="<?php echo BASE_URL . '/' . $lang . '/' . $page; ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?php echo BASE_URL; ?>/assets/images/og-image.jpg">
    <link rel="stylesheet" href="assets/css/main.css">
    <script src="assets/js/main.js" defer></script>
    <?php if ($page === 'home'): ?>
    <script src="assets/js/pi_bg_wave.js" defer></script>
    <?php endif; ?>
</head>
<body class="<?php echo $page; ?>-page">
    <a href="#main-content" class="skip-link"><?php echo t('skip_to_content'); ?></a>
    <header>
        <nav>
            <ul>
                <?php
                $menuItems = loadJsonData('menu');
                if ($menuItems && isset($menuItems['main_menu'])):
                    foreach ($menuItems['main_menu'] as $item):
                ?>
                <li class="<?php echo isActiveMenu($page, $item['main_page_slug']); ?>">
                    <a href="<?php echo $item['url']; ?>">
                        <?php echo t($item['language_slug'][$lang]['label']); ?>
                    </a>
                </li>
                <?php 
                    endforeach;
                endif;
                ?>
            </ul>
        </nav>
        <div class="language-switcher">
            <a href="?page=<?php echo $page; ?>&lang=en">English</a>
            <a href="?page=<?php echo $page; ?>&lang=he">עברית</a>
        </div>
    </header>
    <main id="main-content">

