<?php
/**
 * Page Whitelist Configuration
 * 
 * This file defines the whitelist of allowed pages that can be loaded by the application.
 * Pages not in this list will be redirected to the 404 page.
 */

/**
 * Array of allowed page names
 * 
 * The array keys are the page names as they appear in the URL parameter,
 * while the values indicate whether the page requires authentication.
 * 
 * Value of true means the page requires authentication (if auth system is implemented).
 * Value of false means the page is publicly accessible.
 */
return [
    // Public pages
    'home' => false,
    'about' => false,
    'what-we-do' => false,
    'contact' => false,
    'our-team' => false,
    'team-member' => false,
    'segment' => false,
    'product' => false,
    '404' => false,
    'csp-debug' => false,
    'process-contact' => false,
    'phpmailer-test' => false,
    
    // Auth-protected pages (for future use)
    // 'admin' => true,
    // 'dashboard' => true,
]; 