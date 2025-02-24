<?php
$generalData = loadJsonData('general');
$segmentData = loadJsonData('segment');
$productData = loadJsonData('product');
?>

<div class="home-page">
    <section class="hero full-page">
        <div class="hero-content">
            <h1><?php echo getLocalizedContent($generalData['home'], $lang, 'label'); ?></h1>
            <p><?php echo t('home_hero_description'); ?></p>
            <a href="#learn-more" class="cta-button"><?php echo t('learn_more'); ?></a>
        </div>
        <div id="pi-bg-wave"></div>
        <div class="scroll-indicator">
            <span><?php echo t('scroll_down'); ?></span>
            <i class="arrow-down"></i>
        </div>
    </section>

    <section id="learn-more" class="featured-segments">
        <div class="container">
            <h2><?php echo t('our_segments'); ?></h2>
            <?php foreach ($segmentData as $segmentSlug => $segment): ?>
                <div class="segment-card">
                    <h3><?php echo $segment['name']; ?></h3>
                    <p><?php echo $segment['short_description']; ?></p>
                    <a href="?page=segment&id=<?php echo $segmentSlug; ?>&lang=<?php echo $lang; ?>"><?php echo t('read_more'); ?></a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="featured-products">
        <div class="container">
            <h2><?php echo t('our_products'); ?></h2>
            <?php foreach ($productData as $segmentSlug => $products): ?>
                <?php foreach ($products as $productSlug => $product): ?>
                    <div class="product-card">
                        <img src="<?php echo $product['logo']; ?>" alt="<?php echo getLocalizedContent($product, $lang, 'name'); ?>">
                        <h3><?php echo getLocalizedContent($product, $lang, 'name'); ?></h3>
                        <p><?php echo getLocalizedContent($product, $lang, 'slogan'); ?></p>
                        <a href="?page=product&id=<?php echo $productSlug; ?>&lang=<?php echo $lang; ?>"><?php echo t('learn_more'); ?></a>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="about-preview">
        <div class="container">
            <h2><?php echo getLocalizedContent($generalData['about'], $lang, 'label'); ?></h2>
            <p><?php echo t('about_us_short'); ?></p>
            <a href="?page=about&lang=<?php echo $lang; ?>" class="button"><?php echo t('learn_more'); ?></a>
        </div>
    </section>
</div>

