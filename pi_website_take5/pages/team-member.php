<?php
$memberId = isset($_GET['id']) ? $_GET['id'] : '';
$team = loadJsonData('team');

$member = null;
foreach ($team as $m) {
    if ($m['id'] === $memberId) {
        $member = $m;
        break;
    }
}

if (!$member) {
    include 'pages/404.php';
    exit;
}

$currentIndex = array_search($member, $team);
$prevMember = $currentIndex > 0 ? $team[$currentIndex - 1] : end($team);
$nextMember = $currentIndex < count($team) - 1 ? $team[$currentIndex + 1] : reset($team);

// Set page data for structured data
$pageData = $member;
?>

<section class="team-member">
    <div class="container">
        <div class="member-profile">
            <?php echo responsiveImage("assets/images/team/{$member['image']}", $member['name'], "member-image"); ?>
            <div class="member-info">
                <h1><?php echo $member['name']; ?></h1>
                <p class="member-title"><?php echo $member['title']; ?></p>
                <div class="member-bio">
                    <?php echo t($member['long_bio']); ?>
                </div>
            </div>
        </div>

        <div class="member-navigation">
            <a href="?page=team-member&id=<?php echo $prevMember['id']; ?>&lang=<?php echo $lang; ?>" class="prev-member">&larr; <?php echo t('previous_member'); ?></a>
            <a href="?page=team-member&id=<?php echo $nextMember['id']; ?>&lang=<?php echo $lang; ?>" class="next-member"><?php echo t('next_member'); ?> &rarr;</a>
        </div>
    </div>
</section>

