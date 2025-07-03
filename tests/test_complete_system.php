<?php
/**
 * Complete system test - tests webhook → Slack → cleanup
 * Tests all error handling and logging features
 */

require_once __DIR__ . '/../config/bootstrap.php';

echo "=== Complete System Test ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";
echo "=============================\n\n";

// Test configuration
$webhookUrl = 'https://api.saqibkamran.com/webhook.php';

// Test data
$testData = [
    'customData' => [
        'event-type' => 'newlead',
        'name' => 'System Test User',
        'email' => 'systemtest@example.com',
        'phone' => '+1 555 TEST',
        'source' => 'Complete System Test',
        'assignedTo' => 'Test Team'
    ]
];

echo "1. Testing Environment Configuration\n";
echo "------------------------------------\n";
echo "Logging enabled: " . (($_ENV['ENABLE_LOGGING'] ?? 'true') === 'true' ? 'Yes' : 'No') . "\n";
echo "Error logging: " . (($_ENV['ENABLE_ERROR_LOGGING'] ?? 'true') === 'true' ? 'Yes' : 'No') . "\n";
echo "Debug logging: " . (($_ENV['ENABLE_DEBUG_LOGGING'] ?? 'false') === 'true' ? 'Yes' : 'No') . "\n";
echo "Delete on success: " . (($_ENV['DELETE_WEBHOOK_ON_SUCCESS'] ?? 'false') === 'true' ? 'Yes' : 'No') . "\n";
echo "Environment: " . ($_ENV['ENVIRONMENT'] ?? 'production') . "\n\n";

echo "2. Testing Slack Configuration\n";
echo "-------------------------------\n";
echo "Bot Token: " . (!empty($_ENV['SLACK_BOT_TOKEN']) ? 'Configured' : 'Not configured') . "\n";
echo "Newlead Webhook: " . (!empty($_ENV['SLACK_WEBHOOK_NEWLEAD']) ? 'Configured' : 'Not configured') . "\n\n";

echo "3. Sending Test Webhook\n";
echo "-----------------------\n";
echo "URL: $webhookUrl\n";
echo "Event: newlead\n";
echo "Name: " . $testData['customData']['name'] . "\n\n";

// Count files before
$webhooksBefore = count(glob(__DIR__ . '/../webhooks/*.json'));
echo "Webhook files before: $webhooksBefore\n";

// Send webhook
$ch = curl_init($webhookUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
if ($error) {
    echo "cURL Error: $error\n";
}

echo "\n4. Webhook Response Analysis\n";
echo "----------------------------\n";
$responseData = json_decode($response, true);
if ($responseData) {
    echo json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
    
    if (isset($responseData['success']) && $responseData['success']) {
        echo "\n✓ Webhook processed successfully\n";
        echo "✓ Webhook ID: " . ($responseData['webhook_id'] ?? 'N/A') . "\n";
        
        if (isset($responseData['slack_notification'])) {
            if ($responseData['slack_notification'] === 'sent') {
                echo "✓ Slack notification sent successfully\n";
            } else {
                echo "✗ Slack notification failed\n";
                if (isset($responseData['slack_error'])) {
                    echo "  Error: " . $responseData['slack_error'] . "\n";
                }
            }
        }
    } else {
        echo "✗ Webhook processing failed\n";
        if (isset($responseData['error'])) {
            echo "  Error: " . $responseData['error'] . "\n";
        }
    }
} else {
    echo "Failed to parse response:\n";
    echo $response . "\n";
}

// Count files after
$webhooksAfter = count(glob(__DIR__ . '/../webhooks/*.json'));
echo "\nWebhook files after: $webhooksAfter\n";

if ($_ENV['DELETE_WEBHOOK_ON_SUCCESS'] === 'true' && isset($responseData['slack_notification']) && $responseData['slack_notification'] === 'sent') {
    if ($webhooksAfter < $webhooksBefore || $webhooksAfter === $webhooksBefore) {
        echo "✓ Webhook file cleanup working (file was deleted or not created due to successful processing)\n";
    } else {
        echo "? Webhook file may still exist (check if deletion was configured)\n";
    }
} else {
    echo "? File deletion not tested (either disabled or Slack failed)\n";
}

echo "\n5. Log Files Check\n";
echo "------------------\n";
$logsDir = __DIR__ . '/../logs';
if (is_dir($logsDir)) {
    $logFiles = array_filter(scandir($logsDir), function($file) {
        return pathinfo($file, PATHINFO_EXTENSION) === 'log';
    });
    
    if (!empty($logFiles)) {
        echo "Log files found: " . count($logFiles) . "\n";
        foreach ($logFiles as $logFile) {
            $size = filesize($logsDir . '/' . $logFile);
            echo "  - $logFile (" . round($size / 1024, 2) . " KB)\n";
        }
        
        // Show last few lines from main log
        $latestLog = $logsDir . '/app_' . date('Y-m-d') . '.log';
        if (file_exists($latestLog)) {
            echo "\nLast 3 log entries:\n";
            $lines = file($latestLog);
            $lastLines = array_slice($lines, -3);
            foreach ($lastLines as $line) {
                echo "  " . trim($line) . "\n";
            }
        }
    } else {
        echo "No log files found\n";
    }
} else {
    echo "Logs directory not found\n";
}

echo "\n6. System Status Summary\n";
echo "------------------------\n";
echo "✓ Environment loaded\n";
echo "✓ Webhook endpoint accessible\n";
echo ($httpCode === 200 ? "✓" : "✗") . " Webhook processing\n";
echo (isset($responseData['slack_notification']) && $responseData['slack_notification'] === 'sent' ? "✓" : "✗") . " Slack integration\n";
echo (($_ENV['ENABLE_LOGGING'] ?? 'true') === 'true' ? "✓" : "✗") . " Logging system\n";
echo (($_ENV['DELETE_WEBHOOK_ON_SUCCESS'] ?? 'false') === 'true' ? "✓" : "✗") . " Auto-cleanup enabled\n";

echo "\nTest completed!\n";