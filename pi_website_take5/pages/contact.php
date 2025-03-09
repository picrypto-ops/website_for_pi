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
        <div class="contact-content">
            <!-- Form on the left for desktop, last for mobile -->
            <div class="contact-form contact-column">
                <h2><?php echo $contactTranslatable->getContent($lang, 'form_heading', 'Send Us a Message'); ?></h2>
                <form id="contact-form" action="process-contact.php" method="POST">
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
                    <button type="submit" class="button"><?php echo $contactTranslatable->getContent($lang, 'submit_button', 'Send Message'); ?></button>
                </form>
            </div>
            
            <!-- Map and contact info on the right for desktop, first for mobile -->
            <div class="contact-info-map contact-column">
                <!-- Map section -->
                <div class="map-section">
                    <h2><?php echo $contactTranslatable->getContent($lang, 'our_info_map_label', 'Map'); ?></h2>
                    <div id="map" class="contact-map"></div>
                    <p class="map-link">
                        <a href="<?php echo $contactDetails['map_link'] ?? 'https://www.openstreetmap.org/?mlat=32.109608&mlon=34.837384#map=21/32.109608/34.837384'; ?>" target="_blank">
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
                            <div class="contact-info-content">
                                <h3><?php echo $contactTranslatable->getContent($lang, 'our_info_address_label', 'Address'); ?></h3>
                                <p><?php echo $contactDetails['address'] ?? ''; ?></p>
                                <?php if (!empty($contactDetails['zipcode'])): ?>
                                    <p><?php echo $contactTranslatable->getContent($lang, 'our_info_zipcode_label', 'Zipcode'); ?>: <?php echo $contactDetails['zipcode']; ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Phone card -->
                        <div class="contact-card phone">
                            <div class="icon"><i class="fa fa-phone"></i></div>
                            <div class="contact-info-content">
                                <h3><?php echo $contactTranslatable->getContent($lang, 'our_info_phone_label', 'Phone'); ?></h3>
                                <p><a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $contactDetails['phone'] ?? ''); ?>"><?php echo $contactDetails['phone'] ?? ''; ?></a></p>
                                <?php if (!empty($contactDetails['fax'])): ?>
                                    <p><?php echo $contactTranslatable->getContent($lang, 'our_info_fax_label', 'Fax'); ?>: <?php echo $contactDetails['fax']; ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Email card -->
                        <div class="contact-card email">
                            <div class="icon"><i class="fa fa-envelope"></i></div>
                            <div class="contact-info-content">
                                <h3><?php echo $contactTranslatable->getContent($lang, 'our_info_email_label', 'Email'); ?></h3>
                                <p><a href="mailto:<?php echo $contactDetails['email'] ?? ''; ?>"><?php echo $contactDetails['email'] ?? ''; ?></a></p>
                            </div>
                        </div>
                        
                        <!-- Website card -->
                        <?php if (!empty($contactDetails['website_link'])): ?>
                        <div class="contact-card website">
                            <div class="icon"><i class="fa fa-globe"></i></div>
                            <div class="contact-info-content">
                                <h3><?php echo $contactTranslatable->getContent($lang, 'our_info_website_label', 'Website'); ?></h3>
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

<!-- Include Leaflet CSS and JS for OpenStreetMap -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
      crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
        crossorigin=""></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the map
    const map = L.map('map').setView([<?php echo $contactDetails['lat'] ?? 32.109608; ?>, <?php echo $contactDetails['lng'] ?? 34.837384; ?>], 17);
    
    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);
    
    // Add a marker for the office location
    const officeLocation = [<?php echo $contactDetails['lat'] ?? 32.109608; ?>, <?php echo $contactDetails['lng'] ?? 34.837384; ?>];
    const marker = L.marker(officeLocation).addTo(map);
    
    // Add a popup with the address
    marker.bindPopup('<?php echo addslashes($contactDetails['address'] ?? "18, Raoul Wallenberg, Atidim, Tel Aviv, Israel"); ?>').openPopup();
    
    // Ensure the map container is visible and redraws properly
    setTimeout(function() {
        map.invalidateSize();
    }, 100);
});
</script>

