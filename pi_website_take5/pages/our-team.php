<?php
$allTeam = TranslatableFactory::getData('team');

// Get all team groups
$teamGroups = array_keys($allTeam);
?>

<section class="our-team">
    <div class="container">
        <h1><?php echo TranslatableFactory::general()->getContent($lang, 'our_team_title', 'Our Team'); ?></h1>
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
                
                <div class="team-group">
                    <h2><?php echo $groupName; ?></h2>
                    <div class="team-grid">
                        <?php foreach ($groupMembers as $memberSlug => $member): ?>
                            <?php $memberTranslatable = TranslatableFactory::teamMember($memberSlug); ?>
                            <a href="?page=team-member&id=<?php echo $memberSlug; ?>&lang=<?php echo $lang; ?>" class="card-link">
                                <div class="team-card">
                                    <?php if (isset($member['photo']) && !empty($member['photo'])): ?>
                                        <img src="<?php echo $member['photo']; ?>" alt="<?php echo $memberTranslatable->getContent($lang, 'name', $memberSlug); ?>">
                                    <?php else: ?>
                                        <div class="photo-placeholder"><i class="fa fa-user-circle"></i></div>
                                    <?php endif; ?>
                                    <div class="team-info">
                                        <h3><?php echo $memberTranslatable->getContent($lang, 'name', $memberSlug); ?></h3>
                                        <p class="position"><?php echo $memberTranslatable->getContent($lang, 'position', 'Team Member'); ?></p>
                                        <?php if ($memberTranslatable->hasContent($lang, 'short_bio')): ?>
                                            <p class="short-bio"><?php echo $memberTranslatable->getContent($lang, 'short_bio', ''); ?></p>
                                        <?php endif; ?>
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

