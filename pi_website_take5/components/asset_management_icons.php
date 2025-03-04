<?php
/**
 * Asset Management Icons Component
 * Displays the hand icons for asset management section
 * 
 * @param array $options Optional configuration options
 */

// Default options
$defaults = [
    'container_class' => 'asset-management-icon-container',
    'show_left_hand' => true,
    'show_right_hand' => true,
    'show_combined' => true,
    'container_id' => null
];

// Merge with provided options
$options = isset($options) ? array_merge($defaults, $options) : $defaults;
$container_id = $options['container_id'] ? 'id="' . $options['container_id'] . '"' : '';
?>

<div class="<?php echo $options['container_class']; ?>" <?php echo $container_id; ?>>
    <?php if ($options['show_left_hand']) : ?>
        <div class="asset-management-left-hand" aria-label="Left hand icon">
            <!-- SVG will be inserted by JavaScript -->
        </div>
    <?php endif; ?>
    
    <?php if ($options['show_right_hand']) : ?>
        <div class="asset-management-right-hand" aria-label="Right hand icon">
            <!-- SVG will be inserted by JavaScript -->
        </div>
    <?php endif; ?>
    
    <?php if ($options['show_combined']) : ?>
        <div class="asset-management-combined-hands" aria-label="Combined hands icon">
            <!-- SVG will be inserted by JavaScript -->
        </div>
    <?php endif; ?>
</div>

