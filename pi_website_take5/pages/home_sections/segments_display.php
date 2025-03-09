<?php
/**
 * Segment display rendering for the home page
 */

/**
 * Renders a specific segment with its products
 * 
 * @param string $lang Current language code
 * @param string $segment_slug The slug of the segment to display
 * @param string $nextSectionId ID of the next section to scroll to (optional)
 * @return void
 */
function renderSegmentDisplay($lang, $segment_slug, $nextSectionId = null) {
    $segments = TranslatableFactory::getData('segments');
    $products = TranslatableFactory::getData('products');
    
    // Validate segment exists
    if (!isset($segments[$segment_slug]) || !is_array($segments[$segment_slug])) {
        echo "<div class='error-message'>Segment not found</div>";
        return;
    }
    
    $segment = $segments[$segment_slug];
    
    // Find products that belong to this segment
    $segmentProducts = [];
    if (is_array($products)) {
        // Check if segment ID matches the product category directly
        if (isset($products[$segment_slug]) && is_array($products[$segment_slug])) {
            $segmentProducts = $products[$segment_slug];
        }
    }
    
    // If nextSectionId is not provided, calculate it
    if ($nextSectionId === null) {
        // Find the next segment or point to team section if this is the last segment
        $segmentKeys = array_keys($segments);
        $currentIndex = array_search($segment_slug, $segmentKeys);
        
        if ($currentIndex !== false && $currentIndex < count($segmentKeys) - 1) {
            // There is a next segment
            $nextSegment = $segmentKeys[$currentIndex + 1];
            $nextSectionId = 'segment-' . $nextSegment;
        } else {
            // This is the last segment, point to team section
            $nextSectionId = 'team';
        }
    }
    
    // Create translatable for this segment
    $segmentTranslatable = TranslatableFactory::segment($segment_slug);
    ?>
    
    <section id="segment-<?php echo $segment_slug; ?>" class="segment full-page-section">
        <div class="container">
            <h2><?php echo $segmentTranslatable->getContent($lang, 'name', $segment['segment_slug']); ?></h2>
            <div class="segment-section-description">
                <?php echo $segmentTranslatable->getContent($lang, 'short_description', 'Detailed information about this business segment.'); ?>
            </div>

            <h3><?php echo TranslatableFactory::general()->getContent($lang, 'our_products', 'Our Products'); ?></h3>
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
                            </p>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="back-to-segments">
                <a href="#hero" class="back-link">
                    <?php echo TranslatableFactory::general()->getContent($lang, 'back_to_home', 'Back to Home'); ?>
                </a>
            </div>
        </div>
        
        <?php renderScrollIndicator($nextSectionId, $lang, 'segment-' . $segment_slug); ?>
    </section>
    <?php
} 