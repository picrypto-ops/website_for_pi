<?php
/**
 * Asset Management Example Page
 * Demonstrates the use of the split hand icons
 */

// Include required files
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/header.php';
?>

<main class="asset-management-page">
    <section class="hero-section">
        <div class="container">
            <h1>Asset Management Services</h1>
            <p class="lead">Providing expert financial guidance to help you grow and protect your assets</p>
        </div>
    </section>

    <section class="icon-demo-section">
        <div class="container">
            <h2>Our Approach</h2>
            
            <div class="row">
                <div class="col-md-6">
                    <h3>Personalized Service</h3>
                    <p>Our team provides hands-on service tailored to your unique financial situation.</p>
                    
                    <?php 
                    // Include the asset management icons component with custom options
                    $options = [
                        'container_class' => 'asset-management-icon-container centered',
                        'show_left_hand' => true,
                        'show_right_hand' => false,
                        'show_combined' => false
                    ];
                    include __DIR__ . '/../components/asset_management_icons.php'; 
                    ?>
                </div>
                
                <div class="col-md-6">
                    <h3>Supportive Guidance</h3>
                    <p>We offer the right support at every step of your financial journey.</p>
                    
                    <?php 
                    // Include the asset management icons component with custom options
                    $options = [
                        'container_class' => 'asset-management-icon-container centered',
                        'show_left_hand' => false,
                        'show_right_hand' => true,
                        'show_combined' => false
                    ];
                    include __DIR__ . '/../components/asset_management_icons.php'; 
                    ?>
                </div>
            </div>
            
            <div class="row mt-5">
                <div class="col-12 text-center">
                    <h3>Partnership That Delivers Results</h3>
                    <p>By working together, we create financial strategies that achieve your goals.</p>
                    
                    <?php 
                    // Include the asset management icons component with default options
                    // This will show all three icons
                    $options = [];
                    include __DIR__ . '/../components/asset_management_icons.php'; 
                    ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
// Include footer
require_once __DIR__ . '/../includes/footer.php';
?> 