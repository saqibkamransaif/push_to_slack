<?php
/**
 * Test script specifically for newlead event
 * Tests the complete flow: webhook → storage → Slack
 */

// Test configuration
$webhookUrl = 'https://api.saqibkamran.com/webhook.php';

// Sample new lead data matching your format
$testData = [
    'customData' => [
        'event-type' => 'newlead',
        'name' => 'Test Lead - ' . date('Y-m-d H:i:s'),
        'email' => 'testlead@example.com',
        'phone' => '+1 555 TEST',
        'source' => 'API Test',
        'assignedTo' => 'Test Team'
    ]
];

echo "Testing New Lead Webhook to Slack Integration\n";
echo "=============================================\n\n";
echo "Webhook URL: $webhookUrl\n";
echo "Event Type: newlead\n";
echo "Lead Name: " . $testData['customData']['name'] . "\n\n";

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

// Display results
echo "HTTP Status: $httpCode\n";
if ($error) {
    echo "cURL Error: $error\n";
}

echo "\nResponse:\n";
$responseData = json_decode($response, true);
if ($responseData) {
    echo json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
    
    // Check specific fields
    if (isset($responseData['success']) && $responseData['success']) {
        echo "\n✓ Webhook processed successfully\n";
        echo "✓ Webhook ID: " . ($responseData['webhook_id'] ?? 'N/A') . "\n";
        
        if (isset($responseData['slack_notification'])) {
            if ($responseData['slack_notification'] === 'sent') {
                echo "✓ Slack notification sent successfully\n";
            } else {
                echo "✗ Slack notification failed\n";
                echo "  Check your Slack credentials in .env file\n";
            }
        }
    } else {
        echo "\n✗ Webhook processing failed\n";
        if (isset($responseData['error'])) {
            echo "  Error: " . $responseData['error'] . "\n";
        }
    }
} else {
    echo $response . "\n";
}

echo "\nIf Slack notification was sent, check your #sales-leads channel\n";