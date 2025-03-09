<?php
$allTeam = TranslatableFactory::getData('team');

// Get all team groups
$teamGroups = array_keys($allTeam);
?>

<section class="our-team">
    <div class="container">
        <h1 class="page-title"><?php echo TranslatableFactory::general()->getContent($lang, 'our_team_title', 'Our Team'); ?></h1>
        <p class="lead"><?php echo TranslatableFactory::general()->getContent($lang, 'our_team_description', 'Meet the people who make it all happen.'); ?></p>

        <?php if (empty($teamGroups)): ?>
            <p><?php echo TranslatableFactory::general()->getContent($lang, 'no_team_members', 'No team members available.'); ?></p>
        <?php else: ?>
            <?php foreach ($teamGroups as $groupSlug): ?>
                <?php 
                $groupMembers = $allTeam[$groupSlug];
                if (!is_array($groupMembers) || empty($groupMembers)) continue;
                
                // Get group name from first member or use group slug as fallback
                $groupName = TranslatableFactory::general()->getContent($lang, "team_group_{$groupSlug}", ucfirst(str_replace('_', ' ', $groupSlug)));
                ?>
                
                <div class="team-section">
                    <h2 class="section-title"><?php echo $groupName; ?></h2>
                    <div class="team-grid">
                        <?php foreach ($groupMembers as $index => $member): ?>
                            <?php if (!is_array($member) || !isset($member['name_slug'])) continue; ?>
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

                                        <?php 
                                        // Display credentials or expertise if available
                                        if ($memberTranslatable->hasContent($lang, 'expertise')): 
                                            $expertise = $memberTranslatable->getContent($lang, 'expertise', []);
                                            if (!empty($expertise) && is_array($expertise)):
                                        ?>
                                            <div class="expertise-tags">
                                                <?php foreach ($expertise as $skill): ?>
                                                    <span class="expertise-tag"><?php echo htmlspecialchars($skill); ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php 
                                            endif;
                                        endif; 
                                        ?>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

