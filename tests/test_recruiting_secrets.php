<?php
/**
 * Test script for recruitingsecretsdownload event
 */

require_once __DIR__ . '/../config/bootstrap.php';

echo "=== Recruiting Secrets Download Test ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";
echo "========================================\n\n";

// Test configuration
$webhookUrl = 'https://api.saqibkamran.com/webhook.php';

// Sample recruiting secrets download data matching your format
$testData = [
    'customData' => [
        'event-type' => 'recruitingsecretsdownload',
        'name' => 'John Recruiter',
        'email' => 'john@company.com',
        'phone' => '+1 555 0199',
        'source' => 'LinkedIn Campaign',
        'assignedTo' => 'Recruitment Team'
    ]
];

echo "Testing Recruiting Secrets Download Webhook\n";
echo "==========================================\n";
echo "Webhook URL: $webhookUrl\n";
echo "Event Type: recruitingsecretsdownload\n";
echo "Name: " . $testData['customData']['name'] . "\n";
echo "Email: " . $testData['customData']['email'] . "\n";
echo "Channel: lead-flow\n\n";

// Check if webhook URL is configured
echo "Checking configuration...\n";
if (isset($_ENV['SLACK_WEBHOOK_RECRUITINGSECRETSDOWNLOAD']) && !empty($_ENV['SLACK_WEBHOOK_RECRUITINGSECRETSDOWNLOAD'])) {
    echo "✓ Recruiting secrets webhook URL configured\n";
    echo "  URL: " . substr($_ENV['SLACK_WEBHOOK_RECRUITINGSECRETSDOWNLOAD'], 0, 50) . "...\n";
} else {
    echo "✗ Recruiting secrets webhook URL not configured\n";
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
                echo "✓ Slack notification sent to #lead-flow channel\n";
                echo "Check your Slack channel for the recruiting secrets download message.\n";
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

echo "\nExpected Slack message format:\n";
echo "------------------------------\n";
echo "📜 *Recruiting Secrets Downloaded*\n";
echo "Name: John Recruiter\n";
echo "Email: john@company.com\n";
echo "Phone: +1 555 0199\n";
echo "Source: LinkedIn Campaign\n";
echo "Assigned to: Recruitment Team\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n";

echo "\nTest completed!\n";