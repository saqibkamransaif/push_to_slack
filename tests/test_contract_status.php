<?php
/**
 * Test script for contract_status_updates event
 */

require_once __DIR__ . '/../config/bootstrap.php';

echo "=== Contract Status Updates Test ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";
echo "====================================\n\n";

// Test configuration
$webhookUrl = 'https://api.saqibkamran.com/webhook.php';

// Sample contract status update data
$testData = [
    'customData' => [
        'event-type' => 'contract_status_updates',
        'documentTitle' => 'Annual Service Agreement - Tech Corp',
        'clientName' => 'Tech Corp Inc.',
        'clientEmail' => 'contact@techcorp.com',
        'status' => 'Approved',
        'time' => date('Y-m-d H:i:s')
    ]
];

echo "Testing Contract Status Update Webhook\n";
echo "=====================================\n";
echo "Webhook URL: $webhookUrl\n";
echo "Event Type: contract_status_updates\n";
echo "Document: " . $testData['customData']['documentTitle'] . "\n";
echo "Client: " . $testData['customData']['clientName'] . " (" . $testData['customData']['clientEmail'] . ")\n\n";

// Check if webhook URL is configured
echo "Checking configuration...\n";
if (isset($_ENV['SLACK_WEBHOOK_CONTRACT_STATUS_UPDATES']) && !empty($_ENV['SLACK_WEBHOOK_CONTRACT_STATUS_UPDATES'])) {
    echo "✓ Contract status webhook URL configured\n";
    echo "  URL: " . substr($_ENV['SLACK_WEBHOOK_CONTRACT_STATUS_UPDATES'], 0, 50) . "...\n";
} else {
    echo "✗ Contract status webhook URL not configured\n";
}

echo "\nSending webhook...\n";

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
echo "\nResults:\n";
echo "--------\n";
echo "HTTP Status: $httpCode\n";
if ($error) {
    echo "cURL Error: $error\n";
}

echo "\nResponse:\n";
$responseData = json_decode($response, true);
if ($responseData) {
    echo json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
    
    if (isset($responseData['success']) && $responseData['success']) {
        echo "\n✓ Webhook processed successfully\n";
        echo "✓ Webhook ID: " . ($responseData['webhook_id'] ?? 'N/A') . "\n";
        
        if (isset($responseData['slack_notification'])) {
            if ($responseData['slack_notification'] === 'sent') {
                echo "✓ Slack notification sent to #contract_status_updates channel\n";
                echo "Check your Slack channel for the contract update message.\n";
            } else {
                echo "✗ Slack notification failed\n";
                if (isset($responseData['slack_error'])) {
                    echo "  Error: " . $responseData['slack_error'] . "\n";
                }
            }
        }
    } else {
        echo "\n✗ Webhook processing failed\n";
        if (isset($responseData['error'])) {
            echo "  Error: " . $responseData['error'] . "\n";
        }
    }
} else {
    echo "Failed to parse response:\n";
    echo $response . "\n";
}

echo "\nTest completed!\n";