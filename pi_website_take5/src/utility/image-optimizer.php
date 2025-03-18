<?php
function optimizeImage($sourceFile, $destinationFile, $quality = 80) {
    if (!extension_loaded('imagick')) {
        throw new Exception('Imagick extension is not available');
    }

    $image = new Imagick($sourceFile);
    $image->setImageFormat('webp');
    $image->setImageCompressionQuality($quality);
    $image->writeImage($destinationFile);
    $image->destroy();
}

function ensureWebPVersion($imagePath) {
    $webpPath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $imagePath);
    
    if (!file_exists($webpPath)) {
        optimizeImage($imagePath, $webpPath);
    }
    
    return $webpPath;
}

