</main>
    <footer>
        <div class="footer-content">
            <div class="footer-logo">
                <img src="assets/images/logo.png" alt="PI Group Logo">
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
            <p>&copy; <?php echo date('Y'); ?> PI Group. <?php echo t('all_rights_reserved'); ?></p>
        </div>
    </footer>
</body>
</html>

