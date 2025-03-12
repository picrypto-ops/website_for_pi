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
            <h2><?php echo TranslatableFactory::general()->getContent($lang, 'featured_products', 'Featured Products'); ?></h2>
            <p class="section-description"><?php echo TranslatableFactory::general()->getContent($lang, 'featured_products_description', 'Discover our premium financial products designed to meet your needs.'); ?></p>
            
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
                            // No valid segment ID found
                            header("HTTP/1.0 404 Not Found");
                            echo "<h1>404 - Segment Not Found</h1>";
                            echo "<p>The requested segment does not exist.</p>";
                            echo "<p><a href='index.php'>Return to homepage</a></p>";
                            exit;
                    } else {
                        // Display actual featured products
                        foreach ($featuredProducts as $productInfo): 
                            $productSlug = $productInfo['slug'];
                            $product = $productInfo['data'];
                            $segmentId = $productInfo['category'] ?? '';
                            
                            // Create a proper translatable for this product
                            $productTranslatable = TranslatableFactory::product($productSlug);
                ?>
                        <a href="?page=product&id=<?php echo $productSlug; ?>&segment=<?php echo $segmentId; ?>&lang=<?php echo $lang; ?>" class="product-card">
                            <div class="product-content">
                                <div class="product-icon">
                                    <?php 
                                        $productLogoPath = getProductLogoPath($product);
                                    ?>
                                    <img src="<?php echo $productLogoPath; ?>" alt="<?php echo isset($product['language_slug'][$lang]['name']) ? $product['language_slug'][$lang]['name'] : 'Product'; ?>">
                                </div>
                                <div class="product-text">
                                    <h3><?php echo isset($product['language_slug'][$lang]['name']) ? $product['language_slug'][$lang]['name'] : 'Investment Product'; ?></h3>
                                </div>
                            </div>
                            <p class="slogan">
                                <?php echo isset($product['language_slug'][$lang]['slogan']) ? $product['language_slug'][$lang]['slogan'] : ''; ?>
                            </p>
                        </a>
                <?php 
                        endforeach; 
                    }
                ?>
            </div>
            
            <!-- Removed "View All Products" button as requested -->
        </div>
        
        <?php renderScrollIndicator('team', $lang); ?>
    </section>
    <?php
} 