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

// Get about page translatable

// Get current language from the global variable
global $lang;
?>

<section class="about-us">
    <div class="container">
        <h1><?php echo $aboutTranslatable->getContent($lang, 'title'); ?></h1>
        <p class="slogan"><?php echo $aboutTranslatable->getContent($lang, 'slogan'); ?></p>
        <p class="lead"><?php echo $aboutTranslatable->getContent($lang, 'short_description'); ?></p>
        
        <div class="about-content">
            <div class="company-history">
                <h2><?php echo $aboutTranslatable->getContent($lang, 'company_history_heading', 'Our History'); ?></h2>
                <ul class="timeline">
                    <?php 
                    $history = $aboutTranslatable->getAllContent($lang)['company_history'] ?? [];
                    foreach ($history as $item): 
                    ?>
                        <li class="timeline-item">
                            <span class="year"><?php echo $item['year'] ?? ''; ?></span>
                            <p class="event"><?php echo $item['title'] ?? ''; ?></p>
                            <p class="event"><?php echo $item['description'] ?? ''; ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <h2><?php echo $aboutTranslatable->getContent($lang, 'our_values_heading', 'Our Values'); ?></h2>
        <ul class="values-list">
            <?php 
            $values = $aboutTranslatable->getAllContent($lang)['our_values'] ?? [];
            foreach ($values as $value): 
            ?>
                <li>
                    <h3><?php echo $value['title'] ?? ''; ?></h3>
                    <p><?php echo $value['description'] ?? ''; ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
        <ul class="values-list">
            <?php 
            $values = $aboutTranslatable->getAllContent($lang)['values'] ?? [];
            foreach ($values as $value): 
            ?>
                <li>
                    <h3><?php echo $value['title'] ?? ''; ?></h3>
                    <p><?php echo $value['description'] ?? ''; ?></p>
                </li>
            <?php endforeach; ?>
        </ul>

        <h2><?php echo $aboutTranslatable->getContent($lang, 'our_mission_heading', 'Our Mission'); ?></h2>
        <div class="mission-statement">
            <?php 
            $missions = $aboutTranslatable->getAllContent($lang)['our_mission'] ?? [];
            foreach ($missions as $mission): 
            ?>
                <p><?php echo $mission['mission'] ?? ''; ?></p>
            <?php endforeach; ?>
        </div>
    </div>
</section>

