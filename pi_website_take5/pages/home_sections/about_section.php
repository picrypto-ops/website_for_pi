<?php
/**
 * About section rendering for the home page
 */

/**
 * Renders the About Us section
 * 
 * @param string $lang Current language code
 * @param string $nextSectionId ID of the next section to scroll to
 * @return void
 */
function renderAboutSection($lang, $nextSectionId = 'contact-us') {
    $aboutTranslatable = TranslatableFactory::page('about');
    ?>
    <section id="about-us" class="about-preview full-page-section">
        <div class="container">
            <h2><?php echo $aboutTranslatable->getContent($lang, 'title', 'About Us'); ?></h2>
            <p><?php echo $aboutTranslatable->getContent($lang, 'home_description', 'Learn more about our company and our mission.'); ?></p>
            <a href="?page=about&lang=<?php echo $lang; ?>" class="button">
                <?php echo TranslatableFactory::general()->getContent($lang, 'read_more', 'Read More...'); ?>
            </a>
        </div>
        
        <?php renderScrollIndicator($nextSectionId, $lang, 'about-us'); ?>
    </section>
    <?php
} 