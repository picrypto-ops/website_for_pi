<?php
// Turn on error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include required files
require_once 'src/utility/classes/translation/TranslatableInterface.php';
require_once 'src/utility/classes/translation/AbstractTranslatable.php';
require_once 'src/utility/classes/translation/GeneralTranslatable.php';
require_once 'src/utility/classes/translation/PageTranslatable.php';
require_once 'src/utility/classes/translation/ProductTranslatable.php';
require_once 'src/utility/classes/translation/SegmentTranslatable.php';
require_once 'src/utility/classes/translation/TeamTranslatable.php';
require_once 'src/utility/classes/translation/TranslatableFactory.php';

// Initialize factory
TranslatableFactory::initialize(['general', 'segments', 'products', 'pages', 'team']);

// Helper function to display data structure
function displayData($data, $depth = 0) {
    $indent = str_repeat('&nbsp;&nbsp;', $depth);
    
    if (is_array($data)) {
        echo '<ul style="margin: 0; padding-left: 20px;">';
        foreach ($data as $key => $value) {
            echo '<li>';
            echo $indent . '<strong>' . htmlspecialchars($key) . '</strong>: ';
            if (is_array($value)) {
                echo '<span style="cursor:pointer;" onclick="this.nextElementSibling.style.display=this.nextElementSibling.style.display==\'none\'?\'block\':\'none\';">[+]</span>';
                echo '<div style="display:none;">';
                displayData($value, $depth + 1);
                echo '</div>';
            } else {
                echo htmlspecialchars(substr($value, 0, 100)) . (strlen($value) > 100 ? '...' : '');
            }
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo htmlspecialchars($data);
    }
}

$dataType = isset($_GET['type']) ? $_GET['type'] : 'general';
$validTypes = ['general', 'segments', 'products', 'pages', 'team'];

if (!in_array($dataType, $validTypes)) {
    $dataType = 'general';
}

$data = TranslatableFactory::getData($dataType);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Debugger</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 20px; }
        h1 { color: #333; }
        .data-nav { margin-bottom: 20px; }
        .data-nav a { 
            display: inline-block; 
            margin-right: 10px; 
            padding: 5px 10px; 
            background: #eee; 
            text-decoration: none;
            color: #333;
            border-radius: 3px;
        }
        .data-nav a.active { background: #007bff; color: white; }
        .data-container { 
            border: 1px solid #ddd; 
            padding: 20px;
            border-radius: 5px;
            background: #f9f9f9;
        }
    </style>
</head>
<body>
    <h1>Data Structure Debugger</h1>
    
    <div class="data-nav">
        <?php foreach ($validTypes as $type): ?>
            <a href="?type=<?php echo $type; ?>" class="<?php echo $dataType === $type ? 'active' : ''; ?>">
                <?php echo ucfirst($type); ?>
            </a>
        <?php endforeach; ?>
    </div>
    
    <div class="data-container">
        <h2><?php echo ucfirst($dataType); ?> Data Structure</h2>
        <?php displayData($data); ?>
    </div>
</body>
</html> 