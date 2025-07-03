<?php
/**
 * Test environment variable loading after fix
 */

require_once __DIR__ . '/../config/bootstrap.php';

echo "=== Environment Loading Test ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";
echo "================================\n\n";

echo "1. Checking .env file loading\n";
echo "-----------------------------\n";
$envFile = BASE_PATH . '/.env';
echo ".env file path: $envFile\n";
echo ".env file exists: " . (file_exists($envFile) ? 'Yes' : 'No') . "\n";

if (file_exists($envFile)) {
    echo ".env file size: " . filesize($envFile) . " bytes\n";
    echo ".env file readable: " . (is_readable($envFile) ? 'Yes' : 'No') . "\n";
}

echo "\n2. Slack Configuration\n";
echo "----------------------\n";
echo "SLACK_WEBHOOK_NEWLEAD: ";
if (isset($_ENV['SLACK_WEBHOOK_NEWLEAD'])) {
    echo "LOADED (length: " . strlen($_ENV['SLACK_WEBHOOK_NEWLEAD']) . ")\n";
    echo "  Starts with: " . substr($_ENV['SLACK_WEBHOOK_NEWLEAD'], 0, 30) . "...\n";
} else {
    echo "NOT LOADED\n";
}

echo "\nSLACK_BOT_TOKEN: ";
if (isset($_ENV['SLACK_BOT_TOKEN'])) {
    echo "LOADED (length: " . strlen($_ENV['SLACK_BOT_TOKEN']) . ")\n";
    echo "  Starts with: " . substr($_ENV['SLACK_BOT_TOKEN'], 0, 10) . "...\n";
} else {
    echo "NOT LOADED\n";
}

echo "\n3. All loaded environment variables\n";
echo "-----------------------------------\n";
$count = 0;
foreach ($_ENV as $key => $value) {
    if (!empty($value)) {
        echo "$key = " . (strlen($value) > 50 ? substr($value, 0, 47) . '...' : $value) . "\n";
        $count++;
    }
}
echo "\nTotal loaded: $count variables\n";

echo "\n4. Testing SlackModel with new config\n";
echo "------------------------------------\n";
require_once BASE_PATH . '/app/models/SlackModel.php';

try {
    $slackModel = new SlackModel();
    echo "✓ SlackModel created successfully\n";
    
    // Test data
    $testData = [
        'customData' => [
            'event-type' => 'newlead',
            'name' => 'Environment Test',
            'email' => 'envtest@example.com',
            'phone' => '+1 555 ENV',
            'source' => 'Environment Test',
            'assignedTo' => 'Test Team'
        ]
    ];
    
    echo "\nTesting notification...\n";
    $result = $slackModel->sendNotification('newlead', $testData);
    
    if ($result['success']) {
        echo "✓ SUCCESS: Slack notification sent!\n";
        echo "Check your Slack channel for the test message.\n";
    } else {
        echo "✗ FAILED: " . ($result['error'] ?? 'Unknown error') . "\n";
        if (isset($result['debug'])) {
            echo "Debug info: " . json_encode($result['debug'], JSON_PRETTY_PRINT) . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";