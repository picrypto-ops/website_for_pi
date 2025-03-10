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
        <h1><?php echo $aboutTranslatable->getHtmlContent($lang, 'title'); ?></h1>
        <p class="slogan"><?php echo $aboutTranslatable->getHtmlContent($lang, 'slogan'); ?></p>
        <p class="lead content-with-html"><?php echo $aboutTranslatable->getHtmlContent($lang, 'short_description'); ?></p>
        
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

