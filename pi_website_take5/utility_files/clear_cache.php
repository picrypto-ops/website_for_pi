<?php
/**
 * Cache Clearing Script
 * Removes all files from the cache directory
 */

// Define the cache directory path
$cacheDir = __DIR__ . '/cache';

if (is_dir($cacheDir)) {
    // Get all files in the cache directory
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cacheDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $fileinfo) {
        if ($fileinfo->isDir()) {
            // Remove directories
            rmdir($fileinfo->getRealPath());
        } else {
            // Remove files
            unlink($fileinfo->getRealPath());
        }
    }

    echo "Cache cleared successfully!";
} else {
    echo "Cache directory not found.";
} 