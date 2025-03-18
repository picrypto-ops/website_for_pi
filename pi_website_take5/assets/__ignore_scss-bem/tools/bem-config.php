<?php
/**
 * BEM Tools Configuration
 * 
 * Central configuration file for all BEM tools
 * This file is used by all the BEM tools to ensure consistent paths
 */

// Server paths
$rootPath = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
$projectRoot = dirname(dirname(dirname(__DIR__))); // Up from assets/scss-bem/tools to project root

// Configuration for CSS paths and page URLs
return [
    // SCSS compilation paths
    'scss' => [
        // Source SCSS path
        'source' => dirname(__DIR__) . '/main.scss',
        
        // SASS compiler - either auto-detect or specify path
        'compiler' => [
            'auto_detect' => true,  // Set to false to use specific path
            'path' => 'C:\\Users\\RonenBitman\\AppData\\Roaming\\npm\\sass.ps1',
        ]
    ],
    
    // CSS output paths
    'css' => [
        // Original CSS file paths (in order of preference)
        'original' => [
            $projectRoot . '/assets/css/main.css',  // Primary location
            $projectRoot . '/css/main.css',         // Alternative 
        ],
        
        // BEM CSS file paths (in order of preference)
        'bem' => [
            $projectRoot . '/assets/css/main-bem.css',  // Primary location  
            $projectRoot . '/css/main-bem.css',         // Alternative
        ]
    ],
    
    // Web URLs (relative to web root)
    'urls' => [
        // CSS web URLs - changed to relative paths to match what's in the HTML
        'css' => [
            'original' => 'assets/css/main.css',  // Removed leading slash
            'bem' => 'assets/css/main-bem.css',   // Removed leading slash
        ],
        
        // Page URLs for testing
        'pages' => [
            'home' => '/index.php',
            'about' => '/about.php',
            'contact' => '/contact.php',
        ]
    ],
    
    // Tools paths
    'tools' => [
        'css_finder' => 'css-finder.php',
        'style_tester' => 'style-tester.php',
        'visual_diff' => 'style-visual-diff.php',
        'compile_bem' => 'compile-bem.php',
        'create_test_css' => 'create-test-css.php',
    ]
];
?> 