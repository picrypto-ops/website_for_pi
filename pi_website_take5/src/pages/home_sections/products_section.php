<?php
/**
 * Products section rendering for the home page
 */

/**
 * Renders the Featured Products section
 *
 * @param string $lang Current language code
 * @param string $nextSectionId ID of the next section to scroll to (default: 'team')
 * @return void
 */
function renderProductsSection($lang, $nextSectionId = 'team') {
    $productData = TranslatableFactory::getData('products');
    ?>
    <section id="products" class="featured-products full-page-section">
        <div class="container">
            <h2 class="section-header"> <?php // Example class, adjust if needed ?>
                <?php echo TranslatableFactory::general()->getContent($lang, 'featured_products', 'Featured Products'); ?>
            </h2>
            <p class="section-description"> <?php // Example class, adjust if needed ?>
                <?php echo TranslatableFactory::general()->getContent($lang, 'featured_products_description', 'Discover our premium financial products designed to meet your needs.'); ?>
            </p>

            <?php // Use the standard .card-grid layout ?>
            <div class="card-grid">
                <?php
                    // Extract featured products from the nested structure
                    $featuredProducts = [];
                    $counter = 0;

                    if (is_array($productData)) {
                        foreach ($productData as $categorySlug => $categoryProducts) {
                            if (!is_array($categoryProducts)) continue;

                            foreach ($categoryProducts as $productSlug => $product) {
                                if (!is_array($product)) continue;

                                if (isset($product['is_featured']) && $product['is_featured'] === true) {
                                    // Store necessary info
                                    $featuredProducts[] = [
                                        'slug' => $productSlug,
                                        'data' => $product,
                                        'category' => $categorySlug
                                    ];
                                    $counter++;

                                    // Limit to 3 featured products (or adjust as needed)
                                    if ($counter >= 3) break;
                                }
                            }
                            if ($counter >= 3) break;
                        }
                    }

                    // If no featured products found, show a message or placeholders
                    if (empty($featuredProducts)) {
                        echo "<p>" . TranslatableFactory::general()->getContent($lang, 'no_featured_products', 'No featured products available at this time.') . "</p>";
                        // Optionally display placeholder cards here
                    } else {
                        // Display actual featured products using the new BEM structure
                        foreach ($featuredProducts as $productInfo):
                            $productSlug = $productInfo['slug'];
                            $product = $productInfo['data'];
                            $segmentId = $productInfo['category'] ?? ''; // Get the segment ID

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
                <?php
                        endforeach;
                    }
                ?>
            </div>

            <?php // Removed "View All Products" button as per previous comments, add back if needed ?>
            <?php /*
            <div class="view-all-button-container text-center mt-8"> <?php // Example container styling ?>
                <a href="?page=products&lang=<?php echo $lang; ?>" class="button button--primary"> <?php // Example button styling ?>
                    <?php echo TranslatableFactory::general()->getContent($lang, 'view_all_products', 'View All Products'); ?>
                </a>
            </div>
            */ ?>
        </div>

        <?php renderScrollIndicator($nextSectionId, $lang, 'products'); // Pass correct current section ID ?>
    </section>
    <?php
}
?>