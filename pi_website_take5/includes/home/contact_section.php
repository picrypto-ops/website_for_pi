<?php
/**
 * Contact section rendering for the home page
 */

/**
 * Renders the Contact section
 * 
 * @param string $lang Current language code
 * @return void
 */
function renderContactSection($lang) {
    $contactPage = TranslatableFactory::page('contact');
    ?>
    <section id="contact-us" class="contact-preview full-page-section">
        <div class="container">
            <div class="section-header">
                <h2><?php echo $contactPage->getContent($lang, 'title'); ?></h2>
                <p class="section-description"><?php echo $contactPage->getContent($lang, 'home_description'); ?></p>
            </div>
            
            <div class="contact-info-grid">
                <?php if (!empty($contactPage->getContent($lang, 'address'))): ?>
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fa fa-map-marker-alt"></i>
                    </div>
                    <div class="contact-details">
                        <h3><?php echo TranslatableFactory::general()->getContent($lang, 'address') ?: 'Address'; ?></h3>
                        <p><?php echo $contactPage->getContent($lang, 'address'); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($contactPage->getContent($lang, 'phone'))): ?>
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fa fa-phone"></i>
                    </div>
                    <div class="contact-details">
                        <h3><?php echo TranslatableFactory::general()->getContent($lang, 'phone') ?: 'Phone'; ?></h3>
                        <p><?php echo $contactPage->getContent($lang, 'phone'); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($contactPage->getContent($lang, 'email'))): ?>
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fa fa-envelope"></i>
                    </div>
                    <div class="contact-details">
                        <h3><?php echo TranslatableFactory::general()->getContent($lang, 'email') ?: 'Email'; ?></h3>
                        <p><?php echo $contactPage->getContent($lang, 'email') ?: 'info@pigroup.com'; ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <a href="?page=contact&lang=<?php echo $lang; ?>" class="button view-all-button">
                <?php echo TranslatableFactory::general()->getContent($lang, 'get_in_touch') ?: 'Get In Touch'; ?>
            </a>
        </div>
    </section>
    <?php
} 