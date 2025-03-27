<?php
define('BASE_URL', 'https://www.pi-hf.com');
define('DEFAULT_LANG', 'en');
define('AVAILABLE_LANGUAGES', ['en']);
// define('AVAILABLE_LANGUAGES', ['en']);


// Database configuration (if needed in the future)
define('DB_HOST', 'localhost');
define('DB_NAME', 'pigroup_db');
define('DB_USER', 'username');
define('DB_PASS', 'password');

// Email configuration for contact form
define('SMTP_HOST', 'pihf-com0e.mail.protection.outlook.com');
define('SMTP_PORT', 25);
define('SMTP_FROM', 'website_contact_us@pi-hf.com');
define('SMTP_TO', 'ronen@pi-hf.com');
define('SEND_CONFIRMATION_EMAIL', false);

// reCAPTCHA configuration
define('USE_RECAPTCHA', false);
define('RECAPTCHA_SITE_KEY', '');
define('RECAPTCHA_SECRET_KEY', '');

// Debug configuration
define('SHOW_DEBUG_SMTP_SERVER_BUTTON', true);
define('PHP_DEBUG_MODE', false);

// Other configuration settings
// We're using OpenStreetMap which doesn't require an API key
// define('GOOGLE_MAPS_API_KEY', 'your_google_maps_api_key');

