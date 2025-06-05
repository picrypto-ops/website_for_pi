<?php
/**
 * Segment display rendering for the home page
 * (Displays products within a specific segment section)
 */

/**
 * Renders a specific segment with its products on the home page
 *
 * @param string $lang Current language code
 * @param string $segment_slug The slug of the segment to display
 * @param string|null $nextSectionId ID of the next section to scroll to (optional)
 * @return void
 */
function renderSegmentDisplay($lang, $segment_slug, $nextSectionId = null) {
    $segments = TranslatableFactory::getData('segments');
    $products = TranslatableFactory::getData('products');

    // Validate segment exists
    if (!isset($segments[$segment_slug]) || !is_array($segments[$segment_slug])) {
        error_log("Segment data not found or invalid for slug: " . $segment_slug);
        return;
    }

    $segment = $segments[$segment_slug];

    // Find products that belong to this segment
    $segmentProducts = [];
    if (is_array($products) && isset($products[$segment_slug]) && is_array($products[$segment_slug])) {
        $segmentProducts = $products[$segment_slug];
    }

    // Calculate next section ID if not provided
    if ($nextSectionId === null) {
        $segmentKeys = is_array($segments) ? array_keys($segments) : [];
        $currentIndex = array_search($segment_slug, $segmentKeys);
        $isLastSegment = ($currentIndex === false || $currentIndex >= count($segmentKeys) - 1);

        if (!$isLastSegment) {
            $nextSegmentSlug = $segmentKeys[$currentIndex + 1];
            $nextSectionId = 'segment-' . $nextSegmentSlug;
        } else {
            $nextSectionId = 'team'; // Point to team section after the last segment
        }
    }

    $segmentTranslatable = TranslatableFactory::segment($segment_slug);
    ?>

    <section id="segment-<?php echo $segment_slug; ?>" class="segment-display full-page-section">
        <div class="container">
            <h2 class="section-header">
                <?php echo $segmentTranslatable->getContent($lang, 'name', $segment['segment_slug']); ?>
            </h2>
            <div class="section-description content-with-html">
                <?php echo $segmentTranslatable->getHtmlContent($lang, 'short_description', 'Detailed information about this business segment.'); ?>
            </div>

            <div class="card-grid"> <?php // Use standard card grid ?>
                <?php if (empty($segmentProducts)): ?>
                    <p><?php echo TranslatableFactory::general()->getContent($lang, 'no_products', 'No products available for this segment.'); ?></p>
                <?php else: ?>
                    <?php foreach ($segmentProducts as $productSlug => $product):
                        if (!is_array($product)) continue;
                        $productTranslatable = TranslatableFactory::product($productSlug);
                        $productLogoPath = getProductLogoPath($product);
                    ?>
                        <a href="?page=product&id=<?php echo $productSlug; ?>&segment=<?php echo $segment_slug; ?>&lang=<?php echo $lang; ?>" class="card-link">
                            <div class="product-card">
                                <?php // NEW: Body wrapper for icon+text ?>
                                <div class="product-card__body">
                                    <div class="product-card__icon">
                                        <img src="<?php echo $productLogoPath; ?>" alt="<?php echo $productTranslatable->getContent($lang, 'name', 'Product'); ?>" class="product-card__icon-image">
                                    </div>
                                    <div class="product-card__text">
                                        <h3 class="product-card__title"><?php echo $productTranslatable->getContent($lang, 'name', 'Investment Product'); ?></h3>
                                        <p class="product-card__slogan">
                                            <?php echo $productTranslatable->getContent($lang, 'slogan', ''); ?>
                                        </p>
                                    </div>
                                </div> <?php // End __body ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php // REMOVED "View Segment Details" button ?>

            <?php // ADDED "Back to Home" link (similar to original screenshot) ?>
            <div class="back-to-link-container text-center mt-8"> <?php // Use similar container class ?>
                 <a href="#hero" class="button button--outline"> <?php // Link to top '#hero', style as outline button ?>
                    <?php echo TranslatableFactory::general()->getContent($lang, 'back_to_home', 'Back to Home'); ?>
                 </a>
            </div>

        </div>

        <?php renderScrollIndicator($nextSectionId, $lang, 'segment-' . $segment_slug); ?>
    </section>
    <?php
}
?>