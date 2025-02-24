<?php
define('CACHE_DIR', __DIR__ . '/../cache/');

function getCache($key) {
    $filename = CACHE_DIR . md5($key) . '.cache';
    if (file_exists($filename) && (filemtime($filename) > (time() - 3600))) {
        return file_get_contents($filename);
    }
    return false;
}

function setCache($key, $data, $expiry = 3600) {
    if (!file_exists(CACHE_DIR)) {
        mkdir(CACHE_DIR, 0755, true);
    }
    $filename = CACHE_DIR . md5($key) . '.cache';
    file_put_contents($filename, $data);
}

function clearCache($key = null) {
    if ($key === null) {
        array_map('unlink', glob(CACHE_DIR . '*.cache'));
    } else {
        $filename = CACHE_DIR . md5($key) . '.cache';
        if (file_exists($filename)) {
            unlink($filename);
        }
    }
}

