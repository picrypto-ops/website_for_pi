<?php
$aboutContent = loadJsonData('about');
?>

<section class="about-us">
    <div class="container">
        <h1><?php echo t('about_us_title'); ?></h1>
        <div class="about-content">
            <?php echo t($aboutContent['company_history']); ?>
        </div>

        <h2><?php echo t('our_values'); ?></h2>
        <ul class="values-list">
            <?php foreach ($aboutContent['values'] as $value): ?>
                <li>
                    <h3><?php echo t($value['title']); ?></h3>
                    <p><?php echo t($value['description']); ?></p>
                </li>
            <?php endforeach; ?>
        </ul>

        <h2><?php echo t('our_mission'); ?></h2>
        <div class="mission-statement">
            <?php echo t($aboutContent['mission_statement']); ?>
        </div>
    </div>
</section>

