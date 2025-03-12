<?php
/**
 * Cookie Consent Floating Box Component
 * Displays a cookie consent box on the right side (LTR) or left side (RTL)
 */

// Use TranslatableFactory to get cookie consent text from pages.json
$cookieConsent = TranslatableFactory::page('cookie_consent');

// Get translations for the cookie consent
$cookieTitle = $cookieConsent->getContent(
    $lang, 
    'cookie_title', 
    $lang === 'he' ? 'הסכמה לשימוש בעוגיות' : 'Cookie Consent'
);

$cookieText = $cookieConsent->getContent(
    $lang, 
    'cookie_text', 
    $lang === 'he' 
        ? 'אתר זה משתמש בעוגיות כדי לשפר את חוויית המשתמש שלך. על ידי המשך הגלישה באתר זה, אתה מסכים לשימוש שלנו בעוגיות.'
        : 'This website uses cookies to improve your experience. By continuing to use this site, you agree to our use of cookies.'
);

$acceptText = $cookieConsent->getContent(
    $lang, 
    'cookie_accept', 
    $lang === 'he' ? 'מסכים' : 'Accept'
);

$declineText = $cookieConsent->getContent(
    $lang, 
    'cookie_decline', 
    $lang === 'he' ? 'לא מסכים' : 'Decline'
);
?>

<div id="cookie-consent-banner" class="cookie-consent-banner">
    <div class="container">
        <div class="cookie-consent-banner__content">
            <h3><?php echo $cookieTitle; ?></h3>
            <p><?php echo $cookieText; ?></p>
        </div>
        <div class="cookie-consent-banner__actions">
            <button id="cookie-accept-btn" class="accept"><?php echo $acceptText; ?></button>
            <button id="cookie-decline-btn" class="decline"><?php echo $declineText; ?></button>
        </div>
    </div>
</div> 