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
            <!-- Contact info and map on the left for desktop, first for mobile -->
            <div class="contact-info-map contact-column">
                <!-- Map section -->
                <?php if ($contactTranslatable->getContent($lang, 'show_map', true)): ?>
                <div class="map-section">
                    <h2><?php echo $contactTranslatable->getContent($lang, 'our_info_map_label', 'Find Us'); ?></h2>
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
                <?php endif; ?>
                
                <!-- Contact information -->
                <div class="contact-info">
                    <h2><?php echo $contactTranslatable->getContent($lang, 'our_info_heading', 'Get in Touch'); ?></h2>
                    
                    <div class="contact-info-grid">
                        <!-- Address card -->
                        <div class="contact-card address">
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
                            <div class="contact-info">
                                <h3><?php echo TranslatableFactory::general()->getContent($lang, 'email', 'Email'); ?></h3>
                                <p><a href="mailto:<?php echo $contactDetails['email'] ?? ''; ?>"><?php echo $contactDetails['email'] ?? ''; ?></a></p>
                            </div>
                        </div>
                        
                        <!-- Website card -->
                        <?php if (!empty($contactDetails['website_link'])): ?>
                        <div class="contact-card website">
                            <div class="contact-info">
                                <h3><?php echo TranslatableFactory::general()->getContent($lang, 'website', 'Website'); ?></h3>
                                <p><a href="<?php echo $contactDetails['website_link']; ?>" target="_blank"><?php echo $contactDetails['website_link']; ?></a></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Social Media Links -->
                    <?php if (!empty($contactDetails['social_media'])): ?>
                    <div class="social-media">
                        <h3><?php echo $contactTranslatable->getContent($lang, 'social_media_heading', 'Follow Us'); ?></h3>
                        <div class="social-links">
                            <?php foreach ($contactDetails['social_media'] as $platform => $url): ?>
                                <a href="<?php echo $url; ?>" target="_blank" class="social-link" aria-label="<?php echo ucfirst($platform); ?>">
                                    <i class="fab fa-<?php echo $platform; ?>"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Form on the right for desktop, last for mobile -->
            <div class="contact-form contact-column">
                <h2><?php echo $contactTranslatable->getContent($lang, 'form_heading', 'Send Us a Message'); ?></h2>
                <form id="contact-form" action="../utility/process-contact.php" method="POST" novalidate>
                    <div class="form-group">
                        <label for="name"><?php echo $contactTranslatable->getContent($lang, 'name_label', 'Name'); ?> *</label>
                        <input type="text" id="name" name="name" required 
                               pattern="[A-Za-z\s]{2,50}" 
                               title="<?php echo $contactTranslatable->getContent($lang, 'name_validation', 'Please enter a valid name (2-50 characters)'); ?>">
                        <div class="error-message"></div>
                    </div>
                    <div class="form-group">
                        <label for="email"><?php echo $contactTranslatable->getContent($lang, 'email_label', 'Email'); ?> *</label>
                        <input type="email" id="email" name="email" required>
                        <div class="error-message"></div>
                    </div>
                    <div class="form-group">
                        <label for="subject"><?php echo $contactTranslatable->getContent($lang, 'subject_label', 'Subject'); ?> *</label>
                        <input type="text" id="subject" name="subject" required 
                               minlength="3" maxlength="100">
                        <div class="error-message"></div>
                    </div>
                    <div class="form-group">
                        <label for="message"><?php echo $contactTranslatable->getContent($lang, 'message_label', 'Message'); ?> *</label>
                        <textarea id="message" name="message" required 
                                  minlength="10" maxlength="1000"
                                  placeholder="<?php echo $contactTranslatable->getContent($lang, 'message_placeholder', 'Your message here...'); ?>"></textarea>
                        <div class="error-message"></div>
                    </div>
                    <div class="form-group">
                        <div class="g-recaptcha" data-sitekey="<?php echo $contactDetails['recaptcha_site_key'] ?? ''; ?>"></div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="button button--primary">
                            <span class="button-text"><?php echo $contactTranslatable->getContent($lang, 'submit_button', 'Send Message'); ?></span>
                            <span class="button-loader"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Add reCAPTCHA script -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<!-- Add custom JavaScript for form validation and submission -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contact-form');
    const submitButton = form.querySelector('button[type="submit"]');
    const buttonText = submitButton.querySelector('.button-text');
    const buttonLoader = submitButton.querySelector('.button-loader');

    // Form validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Reset previous errors
        form.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        form.querySelectorAll('input, textarea').forEach(el => el.classList.remove('error'));

        // Validate form
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('error');
                field.nextElementSibling.textContent = '<?php echo $contactTranslatable->getContent($lang, 'field_required', 'This field is required'); ?>';
            }
        });

        // Validate email format
        const emailField = form.querySelector('#email');
        if (emailField.value && !isValidEmail(emailField.value)) {
            isValid = false;
            emailField.classList.add('error');
            emailField.nextElementSibling.textContent = '<?php echo $contactTranslatable->getContent($lang, 'email_invalid', 'Please enter a valid email address'); ?>';
        }

        // Validate reCAPTCHA
        const recaptchaResponse = grecaptcha.getResponse();
        if (!recaptchaResponse) {
            isValid = false;
            form.querySelector('.g-recaptcha').classList.add('error');
        }

        if (!isValid) return;

        // Show loading state
        submitButton.disabled = true;
        buttonText.style.display = 'none';
        buttonLoader.style.display = 'block';

        // Submit form
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?success=1';
            } else {
                throw new Error(data.message || '<?php echo $contactTranslatable->getContent($lang, 'submit_error', 'An error occurred. Please try again.'); ?>');
            }
        })
        .catch(error => {
            // Show error message
            const errorMessage = document.createElement('div');
            errorMessage.className = 'form-message error';
            errorMessage.innerHTML = `<i class="fa fa-exclamation-circle"></i><p>${error.message}</p>`;
            form.insertBefore(errorMessage, form.firstChild);
            
            // Reset button state
            submitButton.disabled = false;
            buttonText.style.display = 'block';
            buttonLoader.style.display = 'none';
        });
    });

    // Helper function to validate email
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    // Initialize the map only if the map container exists
    const mapContainer = document.getElementById('map');
    if (mapContainer) {
        const map = L.map('map', {
            scrollWheelZoom: false,
            dragging: true,
            zoomControl: true
        }).setView([<?php echo $contactDetails['lat'] ?? 32.109608; ?>, <?php echo $contactDetails['lng'] ?? 34.837384; ?>], 17);
        
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
    }
});
</script>

