<?php
$memberId = isset($_GET['id']) ? $_GET['id'] : '';
$allTeam = TranslatableFactory::getData('team');

$member = null;
$teamGroup = null;

// Search for the member in the nested team structure
if (!empty($memberId) && is_array($allTeam)) {
    foreach ($allTeam as $groupSlug => $groupMembers) {
        if (!is_array($groupMembers)) continue;
        
        if (isset($groupMembers[$memberId])) {
            $member = $groupMembers[$memberId];
            $teamGroup = $groupSlug;
            break;
        }
    }
}

if (!$member) {
    include 'pages/404.php';
    exit;
}

// Get translatable for this team member
$memberTranslatable = TranslatableFactory::teamMember($memberId);

// Find previous and next members in the same group
$groupMembers = array_keys($allTeam[$teamGroup]);
$currentIndex = array_search($memberId, $groupMembers);
$prevMemberId = $currentIndex > 0 ? $groupMembers[$currentIndex - 1] : end($groupMembers);
$nextMemberId = $currentIndex < count($groupMembers) - 1 ? $groupMembers[$currentIndex + 1] : reset($groupMembers);

// Set page data for structured data
$pageData = $member;
?>

<section class="team-member">
    <div class="container">
        <div class="member-profile">
            <?php if (isset($member['photo']) && !empty($member['photo'])): ?>
                <img src="<?php echo $member['photo']; ?>" alt="<?php echo $memberTranslatable->getContent($lang, 'name', $member['name_slug']); ?>" class="member-image">
            <?php else: ?>
                <div class="photo-placeholder"><i class="fa fa-user-circle"></i></div>
            <?php endif; ?>
            
            <div class="member-info">
                <h1><?php echo $memberTranslatable->getContent($lang, 'name', $member['name_slug']); ?></h1>
                <p class="member-title"><?php echo $memberTranslatable->getContent($lang, 'position', 'Team Member'); ?></p>
                <div class="member-bio">
                    <?php echo $memberTranslatable->getContent($lang, 'bio', 'Biography information not available.'); ?>
                </div>
            </div>
        </div>

        <div class="member-navigation">
            <a href="?page=team-member&id=<?php echo $prevMemberId; ?>&lang=<?php echo $lang; ?>" class="prev-member">&larr; <?php echo TranslatableFactory::general()->getContent($lang, 'previous_member', 'Previous Member'); ?></a>
            <a href="?page=team-member&id=<?php echo $nextMemberId; ?>&lang=<?php echo $lang; ?>" class="next-member"><?php echo TranslatableFactory::general()->getContent($lang, 'next_member', 'Next Member'); ?> &rarr;</a>
        </div>
    </div>
</section>

