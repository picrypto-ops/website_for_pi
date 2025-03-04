<?php
/**
 * Team section rendering for the home page
 */

/**
 * Renders the Team section
 * 
 * @param string $lang Current language code
 * @return void
 */
function renderTeamSection($lang) {
    $teamData = TranslatableFactory::getData('team');
    ?>
    <section id="team" class="our-team-preview full-page-section">
        <div class="container">
            <h2><?php echo TranslatableFactory::general()->getContent($lang, 'our_team', 'Our Team'); ?></h2>
            <p class="section-description"><?php echo TranslatableFactory::general()->getContent($lang, 'team_description', 'Meet our dedicated professionals who provide exceptional service.'); ?></p>

            <div class="team-navigation">
                <ul class="team-nav">
                    <?php
                    // Find all team groups for the navigation
                    $teamGroups = array_keys($teamData);
                    
                    // Display team group links
                    foreach ($teamGroups as $groupKey):
                        if (!is_array($teamData[$groupKey])) continue;
                        
                        $groupLabel = TranslatableFactory::general()->getContent($lang, 'team_group_' . $groupKey, ucfirst(str_replace('_', ' ', $groupKey)));
                    ?>
                        <li><a href="#team-group-<?php echo $groupKey; ?>" class="team-group-link"><?php echo $groupLabel; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <?php
            // Display team members by group without limiting to 4
            foreach ($teamData as $groupKey => $members):
                if (!is_array($members)) continue;
                
                // Get translatable for group label
                $groupLabel = TranslatableFactory::general()->getContent($lang, 'team_group_' . $groupKey, ucfirst(str_replace('_', ' ', $groupKey)));
            ?>
                <div id="team-group-<?php echo $groupKey; ?>" class="team-group">
                    <h3><?php echo $groupLabel; ?></h3>
                    <div class="team-grid">
                        <?php if (empty($members)): ?>
                            <div class="no-data-message">No team members found for this group.</div>
                        <?php else: ?>
                            <?php 
                            foreach ($members as $index => $member): 
                                if (!is_array($member)) continue;
                                
                                // Skip if no name_slug
                                if (!isset($member['name_slug'])) continue;
                                
                                // Get the correct translatable for a team member
                                $memberTranslatable = TranslatableFactory::createFromData($member);
                            ?>
                                <div class="team-card">
                                    <a href="?page=team-member&id=<?php echo $member['name_slug']; ?>&lang=<?php echo $lang; ?>" class="team-member-link">
                                        <div class="member-photo">
                                            <?php if (isset($member['photo']) && !empty($member['photo'])): ?>
                                                <img src="<?php echo $member['photo']; ?>" alt="<?php echo $memberTranslatable->getContent($lang, 'name', $member['name_slug']); ?>">
                                            <?php else: ?>
                                                <div class="photo-placeholder"><i class="fa fa-user-circle"></i></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="member-info">
                                            <h3><?php echo $memberTranslatable->getContent($lang, 'name', 'Team Member'); ?></h3>
                                            <p class="position"><?php echo $memberTranslatable->getContent($lang, 'position', 'Financial Professional'); ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <a href="?page=our-team&lang=<?php echo $lang; ?>" class="button view-all-button">
                <?php echo TranslatableFactory::general()->getContent($lang, 'meet_our_team', 'Meet Our Team'); ?>
            </a>
        </div>
        
        <?php renderScrollIndicator('contact-us', $lang); ?>
    </section>
    
    <!-- Add CSS for the team grid to the page -->
    <style>
        .team-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: flex-start;
        }
        
        .team-card {
            flex: 0 0 calc(25% - 15px); /* Show 4 cards per row */
            margin-bottom: 20px;
            box-sizing: border-box;
        }
        
        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .team-card {
                flex: 0 0 calc(33.33% - 14px); /* 3 per row on medium screens */
            }
        }
        
        @media (max-width: 768px) {
            .team-card {
                flex: 0 0 calc(50% - 10px); /* 2 per row on smaller screens */
            }
        }
        
        @media (max-width: 480px) {
            .team-card {
                flex: 0 0 100%; /* 1 per row on mobile */
            }
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
            background-color: #f5f5f5;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
        }
        
        .team-nav li a:hover, 
        .team-nav li a.active {
            background-color: #0056b3;
            color: white;
        }
        
        .team-member-link {
            display: block;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
        }
        
        .team-member-link:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .team-card {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .team-card:hover {
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
    </style>
    <?php
} 