<?php
/**
 * Debug Slack configuration - checks environment variables and configuration
 */

require_once __DIR__ . '/../config/bootstrap.php';

echo "=== Slack Configuration Debug ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";
echo "==================================\n\n";

echo "1. Environment Variables\n";
echo "------------------------\n";
echo "SLACK_WEBHOOK_NEWLEAD: ";
if (isset($_ENV['SLACK_WEBHOOK_NEWLEAD'])) {
    echo "SET (length: " . strlen($_ENV['SLACK_WEBHOOK_NEWLEAD']) . ")\n";
    echo "  Value: " . $_ENV['SLACK_WEBHOOK_NEWLEAD'] . "\n";
} else {
    echo "NOT SET\n";
}

echo "\nSLACK_BOT_TOKEN: ";
if (isset($_ENV['SLACK_BOT_TOKEN'])) {
    echo "SET (length: " . strlen($_ENV['SLACK_BOT_TOKEN']) . ")\n";
    echo "  Value starts with: " . substr($_ENV['SLACK_BOT_TOKEN'], 0, 10) . "...\n";
} else {
    echo "NOT SET\n";
}

echo "\n2. All Slack-related Environment Variables\n";
echo "-----------------------------------------\n";
foreach ($_ENV as $key => $value) {
    if (strpos($key, 'SLACK') !== false) {
        echo "$key = " . (empty($value) ? '[EMPTY]' : '[SET - length: ' . strlen($value) . ']') . "\n";
    }
}

echo "\n3. Testing SlackModel Initialization\n";
echo "-----------------------------------\n";
require_once BASE_PATH . '/app/models/SlackModel.php';

try {
    $slackModel = new SlackModel();
    echo "✓ SlackModel created successfully\n";
    
    // Test sending notification
    $testData = [
        'customData' => [
            'event-type' => 'newlead',
            'name' => 'Debug Test',
            'email' => 'debug@test.com',
            'phone' => '555-DEBUG',
            'source' => 'Debug Script',
            'assignedTo' => 'Debug Team'
        ]
    ];
    
    echo "\n4. Testing Slack Notification\n";
    echo "-----------------------------\n";
    $result = $slackModel->sendNotification('newlead', $testData);
    
    echo "Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n";
    
} catch (Exception $e) {
    echo "✗ Error creating SlackModel: " . $e->getMessage() . "\n";
}

echo "\n5. File Permissions Check\n";
echo "-------------------------\n";
$envFile = BASE_PATH . '/.env';
echo ".env file exists: " . (file_exists($envFile) ? 'Yes' : 'No') . "\n";
echo ".env file readable: " . (is_readable($envFile) ? 'Yes' : 'No') . "\n";
echo ".env file size: " . (file_exists($envFile) ? filesize($envFile) . ' bytes' : 'N/A') . "\n";

echo "\n6. Raw .env File Content (Slack lines only)\n";
echo "-------------------------------------------\n";
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $lineNum => $line) {
        if (strpos($line, 'SLACK') !== false) {
            echo "Line " . ($lineNum + 1) . ": " . $line . "\n";
        }
    }
} else {
    echo ".env file not found\n";
}

echo "\nDone!\n";