<?php
// Turn on debug output temporarily - remove in production
TranslatableFactory::$debug = false;

// Initialize the factory and preload common data files
TranslatableFactory::initialize(['general', 'segments', 'products', 'menus', 'pages', 'team']);

// Include necessary files
include_once __DIR__ . '/../utility/functions.php';
include_once __DIR__ . '/home_sections/helpers.php';

// Get home translatable for hero section
$homeTranslatable = TranslatableFactory::page('home');

// Get menu data
$menuData = TranslatableFactory::getData('menus') ?: [];
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
            <?php echo renderLanguageSwitcher($lang, '', false); ?>
        </div>
        
        <!-- Clean, minimal hero menu -->
        <div class="hero-menu <?php echo $lang === 'he' ? 'left-aligned' : 'right-aligned'; ?>">
            <button class="hero-menu-toggle" aria-label="<?php echo TranslatableFactory::general()->getHtmlContent($lang, 'menu_toggle', 'Toggle Menu'); ?>">
                <span></span>
            </button>
            <nav class="hero-nav">
                <ul>
                    <?php
                    if (isset($menuData['main_menu'])):
                        foreach ($menuData['main_menu'] as $item):
                            // Check if this is a segment page (investment_banking or asset_management)
                            $isSegment = in_array($item['main_page_slug'], ['investment_banking', 'asset_management']);
                            
                            // Check if this is the team page
                            $isTeam = $item['main_page_slug'] === 'team';
                            
                            // Check if this is the home page
                            $isHome = $item['main_page_slug'] === 'home';
                            
                            // Generate the correct URL
                            if ($isHome) {
                                $url = "?page=home&lang={$lang}";
                            } elseif ($isSegment) {
                                $url = "?page=segment&id={$item['main_page_slug']}&lang={$lang}";
                            } elseif ($isTeam) {
                                $url = "?page=our-team&lang={$lang}";
                            } else {
                                $url = strpos($item['url'], '/') === 0 ? "?page=".ltrim($item['url'], '/')."&lang={$lang}" : $item['url'];
                            }
                    ?>
                    <li>
                        <a href="<?php echo $url; ?>">
                            <?php echo getTranslatedContent($item, $lang, 'label'); ?>
                        </a>
                    </li>
                    <?php 
                        endforeach;
                    endif;
                    ?>
                </ul>
            </nav>
        </div>
        
        <div class="hero-content">
            <h1><?php echo $homeTranslatable->getHtmlContent($lang, 'title', 'Welcome to PI Group'); ?></h1>
            <p class="slogan"><?php echo $homeTranslatable->getHtmlContent($lang, 'slogan', 'Financial Excellence'); ?></p>
            <p class="wide-description content-with-html"><?php echo $homeTranslatable->getHtmlContent($lang, 'home_description', 'Your trusted partner in financial services.'); ?></p>
            <?php
            // Point CTA to the first segment
            $segments = TranslatableFactory::getData('segments');
            $firstSegmentId = is_array($segments) && !empty($segments) ? 
                'segment-' . array_key_first($segments) : 'team';
            ?>
            <a href="#<?php echo $firstSegmentId; ?>" class="button button--cta">
                <?php echo TranslatableFactory::general()->getHtmlContent($lang, 'learn_more', 'Learn More'); ?>
            </a>
        </div>

        <!-- ThreeJS container positioned at bottom 30% -->
        <div id="threeJsContainer"></div>
        
        <!-- Render scroll indicator with function -->
        <?php
        // Point to the first segment directly
        $segments = TranslatableFactory::getData('segments');
        $firstSegmentId = is_array($segments) && !empty($segments) ? 
            'segment-' . array_key_first($segments) : 'team';
        renderScrollIndicator($firstSegmentId, $lang, 'hero');
        ?>
    </section>

    <!-- Render all page sections using functions -->
    <?php 
    // Include the segments display file
    require_once __DIR__ . '/home_sections/segments_display.php';
    
    // Get all segments to display them one by one
    $segments = TranslatableFactory::getData('segments');
    if (is_array($segments)) {
        $segmentKeys = array_keys($segments);
        foreach ($segmentKeys as $index => $segment_slug) {
            // Determine next section
            $nextSectionId = null; // This will use the default logic in renderSegmentDisplay
            
            // Pass the next section ID to the render function
            renderSegmentDisplay($lang, $segment_slug, $nextSectionId);
        }
    }
    ?>
    
    <?php renderTeamSection($lang, 'about-us'); ?>
    <?php renderAboutSection($lang, 'contact-us'); ?>
    <?php renderContactSection($lang, 'hero'); ?>
    
    <!-- Breadcrumb navigation for direct section access -->
    <nav class="breadcrumb-navigation">
        <ul>
            <li><a href="#hero">Home</a></li>
            <li><a href="#segment-<?php echo array_key_first($segments); ?>">Segments</a></li>
            <li><a href="#team">Team</a></li>
            <li><a href="#about-us">About</a></li>
            <li><a href="#contact-us">Contact</a></li>
        </ul>
    </nav>
</div>

<!-- ThreeJS Container for Background Wave -->
<div id="threeJsContainer"></div>

