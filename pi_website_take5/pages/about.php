<?php
// Initialize the factory and preload common data files if not already done
$Pages = TranslatableFactory::getData('pages');


$aboutTranslatable = null;
if (!empty($Pages) && isset($Pages['about'])) {
    $aboutTranslatable = TranslatableFactory::page('about');
} else {
    // No valid segment ID found
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 - Segment Not Found</h1>";
    echo "<p>The requested segment does not exist.</p>";
    echo "<p><a href='index.php'>Return to homepage</a></p>";
    exit;
}

// Get current language from the global variable
global $lang;

// Get content sections
$contentSections = $aboutTranslatable->getAllContent($lang)['short_description'] ?? [];

// If short_description is not an array but a single string, convert it to array for backward compatibility
if (!is_array($contentSections)) {
    $contentSections = [$contentSections];
}

// Get the image slider insertion index (default to after the first section if not specified)
$imageSliderInsertionIndex = $aboutTranslatable->getContent($lang, 'image_slider_insertion_index', 1);
$imageSliderInsertionIndex = intval($imageSliderInsertionIndex);

// Make sure the index is valid
if ($imageSliderInsertionIndex < 0) {
    $imageSliderInsertionIndex = 0;
} elseif ($imageSliderInsertionIndex > count($contentSections)) {
    $imageSliderInsertionIndex = count($contentSections);
}

// Get image files for the slider
$aboutUsImagesDir = 'assets/images/about_us/';
$aboutUsImages = [];

// Check if directory exists
if (file_exists($aboutUsImagesDir) && is_dir($aboutUsImagesDir)) {
    // Get all image files from the directory
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $dirContents = scandir($aboutUsImagesDir);
    
    foreach ($dirContents as $file) {
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($extension, $allowedExtensions)) {
            // Skip hidden files
            if (substr($file, 0, 1) !== '.') {
                $aboutUsImages[] = $aboutUsImagesDir . $file;
            }
        }
    }
    
    // Sort image files for consistent order
    sort($aboutUsImages);
}

// If no images found, provide sample image URLs for demonstration
if (empty($aboutUsImages)) {
    $aboutUsImages = [
        'assets/images/about_us/sample1.jpg',
        'assets/images/about_us/sample2.jpg',
        'assets/images/about_us/sample3.jpg'
    ];
}
?>

<section class="about-us">
    <div class="container">
        <h1><?php echo $aboutTranslatable->getHtmlContent($lang, 'title'); ?></h1>
        <p class="slogan"><?php echo $aboutTranslatable->getHtmlContent($lang, 'slogan'); ?></p>
        
        <?php 
        // Display sections before the image slider
        for ($i = 0; $i < $imageSliderInsertionIndex; $i++) {
            if (isset($contentSections[$i])) {
                echo '<p class="lead content-with-html">' . $contentSections[$i] . '</p>';
            }
        }
        ?>
        
        <!-- Image Slider -->
        <div class="about-image-slider">
            <div class="about-image-slider__container">
                <?php foreach ($aboutUsImages as $image): ?>
                <div class="about-image-slider__slide" style="background-image: url('<?php echo $image; ?>'); background-position: center center;"></div>
                <?php endforeach; ?>
            </div>
            <div class="about-image-slider__controls">
                <button class="about-image-slider__button about-image-slider__button--prev" aria-label="Previous image">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>
                <div class="about-image-slider__dots"></div>
                <button class="about-image-slider__button about-image-slider__button--next" aria-label="Next image">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>
        </div>
        
        <?php 
        // Display sections after the image slider
        for ($i = $imageSliderInsertionIndex; $i < count($contentSections); $i++) {
            echo '<p class="lead content-with-html">' . $contentSections[$i] . '</p>';
        }
        ?>
        
        <div class="about-content">
            <div class="company-history">
                <?php 
                $history = $aboutTranslatable->getAllContent($lang)['company_history'] ?? [];
                if (!empty($history)): 
                ?>
                <h2><?php echo $aboutTranslatable->getHtmlContent($lang, 'company_history_heading', 'Our History'); ?></h2>
                <ul class="timeline">
                    <?php 
                    foreach ($history as $item): 
                    ?>
                        <li class="timeline-item">
                            <span class="year"><?php echo $item['year'] ?? ''; ?></span>
                            <p class="event"><?php echo $item['title'] ?? ''; ?></p>
                            <p class="event content-with-html"><?php echo formatHtmlContent($item['description'] ?? ''); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>

        <?php 
        $values = $aboutTranslatable->getAllContent($lang)['our_values'] ?? [];
        if (!empty($values)): 
        ?>
        <h2><?php echo $aboutTranslatable->getHtmlContent($lang, 'our_values_heading', 'Our Values'); ?></h2>
        <ul class="values-list">
            <?php foreach ($values as $value): ?>
                <li>
                    <h3><?php echo $value['title'] ?? ''; ?></h3>
                    <p class="content-with-html"><?php echo formatHtmlContent($value['description'] ?? ''); ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>

        <?php 
        $values = $aboutTranslatable->getAllContent($lang)['values'] ?? [];
        if (!empty($values)): 
        ?>
        <ul class="values-list">
            <?php 
            foreach ($values as $value): 
            ?>
                <li>
                    <h3><?php echo $value['title'] ?? ''; ?></h3>
                    <p class="content-with-html"><?php echo formatHtmlContent($value['description'] ?? ''); ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>

        <?php 
        $missions = $aboutTranslatable->getAllContent($lang)['our_mission'] ?? [];
        if (!empty($missions)): 
        ?>
        <h2><?php echo $aboutTranslatable->getHtmlContent($lang, 'our_mission_heading', 'Our Mission'); ?></h2>
        <div class="mission-statement">
            <?php 
            foreach ($missions as $mission): 
            ?>
                <p class="content-with-html"><?php echo formatHtmlContent($mission['mission'] ?? ''); ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

