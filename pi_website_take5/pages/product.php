<?php
$productId = isset($_GET['id']) ? $_GET['id'] : '';
$products = loadJsonData('products');
$team = loadJsonData('team');

$product = null;
foreach ($products as $p) {
    if ($p['id'] === $productId) {
        $product = $p;
        break;
    }
}

if (!$product) {
    include 'pages/404.php';
    exit;
}

$productTeam = array_filter($team, function($member) use ($product) {
    return in_array($product['team_group'], $member['groups']);
});
?>

<section class="product">
    <div class="container">
        <h1><?php echo t($product['name']); ?></h1>
        <div class="product-description">
            <?php echo t($product['description']); ?>
        </div>

        <?php if (!empty($productTeam)): ?>
            <h2><?php echo t('product_team'); ?></h2>
            <div class="team-grid">
                <?php foreach ($productTeam as $member): ?>
                    <div class="team-card">
                        <img src="assets/images/team/<?php echo $member['image']; ?>" alt="<?php echo $member['name']; ?>">
                        <h3><?php echo $member['name']; ?></h3>
                        <p><?php echo $member['title']; ?></p>
                        <a href="?page=team-member&id=<?php echo $member['id']; ?>&lang=<?php echo $lang; ?>" class="button"><?php echo t('view_profile'); ?></a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

