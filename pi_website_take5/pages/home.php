<?php
$generalData = loadJsonData('general');
$segmentData = loadJsonData('segments');
$productData = loadJsonData('products');
$menuData = loadJsonData('menus');
include_once 'includes/functions.php';
?>

<div class="home-page">
    <section class="hero full-page">
        <div class="hero-content">
            <h1><?php echo getTranslatedContent($generalData['home'], $lang, 'label'); ?></h1>
            <p><?php echo getTranslatedContent($menuData['main_menu'][0], $lang, 'description'); ?></p>
            <a href="#learn-more" class="cta-button"><?php echo getTranslatedContent($generalData, $lang, 'learn_more'); ?></a>
        </div>
        <div id="threeJsContainer"></div>
        <div class="scroll-indicator">
            <span><?php echo getTranslatedContent($generalData, $lang, 'scroll_down'); ?></span>
            <i class="arrow-down"></i>
        </div>
    </section>

    <section id="learn-more" class="featured-segments">
        <div class="container">
            <h2><?php echo getTranslatedContent($generalData['our_segments'], $lang, 'label'); ?></h2>
            <?php if (is_array($segmentData)): ?>
                <?php foreach ($segmentData as $segmentSlug => $segment): ?>
                    <div class="segment-card">
                        <h3><?php echo getTranslatedContent($segment, $lang, 'name'); ?></h3>
                        <p><?php echo getTranslatedContent($segment, $lang, 'short_description'); ?></p>
                        <a href="?page=segment&id=<?php echo $segmentSlug; ?>&lang=<?php echo $lang; ?>" class="button">
                            <?php echo getTranslatedContent($generalData, $lang, 'read_more'); ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <section class="featured-products">
        <div class="container">
            <h2><?php echo getTranslatedContent($generalData['our_products'], $lang, 'label'); ?></h2>
            <?php if (is_array($productData)): ?>
                <?php foreach ($productData as $segmentSlug => $products): ?>
                    <?php foreach ($products as $productSlug => $product): ?>
                        <div class="product-card">
                            <img src="<?php echo $product['logo']; ?>" alt="<?php echo getTranslatedContent($product, $lang, 'name'); ?>">
                            <h3><?php echo getTranslatedContent($product, $lang, 'name'); ?></h3>
                            <p><?php echo getTranslatedContent($product, $lang, 'slogan'); ?></p>
                            <a href="?page=product&id=<?php echo $productSlug; ?>&lang=<?php echo $lang; ?>" class="button">
                                <?php echo getTranslatedContent($generalData, $lang, 'learn_more'); ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <section class="about-preview">
        <div class="container">
            <h2><?php echo getTranslatedContent($generalData['about'], $lang, 'label'); ?></h2>
            <p><?php echo getTranslatedContent($generalData['about_us_short'], $lang, 'label'); ?></p>
            <a href="?page=about&lang=<?php echo $lang; ?>" class="button">
                <?php echo getTranslatedContent($generalData, $lang, 'learn_more'); ?>
            </a>
        </div>
    </section>
</div>

