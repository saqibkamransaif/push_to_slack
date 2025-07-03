<?php
/**
 * Test all contract status variations with emojis
 */

require_once __DIR__ . '/../config/bootstrap.php';

echo "=== Contract Status Variations Test ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";
echo "======================================\n\n";

// Test configuration
$webhookUrl = 'https://api.saqibkamran.com/webhook.php';

// Test all 4 status types
$statuses = ['Sent', 'Viewed', 'Signed', 'Completed'];

foreach ($statuses as $status) {
    echo "Testing status: $status\n";
    echo str_repeat('-', 30) . "\n";
    
    // Sample contract data
    $testData = [
        'customData' => [
            'event-type' => 'contract_status_updates',
            'documentTitle' => 'Service Agreement - Test Contract',
            'clientName' => 'Test Client',
            'clientEmail' => 'client@example.com',
            'status' => $status,
            'time' => date('n/j/Y \a\t h:i A')
        ]
    ];
    
    echo "Sending webhook for status: $status\n";
    
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
    curl_close($ch);
    
    $responseData = json_decode($response, true);
    
    if ($httpCode === 200 && isset($responseData['slack_notification']) && $responseData['slack_notification'] === 'sent') {
        echo "✓ SUCCESS: $status message sent to Slack\n";
    } else {
        echo "✗ FAILED: $status message failed\n";
        if (isset($responseData['slack_error'])) {
            echo "  Error: " . $responseData['slack_error'] . "\n";
        }
    }
    
    echo "\n";
    sleep(1); // Small delay between requests
}

echo "Expected message formats:\n";
echo "========================\n";
echo "📤 *Contract Sent*\n";
echo "👀 *Contract Viewed*\n";
echo "✍️ *Contract Signed*\n";
echo "✅ *Contract Completed*\n\n";

echo "Check your #contract_status_updates channel for all 4 test messages!\n";