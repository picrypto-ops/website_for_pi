<?php
$team = loadJsonData('team');
$groups = array_unique(array_merge(...array_column($team, 'groups')));
?>

<section class="our-team">
    <div class="container">
        <h1><?php echo t('our_team_title'); ?></h1>
        <p class="lead"><?php echo t('our_team_description'); ?></p>

        <?php foreach ($groups as $group): ?>
            <h2><?php echo t($group); ?></h2>
            <div class="team-grid">
                <?php
                $groupMembers = array_filter($team, function($member) use ($group) {
                    return in_array($group, $member['groups']);
                });
                foreach ($groupMembers as $member):
                ?>
                    <div class="team-card">
                        <img src="assets/images/team/<?php echo $member['image']; ?>" alt="<?php echo $member['name']; ?>">
                        <h3><?php echo $member['name']; ?></h3>
                        <p><?php echo $member['title']; ?></p>
                        <p><?php echo t($member['short_bio']); ?></p>
                        <a href="?page=team-member&id=<?php echo $member['id']; ?>&lang=<?php echo $lang; ?>" class="button"><?php echo t('view_profile'); ?></a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>

