<?php
/**
 * Scroll indicator component for the home page
 */

/**
 * Renders a scroll indicator pointing to the next section
 * 
 * @param string $targetSectionId ID of the section to scroll to
 * @param string $lang Current language code
 * @return void
 */
function renderScrollIndicator($targetSectionId, $lang) {
    $generalTranslatable = TranslatableFactory::general();
    
    // Get the current section ID from the call stack
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
    $currentSection = '';
    
    if (isset($backtrace[1]['function'])) {
        switch ($backtrace[1]['function']) {
            case 'renderAboutSection':
                $currentSection = 'about-us';
                break;
            case 'renderWhatWeDoSection':
                $currentSection = 'what-we-do';
                break;
            case 'renderProductsSection':
                $currentSection = 'products';
                break;
            case 'renderTeamSection':
                $currentSection = 'team';
                break;
            case 'renderContactSection':
                $currentSection = 'contact-us';
                break;
            default:
                // For the hero section
                $currentSection = 'hero';
        }
    }
    ?>
    <div class="scroll-indicator" data-parent-section="<?php echo $currentSection; ?>">
        <a href="#<?php echo $targetSectionId; ?>" class="scroll-down" data-target="<?php echo $targetSectionId; ?>">
            <span class="sr-only"><?php echo $generalTranslatable->getContent($lang, 'scroll_down') ?: ''; ?></span>
            <i class="arrow-down"></i>
        </a>
    </div>
    <?php
} 