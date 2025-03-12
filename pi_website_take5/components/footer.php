</main>
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <?php 
                    $footerText = TranslatableFactory::general()->getContent($lang, 'footer_text', 'PI Group Financial Services');
                    if (!empty($footerText)): 
                    ?>
                        <img src="assets/images/logo.svg" alt="PI Group Logo">
                        <p class="footer-text"><?php echo $footerText; ?></p>
                    <?php endif; ?>
                </div>
                <div class="footer-links">
                    <ul>
                        <?php
                        $footerItems = loadJsonData('menu');
                        if ($footerItems && isset($footerItems['main_menu'])):
                            foreach ($footerItems['main_menu'] as $item):
                        ?>
                        <li>
                            <a href="<?php echo $item['url']; ?>">
                                <?php echo t($item['language_slug'][$lang]['label']); ?>
                            </a>
                        </li>
                        <?php 
                            endforeach;
                        endif;
                        ?>
                    </ul>
                </div>
                <div class="footer-social">
                    <!-- Add social media links here -->
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> PI Group. <?php echo TranslatableFactory::general()->getContent($lang, 'all_rights_reserved', 'All rights reserved'); ?>.</p>
            </div>
        </div>
    </footer>

    <?php include 'components/cookie-consent.php'; ?>
</body>
</html>

