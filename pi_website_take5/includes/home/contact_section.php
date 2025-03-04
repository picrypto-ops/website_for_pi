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
                <h2><?php echo $contactPage->getContent($lang, 'title', 'Contact Us'); ?></h2>
                <p class="section-description"><?php echo $contactPage->getContent($lang, 'home_description', 'Get in touch with our team.'); ?></p>
            </div>
            
            <div class="contact-info-grid">
                <?php 
                // Access the contact details from the contact_details in pages.json
                $contactDetails = $contactPage->getContent($lang, 'contact_details', []);
                if (is_array($contactDetails)):
                
                    // Address card
                    if (!empty($contactDetails['address'])): 
                ?>
                <div class="contact-card address">
                    <div class="icon"><i class="fa fa-map-marker-alt"></i></div>
                    <div class="contact-info">
                        <h3><?php echo TranslatableFactory::general()->getContent($lang, 'address', 'Address'); ?></h3>
                        <p><?php echo $contactDetails['address']; ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Phone card -->
                <?php if (!empty($contactDetails['phone'])): ?>
                    <div class="contact-card phone">
                        <div class="icon"><i class="fa fa-phone"></i></div>
                        <div class="contact-info">
                            <h3><?php echo TranslatableFactory::general()->getContent($lang, 'phone', 'Phone'); ?></h3>
                            <p><?php echo $contactDetails['phone']; ?></p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Email card -->
                <?php if (!empty($contactDetails['email'])): ?>
                    <div class="contact-card email">
                        <div class="icon"><i class="fa fa-envelope"></i></div>
                        <div class="contact-info">
                            <h3><?php echo TranslatableFactory::general()->getContent($lang, 'email', 'Email'); ?></h3>
                            <p><a href="mailto:<?php echo $contactDetails['email']; ?>"><?php echo $contactDetails['email']; ?></a></p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Website card -->
                <?php if (!empty($contactDetails['website'])): ?>
                    <div class="contact-card website">
                        <div class="icon"><i class="fa fa-globe"></i></div>
                        <div class="contact-info">
                            <h3><?php echo TranslatableFactory::general()->getContent($lang, 'website', 'Website'); ?></h3>
                            <p><a href="<?php echo $contactDetails['website']; ?>" target="_blank"><?php echo $contactDetails['website']; ?></a></p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php endif; ?>
            </div>
            
            <a href="?page=contact&lang=<?php echo $lang; ?>" class="button contact-button">
                <?php echo TranslatableFactory::general()->getContent($lang, 'get_in_touch', 'Get In Touch'); ?>
            </a>
        </div>
    </section>
    <?php
} 