<?php
/**
 * 404 error page
 */

// Get translatable for this page
$pageTranslatable = TranslatableFactory::general();

// Check if the user tried to access a disallowed page
$originalPage = isset($_GET['original']) ? sanitizeInput($_GET['original']) : '';
$isRestricted = !empty($originalPage);
?>

<div class="error-page">
    <div class="container mx-auto py-20 px-6">
        <div class="text-center">
            <h1 class="text-4xl font-bold mb-6"><?php echo $pageTranslatable->getContent($lang, 'page_not_found_title', '404 - Page Not Found'); ?></h1>
            
            <?php if ($isRestricted): ?>
                <p class="text-xl mb-4"><?php echo $pageTranslatable->getContent($lang, 'page_restricted_message', 'Access to the requested page is not allowed.'); ?></p>
                <p class="mb-8 text-gray-700"><?php echo sprintf($pageTranslatable->getContent($lang, 'page_restricted_detail', 'The page "%s" is either restricted or does not exist.'), htmlspecialchars($originalPage)); ?></p>
            <?php else: ?>
                <p class="text-xl mb-8"><?php echo $pageTranslatable->getContent($lang, 'page_not_found_message', 'The page you are looking for does not exist or is no longer available.'); ?></p>
            <?php endif; ?>
            
            <a href="<?php echo buildUrl('home'); ?>" class="inline-block bg-primary text-white font-bold py-3 px-6 rounded-lg hover:bg-primary-dark transition duration-300">
                <?php echo $pageTranslatable->getContent($lang, 'back_home', 'Back to Home'); ?>
            </a>
        </div>
    </div>
</div> 