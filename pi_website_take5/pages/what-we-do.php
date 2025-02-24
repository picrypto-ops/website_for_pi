<section class="what-we-do">
    <div class="container">
        <h1><?php echo t('what_we_do_title'); ?></h1>
        <p class="lead"><?php echo t('what_we_do_description'); ?></p>

        <div class="segment-grid" role="list">
            <?php foreach ($segments as $segment): ?>
                <div class="segment-card" role="listitem">
                    <h2><?php echo t($segment['name']); ?></h2>
                    <p><?php echo t($segment['short_description']); ?></p>
                    <a href="?page=segment&id=<?php echo $segment['id']; ?>&lang=<?php echo $lang; ?>" class="button" aria-label="<?php echo t('learn_more_about') . ' ' . t($segment['name']); ?>"><?php echo t('learn_more'); ?></a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

