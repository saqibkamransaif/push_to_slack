<?php
/**
 * Direct Slack test without environment variables
 * Use this to test your Slack webhook directly
 */

// Replace this with your actual webhook URL
$webhookUrl = 'https://hooks.slack.com/services/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';

$message = ':sparkles: *Test Message from Direct Script*
Name: Test User
Email: test@example.com
Phone: +1 555 0000
Source: Direct Test
Assigned to: Test Team
Time: ' . date('Y-m-d H:i:s');

echo "Testing Slack Webhook Directly\n";
echo "==============================\n";
echo "URL: $webhookUrl\n";
echo "Message length: " . strlen($message) . " characters\n\n";

$payload = [
    'text' => $message,
    'mrkdwn' => true
];

$ch = curl_init($webhookUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "Results:\n";
echo "--------\n";
echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";

if ($curlError) {
    echo "cURL Error: $curlError\n";
}

if ($httpCode === 200 && $response === 'ok') {
    echo "\n✓ SUCCESS: Message sent to Slack!\n";
    echo "Check your Slack channel for the test message.\n";
} else {
    echo "\n✗ FAILED: Could not send to Slack\n";
    echo "Check your webhook URL and Slack app configuration.\n";
}

echo "\nIf this works, the issue is with environment variable loading.\n";
echo "If this fails, check your Slack webhook URL configuration.\n";