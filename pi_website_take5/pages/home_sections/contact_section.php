<?php
/**
 * Contact section rendering for the home page
 */

/**
 * Renders the Contact section
 * 
 * @param string $lang Current language code
 * @param string $nextSectionId ID of the next section to scroll to
 * @return void
 */
function renderContactSection($lang, $nextSectionId = 'hero') {
    $contactPage = TranslatableFactory::page('contact');
    $contactDetails = $contactPage->getContent($lang, 'contact_details', []);
    if (!is_array($contactDetails)) {
        $contactDetails = json_decode($contactDetails, true);
    }
    ?>
    <section id="contact-us" class="contact-preview full-page-section">
        <div class="container">
            <div class="section-header">
                <h2><?php echo $contactPage->getContent($lang, 'title', 'Contact Us'); ?></h2>
                <p class="section-description"><?php echo $contactPage->getContent($lang, 'home_description', 'Get in touch with our team.'); ?></p>
            </div>
            
            <div class="home-contact-content">
                <!-- Map preview - visible first on mobile -->
                <div class="home-map-preview">
                    <div id="home-map" class="home-contact-map"></div>
                </div>
                
                <!-- Contact info cards - visible after map on mobile -->
                <div class="contact-info-grid">
                    <!-- Address card -->
                    <?php if (!empty($contactDetails['address'])): ?>
                    <div class="contact-card address">
                        <div class="icon"><i class="fa fa-map-marker-alt"></i></div>
                        <div class="contact-info">
                            <h3><?php echo TranslatableFactory::general()->getContent($lang, 'address', 'Address'); ?></h3>
                            <p><?php echo $contactDetails['address']; ?></p>
                            <?php if (!empty($contactDetails['zipcode'])): ?>
                                <p><?php echo $contactPage->getContent($lang, 'our_info_zipcode_label', 'Zipcode'); ?>: <?php echo $contactDetails['zipcode']; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Phone card -->
                    <?php if (!empty($contactDetails['phone'])): ?>
                    <div class="contact-card phone">
                        <div class="icon"><i class="fa fa-phone"></i></div>
                        <div class="contact-info">
                            <h3><?php echo TranslatableFactory::general()->getContent($lang, 'phone', 'Phone'); ?></h3>
                            <p><a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $contactDetails['phone']); ?>"><?php echo $contactDetails['phone']; ?></a></p>
                            <?php if (!empty($contactDetails['fax'])): ?>
                                <p><?php echo $contactPage->getContent($lang, 'our_info_fax_label', 'Fax'); ?>: <?php echo $contactDetails['fax']; ?></p>
                            <?php endif; ?>
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
            
            <a href="?page=contact&lang=<?php echo $lang; ?>" class="button contact-button">
                <?php echo TranslatableFactory::general()->getContent($lang, 'get_in_touch', 'Get In Touch'); ?>
            </a>
        </div>
        
        <?php renderScrollIndicator($nextSectionId, $lang, 'contact-us'); ?>
    </section>
    
    <!-- Add Leaflet for home page map preview -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
          crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
            crossorigin=""></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('home-map')) {
            // Initialize the map
            const homeMap = L.map('home-map', {
                dragging: false,
                scrollWheelZoom: false,
                zoomControl: false
            }).setView([<?php echo $contactDetails['lat'] ?? 32.109608; ?>, <?php echo $contactDetails['lng'] ?? 34.837384; ?>], 16);
            
            // Add OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(homeMap);
            
            // Add a marker for the office location
            const officeLocation = [<?php echo $contactDetails['lat'] ?? 32.109608; ?>, <?php echo $contactDetails['lng'] ?? 34.837384; ?>];
            const marker = L.marker(officeLocation).addTo(homeMap);
            
            // Ensure the map container is visible and redraws properly
            setTimeout(function() {
                homeMap.invalidateSize();
            }, 100);
        }
    });
    </script>
    <?php
} 