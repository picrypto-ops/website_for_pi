<?php
/**
 * Scroll indicator component for the home page
 */

/**
 * Renders a scroll indicator pointing to the next section
 * 
 * @param string $targetSectionId ID of the section to scroll to
 * @param string $lang Current language code
 * @param string $currentSectionId ID of the current section (optional)
 * @return void
 */
function renderScrollIndicator($targetSectionId, $lang, $currentSectionId = null) {
    $generalTranslatable = TranslatableFactory::general();
    
    // Use provided current section ID if available
    if ($currentSectionId === null) {
        // If not provided, use backtrace to determine current section (for backward compatibility)
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        
        if (isset($backtrace[1]['function'])) {
            switch ($backtrace[1]['function']) {
                case 'renderAboutSection':
                    $currentSectionId = 'about-us';
                    break;
                case 'renderWhatWeDoSection':
                    $currentSectionId = 'what-we-do';
                    break;
                case 'renderProductsSection':
                    $currentSectionId = 'products';
                    break;
                case 'renderTeamSection':
                    $currentSectionId = 'team';
                    break;
                case 'renderContactSection':
                    $currentSectionId = 'contact-us';
                    break;
                case 'renderSegmentDisplay':
                    // For segment display, get the segment slug from the arguments
                    if (isset($backtrace[1]['args'][1])) {
                        $currentSectionId = 'segment-' . $backtrace[1]['args'][1];
                    } else {
                        $currentSectionId = 'segment';
                    }
                    break;
                default:
                    // For the hero section
                    $currentSectionId = 'hero';
            }
        } else {
            // Default to hero if function name can't be determined
            $currentSectionId = 'hero';
        }
    }
    ?>
    <div class="scroll-indicator" data-parent-section="<?php echo $currentSectionId; ?>">
        <a href="#<?php echo $targetSectionId; ?>" class="scroll-down" data-target="<?php echo $targetSectionId; ?>">
            <span class="sr-only"><?php echo $generalTranslatable->getContent($lang, 'scroll_down', 'Scroll Down'); ?></span>
            <i class="arrow-down"></i>
        </a>
    </div>
    <?php
} 