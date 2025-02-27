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
            <h2><?php echo TranslatableFactory::general()->getContent($lang, 'our_team') ?: 'Our Team'; ?></h2>
            <p class="section-description"><?php echo TranslatableFactory::general()->getContent($lang, 'team_description') ?: 'Meet our dedicated professionals who provide exceptional service.'; ?></p>

            <div class="breadcrumb-nav">
                <ul class="team-nav">
                    <?php
                    // Find all team groups for the breadcrumb
                    $teamGroups = array_keys($teamData);
                    
                    // Display team group links
                    foreach ($teamGroups as $groupKey):
                        if (!is_array($teamData[$groupKey])) continue;
                        
                        $groupLabel = TranslatableFactory::general()->getContent($lang, 'team_group_' . $groupKey) ?: 
                            ucfirst(str_replace('_', ' ', $groupKey));
                    ?>
                        <li><a href="#team-group-<?php echo $groupKey; ?>"><?php echo $groupLabel; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <?php
            // Collect all featured team members
            $featuredTeamMembers = [];
            
            // Process all team groups
            foreach ($teamData as $groupKey => $members) {
                if (!is_array($members)) continue;
                
                foreach ($members as $member) {
                    if (!is_array($member)) continue;
                    
                    // Check for featured team members
                    if (isset($member['display_in_home_page']) && $member['display_in_home_page'] === true) {
                        if (!isset($member['name_slug'])) continue;
                        $featuredTeamMembers[] = $member;
                    }
                }
            }
            
            // If we have featured team members, show them first
            if (!empty($featuredTeamMembers)) {
                echo '<div class="team-group featured-team">';
                echo '<h3>' . (TranslatableFactory::general()->getContent($lang, 'featured_team_members') ?: 'Featured Team Members') . '</h3>';
                echo '<div class="team-preview-grid">';
                
                foreach ($featuredTeamMembers as $member) {
                    $memberSlug = $member['name_slug'];
                    $memberTranslatable = TranslatableFactory::createFromData($member);
                    ?>
                    <div class="team-card">
                        <div class="member-photo">
                            <?php if (isset($member['photo']) && !empty($member['photo'])): ?>
                                <img src="<?php echo $member['photo']; ?>" alt="<?php echo $memberTranslatable->getContent($lang, 'name'); ?>">
                            <?php else: ?>
                                <div class="photo-placeholder"><i class="fa fa-user"></i></div>
                            <?php endif; ?>
                        </div>
                        <div class="member-info">
                            <h3><?php echo $memberTranslatable->getContent($lang, 'name') ?: 'Team Member'; ?></h3>
                            <p class="position"><?php echo $memberTranslatable->getContent($lang, 'position') ?: 'Financial Professional'; ?></p>
                        </div>
                    </div>
                    <?php
                }
                
                echo '</div></div>';
            }
            
            // Display team members by group
            foreach ($teamData as $groupKey => $members):
                if (!is_array($members)) continue;
                
                // Get translatable for group label
                $groupLabel = TranslatableFactory::general()->getContent($lang, 'team_group_' . $groupKey) ?: 
                    ucfirst(str_replace('_', ' ', $groupKey));
            ?>
                <div id="team-group-<?php echo $groupKey; ?>" class="team-group">
                    <h3><?php echo $groupLabel; ?></h3>
                    <div class="team-preview-grid">
                        <?php if (empty($members)): ?>
                            <div class="no-data-message">No team members found for this group.</div>
                        <?php else: ?>
                            <?php 
                            $displayedCount = 0;
                            foreach ($members as $index => $member): 
                                if (!is_array($member)) continue;
                                
                                // Limit to first 4 members per group on homepage
                                if ($displayedCount >= 4) break;
                                
                                // Skip if no name_slug
                                if (!isset($member['name_slug'])) continue;
                                
                                $displayedCount++;
                                
                                // Get the correct translatable for a team member
                                $memberTranslatable = TranslatableFactory::createFromData($member);
                            ?>
                                <div class="team-card">
                                    <div class="member-photo">
                                        <?php if (isset($member['photo']) && !empty($member['photo'])): ?>
                                            <img src="<?php echo $member['photo']; ?>" alt="<?php echo $memberTranslatable->getContent($lang, 'name'); ?>">
                                        <?php else: ?>
                                            <div class="photo-placeholder"><i class="fa fa-user"></i></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="member-info">
                                        <h3><?php echo $memberTranslatable->getContent($lang, 'name') ?: 'Team Member'; ?></h3>
                                        <p class="position"><?php echo $memberTranslatable->getContent($lang, 'position') ?: 'Financial Professional'; ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <a href="?page=our-team&lang=<?php echo $lang; ?>" class="button view-all-button">
                <?php echo TranslatableFactory::general()->getContent($lang, 'meet_our_team') ?: 'Meet Our Team'; ?>
            </a>
        </div>
        
        <?php renderScrollIndicator('contact-us', $lang); ?>
    </section>
    <?php
} 