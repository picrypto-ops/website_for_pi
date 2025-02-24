<?php
$segmentId = isset($_GET['id']) ? $_GET['id'] : '';
$segments = loadJsonData('segments');
$products = loadJsonData('products');

$segment = null;
foreach ($segments as $s) {
    if ($s['id'] === $segmentId) {
        $segment = $s;
        break;
    }
}

if (!$segment) {
    include 'pages/404.php';
    exit;
}

$segmentProducts = array_filter($products, function($product) use ($segmentId) {
    return $product['segment'] === $segmentId;
});
?>

<section class="segment">
    <div class="container">
        <h1><?php echo t($segment['name']); ?></h1>
        <p class="lead"><?php echo t($segment['short_description']); ?></p>
        <div class="segment-description">
            <?php echo t($segment['long_description']); ?>
        </div>

        <h2><?php echo t('our_products'); ?></h2>
        <div class="product-grid">
            <?php foreach ($segmentProducts as $product): ?>
                <div class="product-card">
                    <h3><?php echo t($product['name']); ?></h3>
                    <p><?php echo t($product['description']); ?></p>
                    <a href="?page=product&id=<?php echo $product['id']; ?>&lang=<?php echo $lang; ?>" class="button"><?php echo t('learn_more'); ?></a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

