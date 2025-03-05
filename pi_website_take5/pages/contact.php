<?php
// Initialize the factory and preload common data files if not already done
$Pages = TranslatableFactory::getData('pages');


$contactTranslatable = null;
if (!empty($Pages) && isset($Pages['about'])) {
    // Get contact page translatable
    $contactTranslatable = TranslatableFactory::page('contact');
} else {
    // No valid segment ID found
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 - Segment Not Found</h1>";
    echo "<p>The requested segment does not exist.</p>";
    echo "<p><a href='index.php'>Return to homepage</a></p>";
    exit;
}


// Get contact info from general data
$contactInfo = TranslatableFactory::general('contact_info');

// Get current language from the global variable
global $lang;
?>

<section class="contact-us">
    <div class="container">
        <h1><?php echo $contactTranslatable->getContent($lang, 'title'); ?></h1>
        <p class="slogan"><?php echo $contactTranslatable->getContent($lang, 'slogan'); ?></p>
        <p class="lead"><?php echo $contactTranslatable->getContent($lang, 'short_description'); ?></p>
        
        <div class="contact-content">
            <div class="contact-form">
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
            <div class="contact-info">
                <h2><?php echo $contactTranslatable->getContent($lang, 'our_info_heading', 'Contact Information'); ?></h2>
                <?php 
                $defaultContactInfo = json_encode([
                    "address" => "123 Main St, Vancouver, USA",
                    "phone" => "+123 456 7890",
                    "email" => "info@pigroup.com",
                    "website_link" => "https://www.pigroup.com",
                    "map_link" => "https://www.google.com/maps"
                ]);
                $contactInfoData = $contactTranslatable->getContent($lang, 'contact_details', $defaultContactInfo);
                $contactInfoContent = is_array($contactInfoData) ? $contactInfoData : json_decode($contactInfoData, true);
                ?>
                <p><?php echo $contactInfoContent['address'] ?? ''; ?></p>
                <p><?php echo $contactTranslatable->getContent($lang, 'our_info_phone_label', 'Phone'); ?>: <?php echo $contactInfoContent['phone'] ?? ''; ?></p>
                <p><?php echo $contactTranslatable->getContent($lang, 'our_info_email_label', 'Email'); ?>: <?php echo $contactInfoContent['email'] ?? ''; ?></p>
                <p><?php echo $contactTranslatable->getContent($lang, 'our_info_address_label', 'Address'); ?>: <?php echo $contactInfoContent['address'] ?? ''; ?></p>
                <p><?php echo $contactTranslatable->getContent($lang, 'our_info_map_label', 'Map'); ?>: <a href="<?php echo $contactInfoContent['map_link'] ?? ''; ?>" target="_blank"><?php echo $contactInfoContent['map_link'] ?? ''; ?></a></p>
                <p><?php echo $contactTranslatable->getContent($lang, 'our_info_website_label', 'Website'); ?>: <a href="<?php echo $contactInfoContent['website_link'] ?? ''; ?>" target="_blank"><?php echo $contactInfoContent['website_link'] ?? ''; ?></a></p>
                <div id="map" class="contact-map"></div>
            </div>
        </div>
    </div>
</section>

<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY; ?>&callback=initMap" async defer></script>
<script>
function initMap() {
    const officeLocation = { 
        lat: <?php echo $contactInfoContent['lat'] ?? 49.2827; ?>, 
        lng: <?php echo $contactInfoContent['lng'] ?? -123.1207; ?> 
    };
    const map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: officeLocation,
    });
    const marker = new google.maps.Marker({
        position: officeLocation,
        map: map,
    });
}
</script>

