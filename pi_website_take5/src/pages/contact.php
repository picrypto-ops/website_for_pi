<?php
// Initialize the factory and preload common data files if not already done
$Pages = TranslatableFactory::getData('pages');

$contactTranslatable = null;
if (!empty($Pages) && isset($Pages['contact'])) {
    // Get contact page translatable
    $contactTranslatable = TranslatableFactory::page('contact');
} else {
    // No valid segment ID found
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 - Page Not Found</h1>";
    echo "<p>The requested page does not exist.</p>";
    echo "<p><a href='index.php'>Return to homepage</a></p>";
    exit;
}

// Get current language from the global variable
global $lang;

// Get contact details from the translatable object
$contactDetails = $contactTranslatable->getContent($lang, 'contact_details', []);
if (!is_array($contactDetails)) {
    $contactDetails = json_decode($contactDetails, true);
}

// Check for form submission response messages
$formResponse = null;
if (isset($_SESSION['contact_form_response'])) {
    $formResponse = $_SESSION['contact_form_response'];
    unset($_SESSION['contact_form_response']); // Clear after use
}
?>

<section class="contact-hero">
    <div class="container">
        <h1><?php echo $contactTranslatable->getContent($lang, 'title'); ?></h1>
        <p class="slogan"><?php echo $contactTranslatable->getContent($lang, 'slogan'); ?></p>
        <p class="lead"><?php echo $contactTranslatable->getContent($lang, 'short_description'); ?></p>
    </div>
</section>

<section class="contact-us">
    <div class="container">
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="form-message success">
            <i class="fa fa-check-circle"></i>
            <p><?php echo $formResponse['message'] ?? $contactTranslatable->getContent($lang, 'contact_success_message', 'Thank you for your message. We will contact you shortly.'); ?></p>
        </div>
        <?php elseif (isset($_GET['error']) && $_GET['error'] == 1): ?>
        <div class="form-message error">
            <i class="fa fa-exclamation-circle"></i>
            <p><?php echo $formResponse['message'] ?? $contactTranslatable->getContent($lang, 'contact_error_message', 'There was a problem sending your message. Please try again.'); ?></p>
        </div>
        <?php endif; ?>
        
        <div class="contact-content">
            <!-- Form on the left for desktop, last for mobile -->
            <div class="contact-form contact-column">
                <h2><?php echo $contactTranslatable->getContent($lang, 'form_heading', 'Send Us a Message'); ?></h2>
                <form id="contact-form" action="../utility/process-contact.php" method="POST">
                    <div class="form-group">
                        <label for="name"><?php echo $contactTranslatable->getContent($lang, 'name_label', 'Name'); ?></label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email"><?php echo $contactTranslatable->getContent($lang, 'email_label', 'Email'); ?></label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="subject"><?php echo $contactTranslatable->getContent($lang, 'subject_label', 'Subject'); ?></label>
                        <input type="text" id="subject" name="subject" required>
                    </div>
                    <div class="form-group">
                        <label for="message"><?php echo $contactTranslatable->getContent($lang, 'message_label', 'Message'); ?></label>
                        <textarea id="message" name="message" required></textarea>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="button button--outline"><?php echo $contactTranslatable->getContent($lang, 'submit_button', 'Send Message'); ?></button>
                    </div>
                </form>
            </div>
            
            <!-- Map and contact info on the right for desktop, first for mobile -->
            <div class="contact-info-map contact-column">
                <!-- Map section -->
                <div class="map-section">
                    <h2><?php echo $contactTranslatable->getContent($lang, 'our_info_map_label', 'Map'); ?></h2>
                    <div id="map" 
                         class="contact-map"
                         data-lat="<?php echo $contactDetails['lat'] ?? $contactDetails['default_lat'] ?? 32.109608; ?>"
                         data-lng="<?php echo $contactDetails['lng'] ?? $contactDetails['default_lng'] ?? 34.837384; ?>"
                         data-address="<?php echo htmlspecialchars($contactDetails['address'] ?? $contactDetails['default_address'] ?? "18, Raoul Wallenberg, Atidim, Tel Aviv, Israel"); ?>">
                    </div>
                    <p class="map-link">
                        <a href="<?php echo $contactDetails['map_link'] ?? $contactDetails['default_map_link'] ?? 'https://www.openstreetmap.org/?mlat=32.109608&mlon=34.837384#map=21/32.109608/34.837384'; ?>" target="_blank">
                            <?php echo TranslatableFactory::general()->getContent($lang, 'view_larger_map', 'View Larger Map'); ?>
                        </a>
                    </p>
                </div>
                
                <!-- Contact information -->
                <div class="contact-info">
                    <h2><?php echo $contactTranslatable->getContent($lang, 'our_info_heading', 'Contact Information'); ?></h2>
                    
                    <div class="contact-info-grid">
                        <!-- Address card -->
                        <div class="contact-card address">
                            <div class="icon"><i class="fa fa-map-marker-alt"></i></div>
                            <div class="contact-info">
                                <h3><?php echo TranslatableFactory::general()->getContent($lang, 'address', 'Address'); ?></h3>
                                <p><?php echo $contactDetails['address'] ?? ''; ?></p>
                                <?php if (!empty($contactDetails['zipcode'])): ?>
                                    <p><?php echo $contactTranslatable->getContent($lang, 'our_info_zipcode_label', 'Zipcode'); ?>: <?php echo $contactDetails['zipcode']; ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Phone card -->
                        <div class="contact-card phone">
                            <div class="icon"><i class="fa fa-phone"></i></div>
                            <div class="contact-info">
                                <h3><?php echo $contactTranslatable->getContent($lang, 'our_info_phone_section_label', 'Phone'); ?></h3>
                                <p>
                                    <?php echo $contactTranslatable->getContent($lang, 'our_info_phone_label', 'Tel'); ?>:
                                    <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $contactDetails['phone'] ?? ''); ?>"><?php echo $contactDetails['phone'] ?? ''; ?></a>
                                </p>
                                <?php if (!empty($contactDetails['fax'])): ?>
                                    <p><?php echo $contactTranslatable->getContent($lang, 'our_info_fax_label', 'Fax'); ?>: <?php echo $contactDetails['fax']; ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Email card -->
                        <div class="contact-card email">
                            <div class="icon"><i class="fa fa-envelope"></i></div>
                            <div class="contact-info">
                                <h3><?php echo TranslatableFactory::general()->getContent($lang, 'email', 'Email'); ?></h3>
                                <p><a href="mailto:<?php echo $contactDetails['email'] ?? ''; ?>"><?php echo $contactDetails['email'] ?? ''; ?></a></p>
                            </div>
                        </div>
                        
                        <!-- Website card -->
                        <?php if (!empty($contactDetails['website_link'])): ?>
                        <div class="contact-card website">
                            <div class="icon"><i class="fa fa-globe"></i></div>
                            <div class="contact-info">
                                <h3><?php echo TranslatableFactory::general()->getContent($lang, 'website', 'Website'); ?></h3>
                                <p><a href="<?php echo $contactDetails['website_link']; ?>" target="_blank"><?php echo $contactDetails['website_link']; ?></a></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

