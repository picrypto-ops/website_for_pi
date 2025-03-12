<?php
$productId = isset($_GET['id']) ? $_GET['id'] : '';
$allProducts = TranslatableFactory::getData('products');
$allTeam = TranslatableFactory::getData('team');

$product = null;
$productCategory = null;

// Search for the product in the nested structure
if (!empty($productId) && is_array($allProducts)) {
    foreach ($allProducts as $categorySlug => $categoryProducts) {
        if (!is_array($categoryProducts)) continue;
        
        if (isset($categoryProducts[$productId])) {
            $product = $categoryProducts[$productId];
            $productCategory = $categorySlug;
            break;
        }
    }
}

// If product not found, display error and exit
if (!$product) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 - Product Not Found</h1>";
    echo "<p>The requested product does not exist.</p>";
    echo "<p><a href='index.php'>Return to homepage</a></p>";
    exit;
}

// Get translatable for this product
$productTranslatable = TranslatableFactory::product($productId);

// Find team members associated with this product
$productTeam = [];
if (isset($product['team_group']) && is_array($allTeam)) {
    foreach ($allTeam as $teamGroup => $members) {
        if (!is_array($members)) continue;
        
        foreach ($members as $memberKey => $member) {
            if (!is_array($member)) continue;
            
            // Check if member belongs to the product's team group
            if (isset($member['groups']) && is_array($member['groups']) && 
                in_array($product['team_group'], $member['groups'])) {
                $productTeam[$memberKey] = $member;
            }
        }
    }
}

// Find founder team members associated with team_group_slug
$founderTeam = [];
if (isset($product['team_group_slug']) && !empty($product['team_group_slug']) && is_array($allTeam)) {
    // Debug output to help troubleshoot
    if (TranslatableFactory::$debug) {
        error_log("Looking for team members with team_group_slug: " . $product['team_group_slug']);
    }
    
    foreach ($allTeam as $teamGroup => $members) {
        if (!is_array($members)) continue;
        
        foreach ($members as $memberKey => $member) {
            if (!is_array($member)) continue;
            
            // Check if member is a founder
            $isFounder = isset($member['is_founder']) && $member['is_founder'] === true;
            
            // Check if member belongs to the product's team_group_slug
            // First check direct match with team_group_slug
            if ($isFounder && $product['team_group_slug'] === $teamGroup) {
                $founderTeam[$memberKey] = $member;
                continue;
            }
            
            // Then check if member has the team_group_slug in their groups array
            if ($isFounder && isset($member['groups']) && is_array($member['groups']) && 
                in_array($product['team_group_slug'], $member['groups'])) {
                $founderTeam[$memberKey] = $member;
            }
            
            // Also check if the member's name_slug matches the team_group_slug
            // This handles cases where team_group_slug might refer to an individual
            if ($isFounder && isset($member['name_slug']) && 
                $member['name_slug'] === $product['team_group_slug']) {
                $founderTeam[$memberKey] = $member;
            }
        }
    }
    
    // If still empty, try a more flexible approach by checking if the team_group_slug
    // is contained within any member data
    if (empty($founderTeam)) {
        foreach ($allTeam as $teamGroup => $members) {
            if (!is_array($members)) continue;
            
            foreach ($members as $memberKey => $member) {
                if (!is_array($member)) continue;
                
                if (isset($member['is_founder']) && $member['is_founder'] === true) {
                    // Add any founder to the team if we couldn't find specific matches
                    // This is a fallback to ensure we show something
                    $founderTeam[$memberKey] = $member;
                }
            }
        }
    }
}
?>

