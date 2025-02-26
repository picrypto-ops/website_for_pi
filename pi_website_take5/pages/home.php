<?php
// Turn on debug output temporarily - remove in production
TranslatableFactory::$debug = false;

// Initialize the factory and preload common data files
TranslatableFactory::initialize(['general', 'segments', 'products', 'menus', 'pages', 'team']);

// Include necessary files
include_once 'includes/functions.php';

// Load required data for the page rendering
// We still need these variables for loops and direct data access
$segmentData = TranslatableFactory::getData('segments');
$productData = TranslatableFactory::getData('products');
$generalData = TranslatableFactory::getData('general');
$teamData = TranslatableFactory::getData('team');
$pagesData = TranslatableFactory::getData('pages');
$contactData = isset($pagesData['contact']) ? $pagesData['contact'] : [];

// DEBUGGING - Check if classes are available before using them
if (!class_exists('TranslatableFactory')) {
    echo "<div style='color:red; padding:10px; background:#fff; border:1px solid red; margin:10px; font-family:sans-serif;'>";
    echo "Error: TranslatableFactory class not found. Check includes.";
    echo "</div>";
    exit;
}

// Try-catch for debugging
try {
    // Create translatable objects for commonly used data - now with simplified API
    TranslatableFactory::debugToScreen("Creating translatable objects on home page...");
    
    // Use the new specialized methods that handle data loading transparently
    $homeTranslatable = TranslatableFactory::page('home');
    $aboutTranslatable = TranslatableFactory::page('about');
    $whatWeDoTranslatable = TranslatableFactory::page('what_we_do');
    $generalTranslatableForScroll = TranslatableFactory::general();
    
    TranslatableFactory::debugToScreen("Successfully created translatable objects!");
} catch (Throwable $e) {
    echo "<div style='color:red; padding:10px; background:#fff; border:1px solid red; margin:10px; font-family:sans-serif;'>";
    echo "Error initializing translatable objects: " . $e->getMessage();
    echo "<br>File: " . $e->getFile() . " on line " . $e->getLine();
    echo "</div>";
    exit;
}

// If you want to see all debug information but continue rendering the page
try {
    // At the top of the page, show a collapsible debug section
    echo "<div style='position:fixed; top:0; right:0; z-index:1000; background:#fff; border:1px solid #ccc; padding:5px;'>";
    echo "<button onclick=\"document.getElementById('debugInfo').style.display = document.getElementById('debugInfo').style.display === 'none' ? 'block' : 'none';\">Toggle Debug</button>";
    echo "<div id='debugInfo' style='display:none; max-height:300px; overflow:auto; padding:10px;'>";
    echo "<h3>Debug Information</h3>";
    echo "<p>TranslatableFactory class loaded: " . (class_exists('TranslatableFactory') ? 'Yes' : 'No') . "</p>";
    echo "<p>AbstractTranslatable class loaded: " . (class_exists('AbstractTranslatable') ? 'Yes' : 'No') . "</p>";
    echo "<p>Home translatable class: " . get_class($homeTranslatable) . "</p>";
    echo "<p>About translatable class: " . get_class($aboutTranslatable) . "</p>";
    echo "<p>WhatWeDo translatable class: " . get_class($whatWeDoTranslatable) . "</p>";
    echo "<p>General translatable class: " . get_class($generalTranslatableForScroll) . "</p>";
    echo "</div></div>";
} catch (Throwable $e) {
    // Ignore errors in debug display
}
?>

