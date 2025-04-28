</main>
    <footer class="site-footer"> 
        <div class="container">
            <div class="site-footer__content"> 
                <div class="site-footer__logo"> 
                    <?php
                    $footerText = TranslatableFactory::general()->getContent($lang, 'footer_text', 'PI Group Financial Services');
                    if (!empty($footerText)):
                    ?>
                        <img src="assets/images/logo.svg" alt="PI Group Logo" class="site-footer__logo-image"> 
                        <p class="site-footer__logo-text"><?php echo $footerText; ?></p> 
                    <?php endif; ?>
                </div>
                <div class="site-footer__links"> 
                    <ul class="site-footer__links-list"> 
                        <?php
                        $footerItems = TranslatableFactory::getData('menu'); // Use factory method
                        if ($footerItems && isset($footerItems['main_menu'])):
                            foreach ($footerItems['main_menu'] as $item):
                                // Determine URL (assuming similar logic as header for segments/team)
                                $isSegment = in_array($item['main_page_slug'], ['investment_banking', 'asset_management']);
                                $isTeam = $item['main_page_slug'] === 'our-team'; // Assuming 'our-team' slug
                                $isHome = $item['main_page_slug'] === 'home';
                                if ($isHome) {
                                    $url = "?page=home&lang={$lang}";
                                } elseif ($isSegment) {
                                    $url = "?page=segment&id={$item['main_page_slug']}&lang={$lang}";
                                } elseif ($isTeam) {
                                    $url = "?page=our-team&lang={$lang}";
                                } else {
                                    $url = strpos($item['url'], '/') === 0 ? "?page=".ltrim($item['url'], '/')."&lang={$lang}" : $item['url'];
                                }
                        ?>
                        <li class="site-footer__links-item"> 
                            <a href="<?php echo $url; ?>" class="site-footer__links-link"> 
                                <?php echo getTranslatedContent($item, $lang, 'label'); ?>
                            </a>
                        </li>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </ul>
                </div>
                <div class="site-footer__social"> 
                    </div>
            </div>
            <div class="site-footer__bottom"> 
                <p>&copy; <?php echo date('Y'); ?> PI Group. <?php echo TranslatableFactory::general()->getContent($lang, 'all_rights_reserved', 'All rights reserved'); ?>.</p>
            </div>
        </div>
    </footer>

    <?php include __DIR__ . '/cookie-consent.php'; ?>
</body>
</html>