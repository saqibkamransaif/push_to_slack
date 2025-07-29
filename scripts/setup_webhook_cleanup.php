<?php
/**
 * Setup Webhook Cleanup Automation
 * This script helps configure automatic webhook cleanup
 * 
 * Usage: php setup_webhook_cleanup.php [enable|disable|status]
 */

require_once __DIR__ . '/../config/bootstrap.php';

/**
 * Displays current cleanup configuration
 * @return void
 */
function showCleanupStatus() {
    $enabled = filter_var($_ENV['WEBHOOK_CLEANUP_ENABLED'] ?? 'true', FILTER_VALIDATE_BOOLEAN);
    $hours = $_ENV['WEBHOOK_CLEANUP_HOURS'] ?? 12;
    $webhooksDir = BASE_PATH . '/webhooks';
    
    echo "Webhook Cleanup Configuration:\n";
    echo "================================\n";
    echo "Status: " . ($enabled ? "ENABLED" : "DISABLED") . "\n";
    echo "Cleanup after: {$hours} hours\n";
    echo "Webhooks directory: {$webhooksDir}\n";
    
    if (is_dir($webhooksDir)) {
        $files = glob($webhooksDir . '/*.json');
        $count = count($files);
        echo "Current webhook files: {$count}\n";
        
        if ($count > 0) {
            // Show age of oldest and newest files
            $fileTimes = [];
            foreach ($files as $file) {
                $fileTimes[] = filemtime($file);
            }
            
            $oldest = min($fileTimes);
            $newest = max($fileTimes);
            $oldestAge = (time() - $oldest) / 3600; // hours
            $newestAge = (time() - $newest) / 3600; // hours
            
            echo "Oldest file: " . round($oldestAge, 1) . " hours old\n";
            echo "Newest file: " . round($newestAge, 1) . " hours old\n";
        }
    } else {
        echo "Webhooks directory does not exist\n";
    }
    
    echo "\nRecommended cron job:\n";
    echo "# Run webhook cleanup every hour\n";
    echo "0 * * * * /usr/bin/php " . BASE_PATH . "/scripts/cleanup_logs.php\n";
}

/**
 * Runs cleanup manually for testing
 * @return void
 */
function runCleanupTest() {
    echo "Running manual cleanup test...\n";
    echo "==============================\n";
    
    // Include and run the cleanup script
    include BASE_PATH . '/scripts/cleanup_logs.php';
}

/**
 * Updates cleanup configuration
 * @param bool $enabled Whether to enable cleanup
 * @return void
 */
function updateCleanupConfig($enabled) {
    $envFile = BASE_PATH . '/.env';
    
    if (!file_exists($envFile)) {
        echo "Error: .env file not found\n";
        return;
    }
    
    $lines = file($envFile, FILE_IGNORE_NEW_LINES);
    $updated = false;
    
    foreach ($lines as $i => $line) {
        if (strpos($line, 'WEBHOOK_CLEANUP_ENABLED=') === 0) {
            $lines[$i] = 'WEBHOOK_CLEANUP_ENABLED=' . ($enabled ? 'true' : 'false');
            $updated = true;
            break;
        }
    }
    
    if ($updated) {
        file_put_contents($envFile, implode("\n", $lines));
        echo "Updated WEBHOOK_CLEANUP_ENABLED to " . ($enabled ? 'true' : 'false') . "\n";
    } else {
        echo "WEBHOOK_CLEANUP_ENABLED setting not found in .env\n";
    }
}

// Main execution
$command = $argv[1] ?? 'status';

switch ($command) {
    case 'enable':
        updateCleanupConfig(true);
        showCleanupStatus();
        break;
        
    case 'disable':
        updateCleanupConfig(false);
        showCleanupStatus();
        break;
        
    case 'test':
        runCleanupTest();
        break;
        
    case 'status':
    default:
        showCleanupStatus();
        break;
}

echo "\nUsage: php setup_webhook_cleanup.php [enable|disable|test|status]\n";