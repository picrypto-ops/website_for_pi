<?php
/**
 * 404 error page
 */

// Get translatable for this page
$pageTranslatable = TranslatableFactory::general();
?>

<div class="error-page">
    <div class="container mx-auto py-20 px-6">
        <div class="text-center">
            <h1 class="text-4xl font-bold mb-6"><?php echo $pageTranslatable->getContent($lang, 'page_not_found_title', '404 - Page Not Found'); ?></h1>
            <p class="text-xl mb-8"><?php echo $pageTranslatable->getContent($lang, 'page_not_found_message', 'The page you are looking for does not exist or is no longer available.'); ?></p>
            <a href="<?php echo buildUrl('home'); ?>" class="inline-block bg-primary text-white font-bold py-3 px-6 rounded-lg hover:bg-primary-dark transition duration-300">
                <?php echo $pageTranslatable->getContent($lang, 'back_home', 'Back to Home'); ?>
            </a>
        </div>
    </div>
</div> 