<?php
// Turn on debug output temporarily - remove in production
TranslatableFactory::$debug = false;

// Initialize the factory and preload common data files
TranslatableFactory::initialize(['general', 'segments', 'products', 'menus', 'pages', 'team']);

// Include necessary files
include_once 'includes/functions.php';
include_once 'includes/home/helpers.php';

// Get home translatable for hero section
$homeTranslatable = TranslatableFactory::page('home');
?>

<div class="home-page">
    <!-- Hero Section -->
    <section id="hero" class="hero full-page">
        <!-- Logo in top right for English -->
        <div class="hero-top-logo <?php echo $lang === 'he' ? 'left-aligned' : 'right-aligned'; ?>">
            <img src="assets/images/logo.svg" alt="PI Group Logo">
        </div>
        
        <!-- Language Switcher positioned at top left/right depending on language -->
        <div class="hero-language-switcher <?php echo $lang === 'he' ? 'right-aligned' : 'left-aligned'; ?>">
            <?php
            // Get current URL parameters
            $currentParams = $_GET;
            
            // Create EN link with all current parameters except for lang
            $enParams = $currentParams;
            $enParams['lang'] = 'en';
            $enLink = '?' . http_build_query($enParams);
            
            // Create HE link with all current parameters except for lang
            $heParams = $currentParams;
            $heParams['lang'] = 'he';
            $heLink = '?' . http_build_query($heParams);
            ?>
            <a href="<?php echo $enLink; ?>" class="lang-switch <?php echo $lang === 'en' ? 'active' : ''; ?>" data-lang="en">EN</a>
            <span class="separator">|</span>
            <a href="<?php echo $heLink; ?>" class="lang-switch <?php echo $lang === 'he' ? 'active' : ''; ?>" data-lang="he">עב</a>
        </div>
        
        <div class="hero-content">
            <h1><?php echo $homeTranslatable->getContent($lang, 'title', 'Welcome to PI Group'); ?></h1>
            <p class="slogan"><?php echo $homeTranslatable->getContent($lang, 'slogan', 'Financial Excellence'); ?></p>
            <p class="wide-description"><?php echo $homeTranslatable->getContent($lang, 'home_description', 'Your trusted partner in financial services.'); ?></p>
            <a href="#about-us" class="cta-button"><?php echo TranslatableFactory::general()->getContent($lang, 'learn_more', 'Learn More'); ?></a>
        </div>

        <!-- ThreeJS container positioned at bottom 30% -->
        <div id="threeJsContainer"></div>
        
        <!-- Render scroll indicator with function -->
        <?php renderScrollIndicator('about-us', $lang); ?>
    </section>

    <!-- Render all page sections using functions -->
    <?php renderAboutSection($lang); ?>
    <?php renderWhatWeDoSection($lang); ?>
    <?php renderProductsSection($lang); ?>
    <?php renderTeamSection($lang); ?>
    <?php renderContactSection($lang); ?>
    
    <!-- Breadcrumb navigation for direct section access -->
    <nav class="breadcrumb-navigation">
        <ul>
            <li><a href="#hero">Home</a></li>
            <li><a href="#about-us">About</a></li>
            <li><a href="#what-we-do">Services</a></li>
            <li><a href="#products">Products</a></li>
            <li><a href="#team">Team</a></li>
            <li><a href="#contact-us">Contact</a></li>
        </ul>
    </nav>
</div>

<!-- JavaScript for functionality -->
<script src="assets/js/home.js"></script>
<script src="assets/js/scroll-indicator.js"></script>

