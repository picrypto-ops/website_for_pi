<?php
/**
 * About section rendering for the home page
 */

/**
 * Renders the About Us section
 * 
 * @param string $lang Current language code
 * @return void
 */
function renderAboutSection($lang) {
    $aboutTranslatable = TranslatableFactory::page('about');
    ?>
    <section id="about-us" class="about-preview full-page-section">
        <div class="container">
            <h2><?php echo $aboutTranslatable->getContent($lang, 'title'); ?></h2>
            <p><?php echo $aboutTranslatable->getContent($lang, 'home_description'); ?></p>
            <a href="?page=about&lang=<?php echo $lang; ?>" class="button">
                <?php 
                    $readMoreText = TranslatableFactory::general()->getContent($lang, 'read_more');
                    echo !empty($readMoreText) ? $readMoreText : 'Read More'; 
                ?>
            </a>
        </div>
        
        <?php renderScrollIndicator('what-we-do', $lang); ?>
    </section>
    <?php
} 