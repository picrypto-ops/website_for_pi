<?php
$segmentId = isset($_GET['id']) ? $_GET['id'] : '';
$segments = TranslatableFactory::getData('segments');
$products = TranslatableFactory::getData('products');

$segment = null;
if (!empty($segmentId) && isset($segments[$segmentId])) {
    $segment = $segments[$segmentId];
} else {
    // No valid segment ID found
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 - Segment Not Found</h1>";
    echo "<p>The requested segment does not exist.</p>";
    echo "<p><a href='index.php'>Return to homepage</a></p>";
    exit;
}

// Find products that belong to this segment
$segmentProducts = [];
if (is_array($products)) {
    // Check if segment ID matches the product category directly
    if (isset($products[$segmentId]) && is_array($products[$segmentId])) {
        $segmentProducts = $products[$segmentId];
    }
}

// Create translatable for this segment
$segmentTranslatable = TranslatableFactory::segment($segmentId);
?>

<section class="segment">
    <div class="container">
        <h1><?php echo $segmentTranslatable->getContent($lang, 'name', $segment['segment_slug']); ?></h1>
        <p class="lead"><?php echo $segmentTranslatable->getContent($lang, 'short_description', 'Business segment information.'); ?></p>
        <div class="segment-description">
            <?php echo $segmentTranslatable->getContent($lang, 'long_description', 'Detailed information about this business segment.'); ?>
        </div>

        <h2><?php echo TranslatableFactory::general()->getContent($lang, 'our_products', 'Our Products'); ?></h2>
        <div class="product-grid">
            <?php if (empty($segmentProducts)): ?>
                <p><?php echo TranslatableFactory::general()->getContent($lang, 'no_products', 'No products available for this segment.'); ?></p>
            <?php else: ?>
                <?php foreach ($segmentProducts as $productSlug => $product): 
                    // Create a proper translatable for this product
                    $productTranslatable = TranslatableFactory::product($productSlug);
                ?>
                    <a href="?page=product&id=<?php echo $productSlug; ?>&lang=<?php echo $lang; ?>" class="product-card">
                        <div class="product-content">
                            <div class="product-icon">
                                <?php if (isset($product['logo']) && !empty($product['logo'])): ?>
                                    <img src="<?php echo $product['logo']; ?>" alt="<?php echo isset($product['language_slug'][$lang]['name']) ? $product['language_slug'][$lang]['name'] : 'Product'; ?>">
                                <?php else: ?>
                                    <img src="assets/images/products/pi-logo-icon.svg" alt="PI Logo">
                                <?php endif; ?>
                            </div>
                            <div class="product-text">
                                <h3><?php echo isset($product['language_slug'][$lang]['name']) ? $product['language_slug'][$lang]['name'] : 'Investment Product'; ?></h3>
                            </div>
                        </div>
                        <p class="slogan">
                            <?php echo isset($product['language_slug'][$lang]['slogan']) ? $product['language_slug'][$lang]['slogan'] : ''; ?>
                            <span class="arrow">»</span>
                        </p>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

