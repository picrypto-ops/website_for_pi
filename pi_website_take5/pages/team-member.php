<?php
$memberId = isset($_GET['id']) ? $_GET['id'] : '';
$allTeam = TranslatableFactory::getData('team');

$member = null;
$teamGroup = null;

// Search for the member in the nested team structure
if (!empty($memberId) && is_array($allTeam)) {
    foreach ($allTeam as $groupSlug => $groupMembers) {
        if (!is_array($groupMembers)) continue;
        
        foreach ($groupMembers as $index => $potentialMember) {
            if (is_array($potentialMember) && isset($potentialMember['name_slug']) && $potentialMember['name_slug'] === $memberId) {
                $member = $potentialMember;
                $teamGroup = $groupSlug;
                break 2; // Break from both loops
            }
        }
    }
}

if (!$member) {
    include '404.php';
    exit;
}

// Get translatable for this team member
$memberTranslatable = TranslatableFactory::teamMemberEnhanced($memberId);

// Create a flat array of all team members from all groups
$allMembers = [];
foreach ($allTeam as $groupMembers) {
    if (!is_array($groupMembers)) continue;
    
    foreach ($groupMembers as $m) {
        if (is_array($m) && isset($m['name_slug'])) {
            $allMembers[] = $m;
        }
    }
}

// Find current index in the flat array of all members
$currentIndex = -1;
foreach ($allMembers as $index => $teamMember) {
    if ($teamMember['name_slug'] === $memberId) {
        $currentIndex = $index;
        break;
    }
}

// Check if current member is first or last
$isFirst = ($currentIndex === 0);
$isLast = ($currentIndex === count($allMembers) - 1);

// Get previous and next member IDs only if not first/last
$prevMember = !$isFirst ? $allMembers[$currentIndex - 1] : null;
$nextMember = !$isLast ? $allMembers[$currentIndex + 1] : null;
$prevMemberId = $prevMember ? $prevMember['name_slug'] : '';
$nextMemberId = $nextMember ? $nextMember['name_slug'] : '';

// Set page data for structured data
$pageData = $member;

// Check if the language is Hebrew (RTL)
$isRTL = ($lang === 'he');

// Get arrow from translations
$arrowChar = TranslatableFactory::general()->getContent($lang, 'arrow', '»');
?>


<section class="team-member-page">
    <div class="member-container<?php echo $isRTL ? ' rtl' : ''; ?>">
        <?php if (!$isFirst): ?>
            <a href="?page=team-member&id=<?php echo $prevMemberId; ?>&lang=<?php echo $lang; ?>" class="member-navigation-button prev-button" title="<?php echo TranslatableFactory::general()->getContent($lang, 'previous_member', 'Previous Member'); ?>">
                <span class="arrow-icon"><?php echo $arrowChar; ?></span>
            </a>
        <?php endif; ?>
        
        <div class="member-profile professional-card">
            <div class="member-image-container">
                <?php if (isset($member['photo']) && !empty($member['photo'])): ?>
                    <img src="<?php echo $member['photo']; ?>" alt="<?php echo $memberTranslatable->getContent($lang, 'name', $member['name_slug']); ?>" class="member-image">
                <?php else: ?>
                    <div class="photo-placeholder"><i class="fa fa-user-circle"></i></div>
                <?php endif; ?>
            </div>
            
            <div class="member-info">
                <h1><?php echo $memberTranslatable->getContent($lang, 'name', $member['name_slug']); ?></h1>
                
                <?php
                // Check if this member should display product roles
                $displayProductRoles = $memberTranslatable->getContent($lang, 'display_product_roles', false);
                
                if ($displayProductRoles):
                    // Get position which will be an array of roles when display_product_roles is true
                    $positions = $memberTranslatable->getContent($lang, 'position', []);
                    
                    if (is_array($positions) && !empty($positions)):
                ?>
                    <div class="product-roles-section">
                        <h3><?php echo TranslatableFactory::general()->getContent($lang, 'member_roles', 'Roles'); ?></h3>
                        <div class="product-roles">
                            <?php foreach ($positions as $role): ?>
                                <div class="role">
                                    <?php 
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
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="member-title"><?php echo $memberTranslatable->getContent($lang, 'position', 'Team Member'); ?></p>
                <?php endif; ?>
                <?php else: ?>
                    <p class="member-title"><?php echo $memberTranslatable->getContent($lang, 'position', 'Team Member'); ?></p>
                <?php endif; ?>
                
                <div class="member-bio">
                    <?php echo $memberTranslatable->getContent($lang, 'bio', 'Biography information not available.'); ?>
                </div>
                
                <?php if (isset($member['credentials']) && is_array($member['credentials'])): ?>
                <div class="team-credentials">
                    <?php foreach ($member['credentials'] as $credential => $value): ?>
                    <div class="credential-item">
                        <div class="credential-label"><?php echo ucfirst(str_replace('_', ' ', $credential)); ?>:</div>
                        <div class="credential-value"><?php echo $value; ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (!$isLast): ?>
            <a href="?page=team-member&id=<?php echo $nextMemberId; ?>&lang=<?php echo $lang; ?>" class="member-navigation-button next-button" title="<?php echo TranslatableFactory::general()->getContent($lang, 'next_member', 'Next Member'); ?>">
                <span class="arrow-icon"><?php echo $arrowChar; ?></span>
            </a>
        <?php endif; ?>
    </div>
</section>

