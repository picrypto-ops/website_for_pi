<?php
$segmentId = isset($_GET['id']) ? sanitizeInput($_GET['id']) : ''; // Sanitize input
$segments = TranslatableFactory::getData('segments');
$products = TranslatableFactory::getData('products');
// $general = TranslatableFactory::getData('general'); // Not used directly here

$segment = null;
if (!empty($segmentId) && isset($segments[$segmentId])) {
    $segment = $segments[$segmentId];
} else {
    // No valid segment ID found, redirect to 404 or show message
    redirectToPage('404', ['original' => 'segment:' . $segmentId], $lang);
    exit; // Stop execution
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

<?php // Use a BEM block for the segment page overall structure ?>
<section class="segment-page"> <?php // New BEM Block for the page ?>
    <div class="container">
        <?php // BEM Element: segment-page__title ?>
        <h1 class="segment-page__title">
            <?php echo $segmentTranslatable->getContent($lang, 'name', $segment['segment_slug']); ?>
        </h1>
        <?php // BEM Element: segment-page__description ?>
        <div class="segment-page__description content-with-html"> <?php // Add helper class for HTML content ?>
            <?php echo $segmentTranslatable->getHtmlContent($lang, 'short_description', 'Detailed information about this business segment.'); ?>
        </div>

        <?php // Use the standard .card-grid layout for products ?>
        <div class="card-grid segment-page__product-grid"> <?php // BEM Element for context ?>
            <?php if (empty($segmentProducts)): ?>
                <p><?php echo TranslatableFactory::general()->getContent($lang, 'no_products', 'No products available for this segment.'); ?></p>
            <?php else: ?>
                <?php foreach ($segmentProducts as $productSlug => $product):
                    // Ensure $product is an array before proceeding
                    if (!is_array($product)) continue;

                    $productTranslatable = TranslatableFactory::product($productSlug);
                    $productLogoPath = getProductLogoPath($product); // Use helper function
                ?>
                    <?php // Use card-link helper for clickable card ?>
                    <a href="?page=product&id=<?php echo $productSlug; ?>&segment=<?php echo $segmentId; ?>&lang=<?php echo $lang; ?>" class="card-link">
                        <?php // --- BEM Block: product-card --- ?>
                        <div class="product-card">
                            <div class="product-card__icon">
                                <img src="<?php echo $productLogoPath; ?>" alt="<?php echo $productTranslatable->getContent($lang, 'name', 'Product'); ?>" class="product-card__icon-image">
                            </div>
                            <div class="product-card__text">
                                <h3 class="product-card__title"><?php echo $productTranslatable->getContent($lang, 'name', 'Investment Product'); ?></h3>
                                <p class="product-card__slogan">
                                    <?php echo $productTranslatable->getContent($lang, 'slogan', ''); ?>
                                </p>
                            </div>
                        </div>
                        <?php // --- End BEM Block: product-card --- ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>