<div class="home-page">
    <section class="hero full-page">
        <!-- Logo in top right for English -->
        <div class="hero-top-logo <?php echo $lang === 'he' ? 'left-aligned' : 'right-aligned'; ?>">
            <img src="assets/images/logo.svg" alt="PI Group Logo">
        </div>
        
        <div class="hero-content">
            <h1><?php echo $homeTranslatable->getContent($lang, 'title'); ?></h1>
            <p class="slogan"><?php echo $homeTranslatable->getContent($lang, 'slogan'); ?></p>
            <p class="wide-description"><?php echo $homeTranslatable->getContent($lang, 'home_description'); ?></p>
            <a href="#about-us" class="cta-button"><?php echo TranslatableFactory::general()->getContent($lang, 'learn_more'); ?></a>
        </div>

        <!-- ThreeJS container positioned at bottom 30% -->
        <div id="threeJsContainer"></div>
        
        <div class="scroll-indicator">
            <a href="#about-us" class="scroll-down">
                <span><?php echo $generalTranslatableForScroll->getContent($lang, 'scroll_down'); ?></span>
                <i class="arrow-down"></i>
            </a>
        </div>
    </section>

    <!-- About Us Section -->
    <section id="about-us" class="about-preview full-page-section">
        <div class="container">
            <h2><?php echo $aboutTranslatable->getContent($lang, 'title'); ?></h2>
            <p><?php echo $aboutTranslatable->getContent($lang, 'home_description'); ?></p>
            <a href="?page=about&lang=<?php echo $lang; ?>" class="button">
                <?php 
                    $readMoreText = TranslatableFactory::general()->getContent($lang, 'read_more');
                    // If read_more key doesn't exist, fallback to default text
                    echo !empty($readMoreText) ? $readMoreText : 'Read More'; 
                ?>
            </a>
        </div>
        
        <!-- Add scroll indicator on each section -->
        <div class="scroll-indicator">
            <a href="#what-we-do" class="scroll-down">
                <span><?php echo $generalTranslatableForScroll->getContent($lang, 'scroll_down'); ?></span>
                <i class="arrow-down"></i>
            </a>
        </div>
    </section>

    <!-- What We Do Section -->
    <section id="what-we-do" class="featured-segments full-page-section">
        <div class="container">
            <h2><?php echo $whatWeDoTranslatable->getContent($lang, 'title'); ?></h2>
            <p><?php echo $whatWeDoTranslatable->getContent($lang, 'home_description'); ?></p>
            <div class="segment-grid">
                <?php if (is_array($segmentData)): ?>
                    <?php foreach ($segmentData as $segmentSlug => $segment): ?>
                        <?php $segmentTranslatable = TranslatableFactory::segment($segmentSlug); ?>
                        <div class="segment-card">
                            <h3><?php echo $segmentTranslatable->getContent($lang, 'name'); ?></h3>
                            <p><?php echo $segmentTranslatable->getContent($lang, 'home_description'); ?></p>
                            <a href="?page=segment&id=<?php echo $segmentSlug; ?>&lang=<?php echo $lang; ?>" class="segment-link">
                                <span><?php echo TranslatableFactory::general()->getContent($lang, 'read_more'); ?></span>
                                <span class="icon-arrow-right"></span>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Add scroll indicator -->
        <div class="scroll-indicator">
            <a href="#products" class="scroll-down">
                <span><?php echo $generalTranslatableForScroll->getContent($lang, 'scroll_down'); ?></span>
                <i class="arrow-down"></i>
            </a>
        </div>
    </section>

    <!-- Products Section -->
    <section id="products" class="featured-products full-page-section">
        <div class="container">
            <h2><?php echo TranslatableFactory::general()->getContent($lang, 'our_products'); ?></h2>
            
            <!-- Debugging info to show product data -->
            <?php if (TranslatableFactory::$debug): ?>
                <div class="debug-info" style="background:#f8f8f8; padding:10px; margin-bottom:20px; font-size:12px;">
                    <p>Products data loaded: <?php echo count($productData); ?> segments</p>
                    <p>First segment: <?php echo !empty($productData) ? array_key_first($productData) : 'None'; ?></p>
                </div>
            <?php endif; ?>
            
            <!-- Featured Products Grid -->
            <div class="product-grid">
                <?php 
                    // Extract featured products with better error checking
                    $featuredProducts = [];
                    if (is_array($productData)) {
                        foreach ($productData as $segmentSlug => $products) {
                            if (is_array($products)) {
                                foreach ($products as $productSlug => $product) {
                                    if (isset($product['is_featured']) && $product['is_featured']) {
                                        $featuredProducts[$productSlug] = $product;
                                    }
                                }
                            }
                        }
                    }

                    // If no featured products found, show some example placeholders
                    if (empty($featuredProducts)) {
                        // Display placeholders if no products are found
                        for ($i = 1; $i <= 3; $i++):
                ?>
                        <div class="product-card">
                            <div class="placeholder-image">Product <?php echo $i; ?></div>
                            <h3>Example Product <?php echo $i; ?></h3>
                            <p>This is a placeholder for when no featured products are found in the data. Check your products.json file.</p>
                            <a href="#" class="button">Learn More</a>
                        </div>
                <?php 
                        endfor;
                    } else {
                        // Display actual featured products
                        foreach ($featuredProducts as $productSlug => $product): 
                ?>
                        <div class="product-card">
                            <?php if (isset($product['logo']) && !empty($product['logo'])): ?>
                                <img src="<?php echo $product['logo']; ?>" alt="<?php echo TranslatableFactory::product($productSlug)->getContent($lang, 'name'); ?>">
                            <?php else: ?>
                                <div class="placeholder-image">Product Image</div>
                            <?php endif; ?>
                            <h3><?php echo TranslatableFactory::product($productSlug)->getContent($lang, 'name'); ?></h3>
                            <p><?php echo TranslatableFactory::product($productSlug)->getContent($lang, 'slogan'); ?></p>
                            <a href="?page=product&id=<?php echo $productSlug; ?>&lang=<?php echo $lang; ?>" class="button">
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
        
        <!-- Add scroll indicator to this section -->
        <div class="scroll-indicator">
            <a href="#team" class="scroll-down">
                <span class="sr-only"><?php echo TranslatableFactory::general()->getContent($lang, 'scroll_down'); ?></span>
                <i class="arrow-down"></i>
            </a>
        </div>
    </section>

    <!-- Team Section -->
    <section id="team" class="team-preview full-page-section">
        <div class="container">
            <h2><?php 
                echo TranslatableFactory::general('team')->getContent($lang, 'label'); 
            ?></h2>
            <p><?php echo $homeTranslatable->getContent($lang, 'team_description'); ?></p>
            
            <!-- Group team members by team group -->
            <?php 
            // Get team groups
            $teamGroups = [];
            if (isset($teamData) && is_array($teamData)) {
                foreach ($teamData as $groupKey => $members) {
                    if (is_array($members)) {
                        foreach ($members as $member) {
                            if (isset($member['display_in_home_page']) && $member['display_in_home_page']) {
                                if (!isset($teamGroups[$groupKey])) {
                                    $teamGroups[$groupKey] = [];
                                }
                                $teamGroups[$groupKey][] = $member;
                            }
                        }
                    }
                }
            }
            
            // Display team members by group
            foreach ($teamGroups as $groupKey => $members):
                $groupLabel = isset($generalData['team_groups'][$groupKey]) ? 
                    TranslatableFactory::general($generalData['team_groups'][$groupKey])->getContent($lang, 'label') : 
                    ucfirst($groupKey);
            ?>
                <div class="team-group">
                    <h3><?php echo $groupLabel; ?></h3>
                    <div class="team-preview-grid">
                        <?php if (empty($members)): ?>
                            <div style="color:#888;">No team members found for this group.</div>
                        <?php else: ?>
                            <?php foreach ($members as $member): ?>
                                <?php 
                                    // Debug and ensure we have proper data
                                    if (!isset($member['name_slug'])) {
                                        echo "<div style='color:red'>Missing name_slug in team member data</div>";
                                        continue;
                                    }
                                    
                                    // Get the correct translatable for a team member
                                    $memberTranslatable = TranslatableFactory::createFromData($member);
                                ?>
                                <div class="team-card">
                                    <?php if (isset($member['photo']) && !empty($member['photo'])): ?>
                                        <img src="<?php echo $member['photo']; ?>" alt="<?php echo $memberTranslatable->getContent($lang, 'name'); ?>">
                                    <?php else: ?>
                                        <div class="placeholder-photo">Photo</div>
                                    <?php endif; ?>
                                    <h3><?php echo $memberTranslatable->getContent($lang, 'name'); ?></h3>
                                    <p><?php echo $memberTranslatable->getContent($lang, 'position'); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <a href="?page=our-team&lang=<?php echo $lang; ?>" class="button">
                <?php echo TranslatableFactory::general()->getContent($lang, 'meet_our_team'); ?>
            </a>
        </div>
        
        <!-- Add scroll indicator -->
        <div class="scroll-indicator">
            <a href="#contact-us" class="scroll-down">
                <span><?php echo $generalTranslatableForScroll->getContent($lang, 'scroll_down'); ?></span>
                <i class="arrow-down"></i>
            </a>
        </div>
    </section>

    <!-- Contact Us Section -->
    <section id="contact-us" class="contact-preview full-page-section">
        <div class="container">
            <h2><?php 
                $contactTranslatable = TranslatableFactory::general($generalData['contact']); 
                echo $contactTranslatable->getContent($lang, 'label'); 
            ?></h2>
            <p><?php echo $homeTranslatable->getContent($lang, 'contact_description'); ?></p>
            
            <!-- Add contact details -->
            <div class="contact-details-preview">
                <?php if (isset($contactData['address'])): ?>
                <div class="contact-detail">
                    <i class="icon-location"></i>
                    <p><?php echo TranslatableFactory::general()->getContent($lang, 'address'); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (isset($contactData['phone'])): ?>
                <div class="contact-detail">
                    <i class="icon-phone"></i>
                    <p><?php echo $contactData['phone']; ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (isset($contactData['email'])): ?>
                <div class="contact-detail">
                    <i class="icon-email"></i>
                    <p><?php echo $contactData['email']; ?></p>
                </div>
                <?php endif; ?>
            </div>
            
            <a href="?page=contact&lang=<?php echo $lang; ?>" class="button">
                <?php echo TranslatableFactory::general()->getContent($lang, 'contact_us'); ?>
            </a>
        </div>
        
        <!-- Add scroll button to appear on every page -->
        <div class="scroll-indicator">
            <a href="#" class="scroll-down" id="scrollToNextSection">
                <span><?php echo $generalTranslatableForScroll->getContent($lang, 'scroll_down'); ?></span>
                <i class="arrow-down"></i>
            </a>
        </div>
    </section>
</div>

<!-- Replace embedded script with external script reference -->
<script src="assets/js/home.js"></script>

<!-- At the bottom of home.php, add this script to fix scroll buttons -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fix scroll indicators to ensure they're visible on all sections
    const sections = document.querySelectorAll('.full-page-section');
    const lastSection = sections[sections.length - 1];
    
    sections.forEach((section, index) => {
        const scrollIndicator = section.querySelector('.scroll-indicator');
        
        // If this is the last section, hide its scroll indicator or make it scroll to top
        if (index === sections.length - 1) {
            if (scrollIndicator) {
                scrollIndicator.style.display = 'none';
                // Or alternatively, make it scroll to top:
                // scrollIndicator.querySelector('a').setAttribute('href', '#');
                // scrollIndicator.querySelector('span').textContent = 'Back to Top';
            }
        } 
        // Otherwise make sure it points to the next section
        else if (scrollIndicator && index < sections.length - 1) {
            const nextSectionId = sections[index + 1].id;
            if (nextSectionId) {
                scrollIndicator.querySelector('a').setAttribute('href', '#' + nextSectionId);
            }
        }
    });
});
</script>

