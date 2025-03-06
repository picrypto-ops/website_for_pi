<?php
/**
 * What We Do section rendering for the home page
 */

/**
 * Renders the What We Do section with segment cards
 * 
 * @param string $lang Current language code
 * @param string $nextSectionId ID of the next section to scroll to
 * @return void
 */
function renderWhatWeDoSection($lang, $nextSectionId = null) {
    $whatWeDoTranslatable = TranslatableFactory::page('what_we_do');
    $segmentData = TranslatableFactory::getData('segments');
    
    // If nextSectionId is not provided, default to the first segment or team section
    if ($nextSectionId === null) {
        $nextSectionId = is_array($segmentData) && !empty($segmentData) ? 
            'segment-' . array_key_first($segmentData) : 'team';
    }
    ?>
    <section id="what-we-do" class="featured-segments full-page-section">
        <div class="container">
            <h2><?php echo $whatWeDoTranslatable->getContent($lang, 'title'); ?></h2>
            <p class="section-description"><?php echo $whatWeDoTranslatable->getContent($lang, 'home_description'); ?></p>
            
            <div class="segment-grid">
                <?php 
                // Extract segments with better error handling
                if (is_array($segmentData)) {
                    foreach ($segmentData as $segmentSlug => $segment): 
                        if (!is_array($segment)) continue;
                        
                        // Create translatable for this segment
                        $segmentTranslatable = TranslatableFactory::segment($segmentSlug);
                ?>
                    <div class="segment-card">
                        <div class="segment-icon">
                            <?php if (isset($segment['icon']) && !empty($segment['icon'])): ?>
                                <img src="<?php echo $segment['icon']; ?>" alt="<?php echo $segmentTranslatable->getContent($lang, 'name'); ?>">
                            <?php else: ?>
                                <div class="icon-placeholder"><i class="fa fa-chart-line"></i></div>
                            <?php endif; ?>
                        </div>
                        <h3><?php echo $segmentTranslatable->getContent($lang, 'name'); ?></h3>
                        <p><?php echo $segmentTranslatable->getContent($lang, 'home_description'); ?></p>
                        <a href="?page=segment&id=<?php echo $segmentSlug; ?>&lang=<?php echo $lang; ?>" class="segment-link">
                            <p class="slogan">
                                <?php echo TranslatableFactory::general()->getContent($lang, 'view_details', 'View Details'); ?>
                                <!-- <span class="arrow"><?php echo TranslatableFactory::general()->getContent($lang, 'arrow', '»'); ?></span>                                 -->
                            </p>
                        </a>
                    </div>
                <?php 
                    endforeach;
                } else {
                    echo "<div class='no-data-message'>No segment data available</div>";
                }
                ?>
            </div>
            
            <!-- Removed "View All Services" button as requested -->
        </div>
        
        <?php renderScrollIndicator($nextSectionId, $lang, 'what-we-do'); ?>
    </section>
    <?php
} 