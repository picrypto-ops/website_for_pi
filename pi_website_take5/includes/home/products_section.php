<?php
/**
 * Products section rendering for the home page
 */

/**
 * Renders the Featured Products section
 * 
 * @param string $lang Current language code
 * @return void
 */
function renderProductsSection($lang) {
    $productData = TranslatableFactory::getData('products');
    ?>
    <section id="products" class="featured-products full-page-section">
        <div class="container">
            <h2><?php echo TranslatableFactory::general()->getContent($lang, 'featured_products') ?: 'Featured Products'; ?></h2>
            <p class="section-description"><?php echo TranslatableFactory::general()->getContent($lang, 'featured_products_description') ?: 'Discover our premium financial products designed to meet your needs.'; ?></p>
            
            <!-- Featured Products Grid -->
            <div class="product-grid">
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
                                    // Store both the product and its slug
                                    $featuredProducts[] = [
                                        'slug' => $productSlug,
                                        'data' => $product,
                                        'category' => $categorySlug
                                    ];
                                    $counter++;
                                    
                                    // Limit to 3 featured products
                                    if ($counter >= 3) break;
                                }
                            }
                            if ($counter >= 3) break;
                        }
                    }

                    // If no featured products found, show example placeholders
                    if (empty($featuredProducts)) {
                        // Display placeholders if no products are found
                        for ($i = 1; $i <= 3; $i++):
                ?>
                        <div class="product-card">
                            <div class="product-icon">
                                <div class="icon-placeholder"><i class="fa fa-briefcase"></i></div>
                            </div>
                            <h3>Premium Investment <?php echo $i; ?></h3>
                            <p>This premium investment product offers excellent return potential with managed risk levels.</p>
                            <a href="#" class="button product-button">Learn More</a>
                        </div>
                <?php 
                        endfor;
                    } else {
                        // Display actual featured products
                        foreach ($featuredProducts as $productInfo): 
                            $productSlug = $productInfo['slug'];
                            $product = $productInfo['data'];
                            
                            // Create a proper translatable for this product
                            $productTranslatable = TranslatableFactory::product($productSlug);
                ?>
                        <div class="product-card">
                            <div class="product-icon">
                                <?php if (isset($product['logo']) && !empty($product['logo'])): ?>
                                    <img src="<?php echo $product['logo']; ?>" alt="<?php echo isset($product['language_slug'][$lang]['name']) ? $product['language_slug'][$lang]['name'] : 'Product'; ?>">
                                <?php else: ?>
                                    <div class="icon-placeholder"><i class="fa fa-chart-pie"></i></div>
                                <?php endif; ?>
                            </div>
                            <h3><?php echo isset($product['language_slug'][$lang]['name']) ? $product['language_slug'][$lang]['name'] : 'Investment Product'; ?></h3>
                            <p><?php echo isset($product['language_slug'][$lang]['slogan']) ? $product['language_slug'][$lang]['slogan'] : 'A premium financial product for discerning investors.'; ?></p>
                            <a href="?page=product&id=<?php echo $productSlug; ?>&lang=<?php echo $lang; ?>" class="button product-button">
                                <?php echo TranslatableFactory::general()->getContent($lang, 'learn_more') ?: 'Learn More'; ?>
                            </a>
                        </div>
                <?php 
                        endforeach; 
                    }
                ?>
            </div>
            
            <a href="?page=products&lang=<?php echo $lang; ?>" class="button view-all-button">
                <?php echo TranslatableFactory::general()->getContent($lang, 'view_all_products') ?: 'View All Products'; ?>
            </a>
        </div>
        
        <?php renderScrollIndicator('team', $lang); ?>
    </section>
    <?php
} 