<section class="product">
    <?php
            // Determine the path to the product image
            $productSlug = isset($product['product_slug']) ? $product['product_slug'] : '';
            $productCategory = isset($product['asset_category']) ? $product['asset_category'] : '';
            $productLogoPath = getProductLogoPath($product);
            
            // Special case for pi_emf which has an image in pi_emf_retired directory
            $productImagePath = "assets/images/{$productCategory}/{$productSlug}/product_image.webp";
            // Check if product image exists
            $productImageExists = file_exists($productImagePath);
            
            // Get product description - this could be a string or an array
            $description = $productTranslatable->getContent($lang, 'description', 'Product description not available.');
            
            // If description is a string, convert it to an array for consistent processing
            if (!is_array($description)) {
                $description = [$description];
            }
            
            // Get the image insertion index (default to after the first paragraph if not specified)
            $imageInsertionIndex = $productTranslatable->getContent($lang, 'image_insertion_index', 1);
            $imageInsertionIndex = intval($imageInsertionIndex);
            
            // Make sure the index is valid
            if ($imageInsertionIndex < 0) {
                $imageInsertionIndex = 0;
            } elseif ($imageInsertionIndex > count($description)) {
                $imageInsertionIndex = count($description);
            }
    ?>
    <div class="container">
        <div class="product-header">
            <?php if (file_exists($productLogoPath)): ?>
                <div class="product-logo">
                    <img src="<?php echo $productLogoPath; ?>" alt="<?php echo $productTranslatable->getContent($lang, 'name', $product['product_slug']); ?>">
                </div>
            <?php endif; ?>
            <div class="product-title">
                <h1><?php echo $productTranslatable->getContent($lang, 'name', $product['product_slug']); ?></h1>
                <?php if ($productTranslatable->hasContent($lang, 'slogan')): ?>
                    <p class="product-slogan"><?php echo $productTranslatable->getContent($lang, 'slogan', ''); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="product-content">
            <?php 
            // Display paragraphs before the image
            for ($i = 0; $i < $imageInsertionIndex; $i++) {
                if (isset($description[$i])) {
                    echo '<p class="product-description-paragraph">' . $description[$i] . '</p>';
                }
            }
            
            // Display the product image if it exists
            if ($productImageExists): 
            ?>
                <div class="product-image">
                    <img src="<?php echo $productImagePath; ?>" alt="<?php echo $productTranslatable->getContent($lang, 'name', $productSlug); ?> team">
                    <div class="product-image-caption">
                        <?php echo TranslatableFactory::general()->getContent($lang, 'product_image_caption', 'Our dedicated team'); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php 
            // Display paragraphs after the image
            for ($i = $imageInsertionIndex; $i < count($description); $i++) {
                echo '<p class="product-description-paragraph">' . $description[$i] . '</p>';
            }
            ?>
        </div>

        <?php if (!empty($founderTeam)): ?>
            <h2 class="section-title"><?php echo TranslatableFactory::general()->getContent($lang, 'product_founders', 'Founders'); ?></h2>
            <div class="team-grid founders-grid">
                <?php foreach ($founderTeam as $memberKey => $member): ?>
                    <?php $memberTranslatable = TranslatableFactory::createFromData($member); ?>
                    <a href="?page=team-member&id=<?php echo $member['name_slug']; ?>&lang=<?php echo $lang; ?>" class="card-link">
                        <div class="team-card founder-card">
                            <?php if (isset($member['photo']) && !empty($member['photo'])): ?>
                                <img src="<?php echo $member['photo']; ?>" alt="<?php echo $memberTranslatable->getContent($lang, 'name', $member['name_slug']); ?>">
                            <?php else: ?>
                                <div class="photo-placeholder"><i class="fa fa-user-circle"></i></div>
                            <?php endif; ?>
                            <div class="team-info">
                                <h3><?php echo $memberTranslatable->getContent($lang, 'name', $member['name_slug']); ?></h3>
                                <?php
                                // Check if this member should display product roles
                                $displayProductRoles = $memberTranslatable->getContent($lang, 'display_product_roles', false);
                                
                                if ($displayProductRoles) {
                                    // Get position which will be an array of roles when display_product_roles is true
                                    $positions = $memberTranslatable->getContent($lang, 'position', []);
                                    
                                    if (is_array($positions) && !empty($positions)) {
                                        echo '<div class="product-roles">';
                                        foreach ($positions as $role) {
                                            echo '<p class="role">';
                                            
                                            $roleTitle = isset($role['title']) ? $role['title'] : '';
                                            $productName = isset($role['product_name']) ? $role['product_name'] : '';
                                            // todo: format the output to be more readable
                                            if (!empty($roleTitle) && !empty($productName)) {
                                                echo htmlspecialchars(ucfirst($roleTitle)) . ' - ' . htmlspecialchars($productName);
                                            } elseif (!empty($roleTitle)) {
                                                echo htmlspecialchars(ucfirst($roleTitle));
                                            } elseif (!empty($productName)) {
                                                echo htmlspecialchars($productName);
                                            }
                                            
                                            echo '</p>';
                                        }
                                        echo '</div>';
                                    } else {
                                        echo '<p class="position">' . $memberTranslatable->getContent($lang, 'position', 'Team Member') . '</p>';
                                    }
                                } else {
                                    echo '<p class="position">' . $memberTranslatable->getContent($lang, 'position', 'Team Member') . '</p>';
                                }
                                ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($productTeam)): ?>
            <div class="team-section">
                <h2 class="section-title"><?php echo TranslatableFactory::general()->getContent($lang, 'product_team', 'Product Team'); ?></h2>
                <div class="team-grid">
                    <?php foreach ($productTeam as $memberKey => $member): ?>
                        <?php $memberTranslatable = TranslatableFactory::teamMemberEnhanced($member['name_slug']); ?>
                        <a href="?page=team-member&id=<?php echo $member['name_slug']; ?>&lang=<?php echo $lang; ?>" class="card-link">
                            <div class="team-card">
                                <div class="team-info">
                                    <h3><?php echo $memberTranslatable->getContent($lang, 'name', $member['name_slug']); ?></h3>
                                    <?php
                                    // Check if this member should display product roles
                                    $displayProductRoles = $memberTranslatable->getContent($lang, 'display_product_roles', false);
                                    
                                    if ($displayProductRoles) {
                                        // Get position which will be an array of roles when display_product_roles is true
                                        $positions = $memberTranslatable->getContent($lang, 'position', []);
                                        
                                        if (is_array($positions) && !empty($positions)) {
                                            echo '<div class="product-roles">';
                                            foreach ($positions as $role) {
                                                echo '<div class="role">';
                                                
                                                $roleTitle = isset($role['title']) ? $role['title'] : '';
                                                $productName = isset($role['product_name']) ? $role['product_name'] : '';
                                                
                                                if (!empty($productName)) {
                                                    echo '<span class="product-name">' . htmlspecialchars($productName) . '</span>';
                                                }
                                                
                                                if (!empty($roleTitle)) {
                                                    echo '<span class="role-title">' . htmlspecialchars(ucfirst($roleTitle)) . '</span>';
                                                }
                                                
                                                echo '</div>';
                                            }
                                            echo '</div>';
                                        } else {
                                            echo '<p class="position">' . $memberTranslatable->getContent($lang, 'position', 'Team Member') . '</p>';
                                        }
                                    } else {
                                        echo '<p class="position">' . $memberTranslatable->getContent($lang, 'position', 'Team Member') . '</p>';
                                    }
                                    ?>
                                    
                                    <?php if ($memberTranslatable->hasContent($lang, 'short_bio')): ?>
                                        <p class="short-bio"><?php echo $memberTranslatable->getContent($lang, 'short_bio', ''); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <?php
        // Add a back to segment link if we have a segment parameter
        $segmentId = isset($_GET['segment']) ? $_GET['segment'] : $productCategory;
        if ($segmentId): 
        ?>
        <div class="back-to-segment">
            <a href="?page=segment&id=<?php echo $segmentId; ?>&lang=<?php echo $lang; ?>" class="back-link">
                <span class="back-arrow">←</span> 
                <?php echo TranslatableFactory::general()->getContent($lang, 'back_to_segment', 'Back to Segment'); ?>
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

