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
    <div class="container">
        <div class="navigation-breadcrumb<?php echo $isRTL ? ' rtl' : ''; ?>">
            <a href="?page=our-team&lang=<?php echo $lang; ?>" class="back-link">
                <span class="back-arrow">←</span> 
                <?php echo TranslatableFactory::general()->getContent($lang, 'back_to_team', 'Back to Team'); ?>
            </a>
        </div>
        
        <div class="member-container<?php echo $isRTL ? ' rtl' : ''; ?>">
            <div class="member-profile-card">
                <div class="member-header">
                    <div class="member-title-section">
                        <h1 class="member-name"><?php echo $memberTranslatable->getContent($lang, 'name', $member['name_slug']); ?></h1>
                        
                        <?php
                        // Check if this member should display product roles
                        $displayProductRoles = $memberTranslatable->getContent($lang, 'display_product_roles', false);
                        
                        if ($displayProductRoles) {
                            // Get position which will be an array of roles when display_product_roles is true
                            $positions = $memberTranslatable->getContent($lang, 'position', []);
                            
                            if (is_array($positions) && !empty($positions)) {
                                $firstRole = reset($positions);
                                $primaryRoleTitle = isset($firstRole['title']) ? $firstRole['title'] : '';
                                $primaryProductName = isset($firstRole['product_name']) ? $firstRole['product_name'] : '';
                                
                                if (!empty($primaryRoleTitle) && !empty($primaryProductName)) {
                                    echo '<p class="primary-role">' . htmlspecialchars($primaryRoleTitle) . ' - ' . htmlspecialchars($primaryProductName) . '</p>';
                                } elseif (!empty($primaryRoleTitle)) {
                                    echo '<p class="primary-role">' . htmlspecialchars($primaryRoleTitle) . '</p>';
                                } elseif (!empty($primaryProductName)) {
                                    echo '<p class="primary-role">' . htmlspecialchars($primaryProductName) . '</p>';
                                }
                            } else {
                                echo '<p class="primary-role">' . $memberTranslatable->getContent($lang, 'position', 'Team Member') . '</p>';
                            }
                        } else {
                            echo '<p class="primary-role">' . $memberTranslatable->getContent($lang, 'position', 'Team Member') . '</p>';
                        }
                        ?>
                    </div>
                </div>
                
                <div class="member-content">
                    <div class="member-bio">
                        <?php echo $memberTranslatable->getContent($lang, 'bio', 'Biography information not available.'); ?>
                    </div>
                    
                    <?php if ($displayProductRoles && is_array($positions) && count($positions) > 1) { ?>
                    <div class="member-roles-section">
                        <h3 class="section-subtitle"><?php echo TranslatableFactory::general()->getContent($lang, 'member_roles', 'Roles'); ?></h3>
                        <div class="product-roles">
                            <?php foreach ($positions as $index => $role) { 
                                if ($index === 0) continue; // Skip first role which is already displayed
                            ?>
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
                    </div>
                    <?php } ?>
                    
                    <?php 
                    // Display expertise/skills if available
                    if ($memberTranslatable->hasContent($lang, 'expertise')) { 
                        $expertise = $memberTranslatable->getContent($lang, 'expertise', []);
                        if (!empty($expertise) && is_array($expertise)) {
                    ?>
                    <div class="member-expertise-section">
                        <h3 class="section-subtitle"><?php echo TranslatableFactory::general()->getContent($lang, 'expertise', 'Areas of Expertise'); ?></h3>
                        <div class="expertise-tags">
                            <?php foreach ($expertise as $skill) { ?>
                                <span class="expertise-tag"><?php echo htmlspecialchars($skill); ?></span>
                            <?php } ?>
                        </div>
                    </div>
                    <?php 
                        }
                    } 
                    ?>
                    
                    <?php if (isset($member['credentials']) && is_array($member['credentials']) && !empty($member['credentials'])) { ?>
                    <div class="member-credentials-section">
                        <h3 class="section-subtitle"><?php echo TranslatableFactory::general()->getContent($lang, 'credentials', 'Credentials'); ?></h3>
                        <div class="credentials-list">
                            <?php foreach ($member['credentials'] as $credential => $value) { ?>
                            <div class="credential-item">
                                <div class="credential-label"><?php echo str_replace('_', ' ', $credential); ?>:</div>
                                <div class="credential-value"><?php echo $value; ?></div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            
            <div class="member-navigation<?php echo $isFirst ? ' first-member' : ''; ?><?php echo $isLast ? ' last-member' : ''; ?>">
                <?php if (!$isFirst) { ?>
                <a href="?page=team-member&id=<?php echo $prevMemberId; ?>&lang=<?php echo $lang; ?>" class="navigation-button prev-button" data-tooltip="<?php echo TranslatableFactory::general()->getContent($lang, 'previous', 'Previous'); ?>">
                    <span class="nav-icon">←</span>
                    <span class="nav-text"><?php echo TranslatableFactory::general()->getContent($lang, 'previous', 'Previous'); ?></span>
                </a>
                <?php } else { ?>
                <div class="navigation-placeholder"></div>
                <?php } ?>
                
                <?php if (!$isLast) { ?>
                <a href="?page=team-member&id=<?php echo $nextMemberId; ?>&lang=<?php echo $lang; ?>" class="navigation-button next-button" data-tooltip="<?php echo TranslatableFactory::general()->getContent($lang, 'next', 'Next'); ?>">
                    <span class="nav-text"><?php echo TranslatableFactory::general()->getContent($lang, 'next', 'Next'); ?></span>
                    <span class="nav-icon">→</span>
                </a>
                <?php } else { ?>
                <div class="navigation-placeholder"></div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>

