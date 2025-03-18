<?php

// Create directories
$directories = [
    'src/components',
    'src/config',
    'src/utility/classes/translation',
    'src/pages'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Move files
$moves = [
    // Components
    ['components/header.php', 'src/components/header.php'],
    ['components/footer.php', 'src/components/footer.php'],
    ['components/cookie-consent.php', 'src/components/cookie-consent.php'],
    ['components/asset_management_icons.php', 'src/components/asset_management_icons.php'],
    
    // Translation files
    ['includes/classes/translation/PageTranslatable.php', 'src/utility/classes/translation/PageTranslatable.php'],
    ['includes/classes/translation/ProductTranslatable.php', 'src/utility/classes/translation/ProductTranslatable.php'],
    ['includes/classes/translation/SegmentTranslatable.php', 'src/utility/classes/translation/SegmentTranslatable.php'],
    ['includes/classes/translation/TeamMemberEnhanced.php', 'src/utility/classes/translation/TeamMemberEnhanced.php'],
    ['includes/classes/translation/TeamTranslatable.php', 'src/utility/classes/translation/TeamTranslatable.php'],
    ['includes/classes/translation/TranslatableFactory.php', 'src/utility/classes/translation/TranslatableFactory.php'],
    ['includes/classes/translation/TranslatableInterface.php', 'src/utility/classes/translation/TranslatableInterface.php'],
    ['includes/classes/translation/GeneralTranslatable.php', 'src/utility/classes/translation/GeneralTranslatable.php'],
    ['includes/classes/translation/AbstractTranslatable.php', 'src/utility/classes/translation/AbstractTranslatable.php'],
    
    // Utility files
    ['includes/language.php', 'src/utility/language.php'],
    ['includes/image-optimizer.php', 'src/utility/image-optimizer.php'],
    ['includes/functions.php', 'src/utility/functions.php'],
    ['includes/cache.php', 'src/utility/cache.php'],
    ['includes/__structured-data.php', 'src/utility/__structured-data.php'],
    ['includes/README.md', 'src/utility/README.md'],
    
    // Pages
    ['pages/our-team.php', 'src/pages/our-team.php'],
    ['pages/product.php', 'src/pages/product.php'],
    ['pages/segment.php', 'src/pages/segment.php'],
    ['pages/team-member.php', 'src/pages/team-member.php'],
    ['pages/what-we-do.php', 'src/pages/what-we-do.php'],
    ['pages/home.php', 'src/pages/home.php'],
    ['pages/contact.php', 'src/pages/contact.php'],
    ['pages/asset_management_example.php', 'src/pages/asset_management_example.php'],
    ['pages/about.php', 'src/pages/about.php'],
    ['pages/404.php', 'src/pages/404.php'],
    
    // Config
    ['config.php', 'src/config/config.php']
];

foreach ($moves as $move) {
    if (file_exists($move[0])) {
        if (copy($move[0], $move[1])) {
            echo "Moved: {$move[0]} -> {$move[1]}\n";
        } else {
            echo "Failed to move: {$move[0]}\n";
        }
    } else {
        echo "Source file not found: {$move[0]}\n";
    }
}

// Move home_sections directory
if (is_dir('pages/home_sections')) {
    if (!is_dir('src/pages/home_sections')) {
        mkdir('src/pages/home_sections', 0777, true);
    }
    $files = glob('pages/home_sections/*');
    foreach ($files as $file) {
        $dest = str_replace('pages/home_sections', 'src/pages/home_sections', $file);
        if (copy($file, $dest)) {
            echo "Moved: {$file} -> {$dest}\n";
        }
    }
}

echo "Reorganization complete!\n"; 