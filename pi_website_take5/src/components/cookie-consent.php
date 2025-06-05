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

<div id="cookie-consent-banner" class="cookie-consent">
    <?php // Use global container or BEM container ?>
    <div class="container">
        <?php // Use BEM element class ?>
        <div class="cookie-consent__content">
            <?php // Use BEM element class ?>
            <h3 class="cookie-consent__title"><?php echo $cookieTitle; ?></h3>
            <?php // Use BEM element class ?>
            <p class="cookie-consent__text"><?php echo $cookieText; ?></p>
        </div>
         <?php // Use BEM element class ?>
        <div class="cookie-consent__actions">
            <?php // Use BEM element and modifier classes ?>
            <button id="cookie-accept-btn" class="cookie-consent__button cookie-consent__button--accept"><?php echo $acceptText; ?></button>
            <?php // Use BEM element and modifier classes ?>
            <button id="cookie-decline-btn" class="cookie-consent__button cookie-consent__button--decline"><?php echo $declineText; ?></button>
        </div>
    </div>
</div>