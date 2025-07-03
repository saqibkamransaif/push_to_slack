<?php
/**
 * Log cleanup script - removes logs older than 24 hours
 * Schedule this script to run via cron every hour
 * Cron example: 0 * * * * /usr/bin/php /path/to/cleanup_logs.php
 */

require_once __DIR__ . '/../config/bootstrap.php';

$logsDir = BASE_PATH . '/logs';
$webhooksDir = BASE_PATH . '/webhooks';
$maxAge = 24 * 60 * 60; // 24 hours in seconds

echo "Log Cleanup Script - " . date('Y-m-d H:i:s') . "\n";
echo "======================================\n\n";

/**
 * Cleans up files older than specified age
 * @param string $directory Directory to clean
 * @param int $maxAge Maximum age in seconds
 * @param string $pattern File pattern to match
 * @return int Number of files deleted
 */
function cleanupOldFiles($directory, $maxAge, $pattern = '*') {
    $deleted = 0;
    $currentTime = time();
    
    if (!is_dir($directory)) {
        echo "Directory not found: $directory\n";
        return 0;
    }
    
    $files = glob($directory . '/' . $pattern);
    
    foreach ($files as $file) {
        if (is_file($file)) {
            $fileAge = $currentTime - filemtime($file);
            
            if ($fileAge > $maxAge) {
                if (unlink($file)) {
                    echo "Deleted: " . basename($file) . " (age: " . round($fileAge / 3600, 1) . " hours)\n";
                    $deleted++;
                } else {
                    echo "Failed to delete: " . basename($file) . "\n";
                }
            }
        }
    }
    
    return $deleted;
}

// Clean up log files
echo "Cleaning up log files older than 24 hours...\n";
$logPatterns = ['*.log', '*.log.*'];
$totalDeleted = 0;

foreach ($logPatterns as $pattern) {
    $deleted = cleanupOldFiles($logsDir, $maxAge, $pattern);
    $totalDeleted += $deleted;
}

echo "Deleted $totalDeleted log files\n\n";

// Clean up webhook JSON files (optional - uncomment if needed)
// echo "Cleaning up webhook files older than 24 hours...\n";
// $webhookDeleted = cleanupOldFiles($webhooksDir, $maxAge, '*.json');
// echo "Deleted $webhookDeleted webhook files\n\n";

// Log the cleanup activity
require_once BASE_PATH . '/app/models/Logger.php';
$logger = Logger::getInstance();
$logger->info('Log cleanup completed', [
    'logs_deleted' => $totalDeleted,
    'execution_time' => time() - $_SERVER['REQUEST_TIME_FLOAT']
]);

echo "\nCleanup completed successfully!\n";