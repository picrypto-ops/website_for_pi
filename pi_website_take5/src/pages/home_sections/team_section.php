<?php
/**
 * Team section rendering for the home page
 */

/**
 * Renders the Team section
 * 
 * @param string $lang Current language code
 * @param string $nextSectionId ID of the next section to scroll to
 * @return void
 */
function renderTeamSection($lang, $nextSectionId = 'about-us') {
    $teamData = TranslatableFactory::getData('team');
    ?>
    <section id="team" class="our-team-preview full-page-section">
        <div class="container">
            <h2><?php echo TranslatableFactory::general()->getContent($lang, 'our_team', 'Our Team'); ?></h2>
            <p class="section-description"><?php echo TranslatableFactory::general()->getContent($lang, 'team_description', 'Meet our dedicated professionals who provide exceptional service.'); ?></p>

            <div class="view-all-link">
                <a href="?page=our-team&lang=<?php echo $lang; ?>" class="button-link">
                    <?php echo TranslatableFactory::general()->getContent($lang, 'view_all_team', 'View All Team Members'); ?>
                </a>
            </div>

            <div class="team-navigation">
                <ul class="team-nav">
                    <?php
                    // Find all team groups for the navigation
                    $teamGroups = array_keys($teamData);
                    
                    // Display team group links
                    foreach ($teamGroups as $groupKey) {
                        if (!is_array($teamData[$groupKey])) continue;
                        
                        $groupLabel = TranslatableFactory::general()->getContent($lang, 'team_group_' . $groupKey, ucfirst(str_replace('_', ' ', $groupKey)));
                    ?>
                        <li><a href="#team-group-<?php echo $groupKey; ?>" class="team-group-link"><?php echo $groupLabel; ?></a></li>
                    <?php } ?>
                </ul>
            </div>
            
            <?php
            // Display team members by group without limiting to 4
            foreach ($teamData as $groupKey => $members) {
                if (!is_array($members)) continue;
                
                // Get translatable for group label
                $groupLabel = TranslatableFactory::general()->getContent($lang, 'team_group_' . $groupKey, ucfirst(str_replace('_', ' ', $groupKey)));
            ?>
                <div id="team-group-<?php echo $groupKey; ?>" class="team-section">
                    <h3 class="section-title"><?php echo $groupLabel; ?></h3>
                    <div class="team-grid">
                        <?php if (empty($members)) { ?>
                            <div class="no-data-message">No team members found for this group.</div>
                        <?php } else { ?>
                            <?php 
                            foreach ($members as $index => $member) { 
                                if (!is_array($member)) continue;
                                
                                // Skip if no name_slug
                                if (!isset($member['name_slug'])) continue;
                                
                                // Get the correct translatable for a team member
                                $memberTranslatable = TranslatableFactory::teamMemberEnhanced($member['name_slug']);
                            ?>
                                <a href="?page=team-member&id=<?php echo $member['name_slug']; ?>&lang=<?php echo $lang; ?>" class="card-link">
                                    <div class="team-card">
                                        <div class="team-info">
                                            <h3><?php echo $memberTranslatable->getContent($lang, 'name', 'Team Member'); ?></h3>
                                            <?php
                                            // Check if this member should display product roles
                                            $displayProductRoles = $memberTranslatable->getContent($lang, 'display_product_roles', false);
                                            
                                            if ($displayProductRoles) {
                                                // Get position which will be an array of roles when display_product_roles is true
                                                $positions = $memberTranslatable->getContent($lang, 'position', []);
                                                
                                                if (is_array($positions) && !empty($positions)) {
                                            ?>
                                                <div class="product-roles">
                                                    <?php foreach ($positions as $role) { ?>
                                                        <div class="role">
                                                            <?php 
                                                            $roleTitle = isset($role['title']) ? $role['title'] : '';
                                                            $productName = isset($role['product_name']) ? $role['product_name'] : '';
                                                            
                                                            if (!empty($productName)) {
                                                                echo '<span class="product-name">' . htmlspecialchars($productName) . '</span>';
                                                            }
                                                            
                                                            if (!empty($roleTitle)) {
                                                                echo '<span class="role-title">' . htmlspecialchars($roleTitle) . '</span>';
                                                            }
                                                            ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            <?php } else { ?>
                                                <p class="position"><?php echo $memberTranslatable->getContent($lang, 'position', 'Financial Professional'); ?></p>
                                            <?php } ?>
                                            <?php } else { ?>
                                                <p class="position"><?php echo $memberTranslatable->getContent($lang, 'position', 'Financial Professional'); ?></p>
                                            <?php } ?>
                                            

                                        </div>
                                    </div>
                                </a>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            
            <div class="view-all-container">
                <a href="?page=our-team&lang=<?php echo $lang; ?>" class="view-all-button">
                    <?php echo TranslatableFactory::general()->getContent($lang, 'meet_our_team', 'Meet Our Team'); ?>
                </a>
            </div>
        </div>
        
        <?php renderScrollIndicator($nextSectionId, $lang, 'team'); ?>
    </section>
    
    <!-- Add additional CSS for the team grid to the page -->
    <style>
        .our-team-preview .section-title {
            position: relative;
            font-size: 1.8rem;
            color: #1a3a5f;
            margin-bottom: 25px;
            padding-bottom: 12px;
            font-weight: 600;
        }
        
        .our-team-preview .section-title::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            width: 60px;
            background: linear-gradient(90deg, #0056b3, #007bff);
        }
        
        .our-team-preview .section-description {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #4a4a4a;
            margin-bottom: 30px;
            max-width: 800px;
        }
        
        .team-navigation {
            margin-bottom: 2rem;
        }
        
        .team-nav {
            display: flex;
            list-style: none;
            padding: 0;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .team-nav li a {
            display: inline-block;
            padding: 8px 15px;
            background-color: #f8fafc;
            border-radius: 4px;
            text-decoration: none;
            color: #1a3a5f;
            transition: all 0.3s ease;
            font-weight: 500;
            border: 1px solid #e2e8f0;
        }
        
        .team-nav li a:hover, 
        .team-nav li a.active {
            background-color: #0056b3;
            color: white;
            border-color: #0056b3;
        }
        
        .view-all-container {
            margin-top: 40px;
            text-align: center;
        }
        
        .view-all-button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #0056b3;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
        }
        
        .view-all-button:hover {
            background-color: #003d80;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
    <?php
} 