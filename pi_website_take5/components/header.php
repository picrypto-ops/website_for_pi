<?php
$generalData = loadJsonData('general');
$menuData = loadJsonData('menus');
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'he' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo getTranslatedContent($generalData['home'], $lang, 'label'); ?> - <?php echo getTranslatedContent($generalData[$page], $lang, 'label'); ?></title>
    <meta name="description" content="<?php echo getTranslatedContent($generalData[$page], $lang, 'description'); ?>">
    <link rel="canonical" href="<?php echo BASE_URL . '/' . $lang . '/' . $page; ?>">
    <meta property="og:title" content="<?php echo getTranslatedContent($generalData['home'], $lang, 'label'); ?> - <?php echo getTranslatedContent($generalData[$page], $lang, 'label'); ?>">
    <meta property="og:description" content="<?php echo getTranslatedContent($generalData[$page], $lang, 'description'); ?>">
    <meta property="og:url" content="<?php echo BASE_URL . '/' . $lang . '/' . $page; ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?php echo BASE_URL; ?>/assets/images/og-image.jpg">
    <link rel="stylesheet" href="assets/css/main.css">
    <script src="assets/js/main.js" defer></script>
    <?php if ($page === 'home'): ?>
    <script type="module" src="assets/js/pi_bg_wave.js"></script>
    <?php endif; ?>
    <!-- Vertex Shader -->
    <script type="x-shader/x-vertex" id="vertexshader">
        precision mediump float;

        attribute float scale;

        void main() {
            vec4 mvPosition = modelViewMatrix * vec4(position, 1.0);
            gl_PointSize = scale * (450.0 / -mvPosition.z);
            gl_Position = projectionMatrix * mvPosition;
        }
    </script>

    <!-- Fragment Shader -->
    <script type="x-shader/x-fragment" id="fragmentshader">
        precision mediump float;

        uniform sampler2D pointTexture;
        uniform vec3 color;

        varying float vDigitIndex;

        void main() {
            vec4 texColor = texture2D(pointTexture, vec2(gl_PointCoord.x,1.0-gl_PointCoord.y));
            if (texColor.a < 0.1) discard; // Discard transparent pixels
            gl_FragColor = vec4(color, 1.0) * texColor;
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="assets/js/pi_bg_wave.js" type="module"></script>
</head>
<body class="<?php echo $page; ?>-page">
    <header>
        <nav>
            <ul>
                <?php
                if (isset($menuData['main_menu'])):
                    foreach ($menuData['main_menu'] as $item):
                ?>
                <li class="<?php echo isActiveMenu($page, $item['main_page_slug']); ?>">
                    <a href="<?php echo $item['url']; ?>">
                        <?php echo getTranslatedContent($item, $lang, 'label'); ?>
                    </a>
                </li>
                <?php 
                    endforeach;
                endif;
                ?>
            </ul>
        </nav>
        <div class="language-switcher">
            <a href="?page=<?php echo $page; ?>&lang=en">English</a>
            <a href="?page=<?php echo $page; ?>&lang=he">עברית</a>
        </div>
    </header>
    <main id="main-content">

