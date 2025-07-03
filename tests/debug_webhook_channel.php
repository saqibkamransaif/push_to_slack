<?php
/**
 * Debug webhook channel issue
 * Tests different approaches to fix channel_not_found error
 */

echo "=== Debugging Webhook Channel Issue ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";
echo "========================================\n\n";

// Test the current webhook URL directly
$webhookUrl = 'https://hooks.slack.com/services/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';

echo "1. Testing Current Webhook URL\n";
echo "------------------------------\n";
echo "URL: $webhookUrl\n\n";

// Test 1: Simple message without channel specification
echo "Test 1: Simple message (no channel specified)\n";
$payload1 = [
    'text' => 'Test message from debug script - ' . date('H:i:s')
];

$result1 = sendToWebhook($webhookUrl, $payload1);
echo "Result: " . json_encode($result1) . "\n\n";

// Test 2: Try with channel specified
echo "Test 2: With channel specified\n";
$payload2 = [
    'text' => 'Test message with channel - ' . date('H:i:s'),
    'channel' => '#contract_status_updates'
];

$result2 = sendToWebhook($webhookUrl, $payload2);
echo "Result: " . json_encode($result2) . "\n\n";

// Test 3: Try without # prefix
echo "Test 3: Channel without # prefix\n";
$payload3 = [
    'text' => 'Test message without # - ' . date('H:i:s'),
    'channel' => 'contract_status_updates'
];

$result3 = sendToWebhook($webhookUrl, $payload3);
echo "Result: " . json_encode($result3) . "\n\n";

// Test 4: Try with private channel ID format
echo "Test 4: No channel specified (webhook default)\n";
$payload4 = [
    'text' => 'Test message - webhook default channel - ' . date('H:i:s'),
    'mrkdwn' => true
];

$result4 = sendToWebhook($webhookUrl, $payload4);
echo "Result: " . json_encode($result4) . "\n\n";

echo "Solutions to try:\n";
echo "================\n";
echo "1. Check if the webhook was created for the correct channel\n";
echo "2. Recreate the webhook URL in Slack app settings\n";
echo "3. Use the bot token instead of webhook URL\n";
echo "4. Make sure the channel name matches exactly\n";

function sendToWebhook($url, $payload) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    return [
        'http_code' => $httpCode,
        'response' => $response,
        'curl_error' => $curlError
    ];
}
?>