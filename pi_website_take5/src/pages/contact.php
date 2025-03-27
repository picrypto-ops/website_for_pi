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
                <form id="contactForm" class="contact-form" data-action="?page=process-contact" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required>
                        <div class="error-message"></div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                        <div class="error-message"></div>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" required>
                        <div class="error-message"></div>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" required></textarea>
                        <div class="error-message"></div>
                    </div>
                    <?php if (USE_RECAPTCHA): ?>
                    <div class="form-group">
                        <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY; ?>"></div>
                        <div class="error-message"></div>
                    </div>
                    <?php endif; ?>
                    <button type="submit" class="submit-btn">Send Message</button>
                    <?php if (defined('SHOW_DEBUG_SMTP_SERVER_BUTTON') && SHOW_DEBUG_SMTP_SERVER_BUTTON): ?>
                    <button type="button" id="debugSmtpButton" class="debug-btn">Test SMTP Server</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <div id="formMessage" class="form-message"></div>
    </div>
</section>

<?php if (defined('USE_RECAPTCHA') && USE_RECAPTCHA): ?>
<!-- Add reCAPTCHA script -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>

<!-- Add custom JavaScript for form validation and submission -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');
    const formMessage = document.getElementById('formMessage');
    
    // Form validation
    form.addEventListener('input', function(e) {
        const field = e.target;
        const errorDiv = field.nextElementSibling;
        
        // Reset error message
        errorDiv.textContent = '';
        field.classList.remove('error');
        
        // Validate field
        if (field.validity.valid) {
            field.classList.add('valid');
        } else {
            field.classList.add('error');
            errorDiv.textContent = field.validationMessage;
        }
    });
    
    // Debug SMTP button handler
    const debugButton = document.getElementById('debugSmtpButton');
    if (debugButton) {
        debugButton.addEventListener('click', function() {
            // Fill form with test data
            form.querySelector('#name').value = 'test name';
            form.querySelector('#email').value = 'ronenbitman@gmail.com';
            form.querySelector('#subject').value = 'test subject';
            form.querySelector('#message').value = 'test message';
            
            // Submit the form
            form.dispatchEvent(new Event('submit'));
        });
    }
    
    // Form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Reset previous messages
        formMessage.textContent = '';
        formMessage.className = 'form-message';
        
        // Reset all error messages
        form.querySelectorAll('.error-message').forEach(div => div.textContent = '');
        form.querySelectorAll('.error').forEach(field => field.classList.remove('error'));
        
        // Validate form
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Validate reCAPTCHA if enabled
        <?php if (USE_RECAPTCHA): ?>
        const recaptchaResponse = grecaptcha.getResponse();
        if (!recaptchaResponse) {
            const recaptchaError = form.querySelector('.g-recaptcha').nextElementSibling;
            recaptchaError.textContent = 'Please complete the reCAPTCHA verification.';
            return;
        }
        <?php endif; ?>
        
        // Get form data
        const formData = new FormData(form);
        
        try {
            // Log form submission details
            console.log('Form submission started');
            console.log('Form action:', form.dataset.action);
            console.log('Form data:', Object.fromEntries(formData));
            
            // Send form data
            const response = await fetch(form.dataset.action, {
                method: 'POST',
                body: formData,
                credentials: 'include',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            // Get response text first
            const responseText = await response.text();
            console.log('Raw server response:', responseText);
            
            // Try to parse as JSON
            let data;
            try {
                data = JSON.parse(responseText);
            } catch (e) {
                console.error('Failed to parse JSON response:', e);
                throw new Error('Server returned invalid response format');
            }
            
            // Log SMTP configuration if available
            if (data.debug) {
                console.log('SMTP Configuration:', data.debug);
            }
            
            if (data.success) {
                // Show success message
                formMessage.textContent = data.message;
                formMessage.className = 'form-message success';
                
                // Reset form
                form.reset();
                
                // Reset reCAPTCHA if enabled
                <?php if (USE_RECAPTCHA): ?>
                grecaptcha.reset();
                <?php endif; ?>
                
                // Scroll message into view
                formMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                // Show error message
                formMessage.textContent = data.message;
                formMessage.className = 'form-message error';
                
                // Show field-specific errors
                if (data.errors) {
                    Object.entries(data.errors).forEach(([field, message]) => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('error');
                            input.nextElementSibling.textContent = message;
                        }
                    });
                }
                
                // If CSRF token error, regenerate token
                if (data.message.includes('Invalid security token')) {
                    try {
                        // Get new CSRF token by making a GET request to the contact page
                        const tokenResponse = await fetch('?page=contact&lang=<?php echo $lang; ?>', {
                            credentials: 'include',
                            headers: {
                                'Accept': 'text/html'
                            }
                        });
                        
                        if (!tokenResponse.ok) {
                            throw new Error('Failed to get new CSRF token');
                        }
                        
                        const html = await tokenResponse.text();
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newToken = doc.querySelector('input[name="csrf_token"]')?.value;
                        
                        if (!newToken) {
                            throw new Error('Could not find CSRF token in response');
                        }
                        
                        // Update form with new token
                        const tokenInput = form.querySelector('input[name="csrf_token"]');
                        if (tokenInput) {
                            tokenInput.value = newToken;
                            
                            // Retry submission if it was the debug button
                            if (e.target === debugButton) {
                                console.log('Retrying form submission with new CSRF token');
                                form.dispatchEvent(new Event('submit'));
                            }
                        } else {
                            throw new Error('Could not find CSRF token input field');
                        }
                    } catch (error) {
                        console.error('Error refreshing CSRF token:', error);
                        formMessage.textContent = 'Session expired. Please refresh the page and try again.';
                        formMessage.className = 'form-message error';
                    }
                }
            }
        } catch (error) {
            console.error('Form submission error:', error);
            formMessage.textContent = 'An error occurred while submitting the form. Please try again.';
            formMessage.className = 'form-message error';
        }
    });

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

