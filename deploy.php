<?php
/**
 * Deployment Script Using WinSCP
 * ==============================
 * 
 * This script uses WinSCP to copy files to the server using a saved connection profile.
 */

// Configuration
$config = [
    'winscp_path' => 'C:/Program Files (x86)/WinSCP/WinSCP.com', // Update this path if needed
    // 'connection_name' => 'vibit_pi_hf_storage', // Your saved WinSCP connection name
    'connection_name' => 'InfinityFree', // Your saved WinSCP connection name
    'local_paths' => [
        './pi_website_take5/vendor',
        './pi_website_take5/assets/css',
        './pi_website_take5/assets/images',
        './pi_website_take5/assets/js',
        './pi_website_take5/src',
        './index.php'
    ],
    'cache_path' => './pi_website_take5/cache'
];

// First, run the prepare deploy script to disable debug files
echo "Running deployment preparation script...\n";
if (file_exists('./pi_website_take5/utility_files/prepare_deploy.php')) {
    include './pi_website_take5/utility_files/prepare_deploy.php';
    echo "\nPreparation completed.\n\n";
} else {
    echo "Warning: Preparation script not found. Continuing with deployment...\n\n";
}

// Create a WinSCP script file
$scriptFile = 'winscp_deploy_' . time() . '.txt';
$script = "# Automatically generated WinSCP script\n";

// Connection settings using saved profile
$script .= "open {$config['connection_name']}\n";

// Set file transfer options
$script .= "option transfer binary\n";
$script .= "option confirm off\n";

// Handle cache directory - using proper WinSCP scripting syntax
$script .= "# Handle cache directory\n";
$script .= "cd /\n"; // Start from root directory
$script .= "call mkdir pi_website_take5\n"; // Create main dir if it doesn't exist
$script .= "call mkdir pi_website_take5/cache\n"; // Try to create cache dir
$script .= "rm pi_website_take5/cache/* -norecurse\n"; // Clear cache files but not subdirectories if any

// Copy each directory/file
$script .= "# Copy directories and files\n";
foreach ($config['local_paths'] as $localPath) {
    $remotePath = str_replace('./', '', $localPath);
    
    if (is_dir($localPath)) {
        // For directories, create remote directory first to ensure it exists
        $script .= "call mkdir \"$remotePath\"\n";
        // Use synchronize to copy content
        $script .= "synchronize remote -delete \"{$localPath}\" \"/{$remotePath}\"\n";
    } else {
        // For files, upload directly
        $remoteDirPath = dirname($remotePath);
        // Create remote directory structure if needed
        $script .= "call mkdir \"$remoteDirPath\"\n";
        $script .= "put -overwrite \"{$localPath}\" \"/{$remotePath}\"\n";
    }
}

// Set permissions (using WinSCP chmod command)
$script .= "# Set permissions\n";
$script .= "chmod 755 /pi_website_take5/src -recurse\n"; // Directories and PHP files
$script .= "chmod 644 /pi_website_take5/assets -recurse\n"; // Static asset files

// Close connection
$script .= "exit\n";

// Write script to file
file_put_contents($scriptFile, $script);

echo "Deploying files to server using saved connection '{$config['connection_name']}'...\n";

// Run WinSCP with the script
$command = "\"{$config['winscp_path']}\" /script=\"{$scriptFile}\" /log=\"winscp_deploy.log\"";
$output = [];
$returnCode = 0;

exec($command, $output, $returnCode);

// Check results
if ($returnCode === 0) {
    echo "Deployment completed successfully!\n";
} else {
    echo "Deployment failed with error code {$returnCode}\n";
    echo "Check winscp_deploy.log for details\n";
}

// Clean up
unlink($scriptFile);
echo "Script file removed\n";
echo "WinSCP log saved as winscp_deploy.log\n";

echo "\nDeployment process finished.\n";
?> 