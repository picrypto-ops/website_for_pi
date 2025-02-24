<?php
require_once 'config.php';
require_once 'includes/functions.php';

header("Content-Type: application/xml; charset=utf-8");

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
    <?php
    $pages = ['home', 'what-we-do', 'about', 'our-team', 'contact'];
    $segments = loadJsonData('segments');
    $products = loadJsonData('products');
    $team = loadJsonData('team');

    foreach ($pages as $page) {
        foreach (AVAILABLE_LANGUAGES as $lang) {
            echo '<url>';
            echo '<loc>' . BASE_URL . '/' . $lang . '/' . $page . '</loc>';
            echo '<lastmod>' . date('Y-m-d') . '</lastmod>';
            echo '<changefreq>weekly</changefreq>';
            echo '<priority>0.8</priority>';
            foreach (AVAILABLE_LANGUAGES as $altLang) {
                if ($altLang !== $lang) {
                    echo '<xhtml:link rel="alternate" hreflang="' . $altLang . '" href="' . BASE_URL . '/' . $altLang . '/' . $page . '" />';
                }
            }
            echo '</url>';
        }
    }

    foreach ($segments as $segment) {
        foreach (AVAILABLE_LANGUAGES as $lang) {
            echo '<url>';
            echo '<loc>' . BASE_URL . '/' . $lang . '/segment/' . $segment['id'] . '</loc>';
            echo '<lastmod>' . date('Y-m-d') . '</lastmod>';
            echo '<changefreq>weekly</changefreq>';
            echo '<priority>0.7</priority>';
            foreach (AVAILABLE_LANGUAGES as $altLang) {
                if ($altLang !== $lang) {
                    echo '<xhtml:link rel="alternate" hreflang="' . $altLang . '" href="' . BASE_URL . '/' . $altLang . '/segment/' . $segment['id'] . '" />';
                }
            }
            echo '</url>';
        }
    }

    foreach ($products as $product) {
        foreach (AVAILABLE_LANGUAGES as $lang) {
            echo '<url>';
            echo '<loc>' . BASE_URL . '/' . $lang . '/product/' . $product['id'] . '</loc>';
            echo '<lastmod>' . date('Y-m-d') . '</lastmod>';
            echo '<changefreq>weekly</changefreq>';
            echo '<priority>0.6</priority>';
            foreach (AVAILABLE_LANGUAGES as $altLang) {
                if ($altLang !== $lang) {
                    echo '<xhtml:link rel="alternate" hreflang="' . $altLang . '" href="' . BASE_URL . '/' . $altLang . '/product/' . $product['id'] . '" />';
                }
            }
            echo '</url>';
        }
    }

    foreach ($team as $member) {
        foreach (AVAILABLE_LANGUAGES as $lang) {
            echo '<url>';
            echo '<loc>' . BASE_URL . '/' . $lang . '/team-member/' . $member['id'] . '</loc>';
            echo '<lastmod>' . date('Y-m-d') . '</lastmod>';
            echo '<changefreq>monthly</changefreq>';
            echo '<priority>0.5</priority>';
            foreach (AVAILABLE_LANGUAGES as $altLang) {
                if ($altLang !== $lang) {
                    echo '<xhtml:link rel="alternate" hreflang="' . $altLang . '" href="' . BASE_URL . '/' . $altLang . '/team-member/' . $member['id'] . '" />';
                }
            }
            echo '</url>';
        }
    }
    ?>
</urlset>

