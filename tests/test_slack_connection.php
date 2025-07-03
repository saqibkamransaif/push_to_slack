<?php
/**
 * Test Slack connection directly
 * Verifies that Slack credentials are working
 */

require_once __DIR__ . '/../config/bootstrap.php';
require_once BASE_PATH . '/app/models/SlackModel.php';

echo "Testing Slack Connection\n";
echo "========================\n\n";

// Test data
$testData = [
    'customData' => [
        'event-type' => 'newlead',
        'name' => 'Connection Test',
        'email' => 'test@example.com',
        'phone' => '+1 555 0000',
        'source' => 'Direct Test',
        'assignedTo' => 'System Test'
    ]
];

// Initialize Slack model
$slackModel = new SlackModel();

echo "Attempting to send test message to Slack...\n\n";

// Send test notification
$result = $slackModel->sendNotification('newlead', $testData);

if ($result['success']) {
    echo "✓ SUCCESS: Message sent to Slack!\n";
    echo "Check your #sales-leads channel for the test message.\n";
} else {
    echo "✗ FAILED: Could not send to Slack\n";
    echo "Error: " . ($result['error'] ?? 'Unknown error') . "\n\n";
    
    echo "Troubleshooting:\n";
    echo "1. Check if SLACK_WEBHOOK_NEWLEAD is correct in .env\n";
    echo "2. Verify the webhook URL is active in your Slack app\n";
    echo "3. Make sure the bot token has correct permissions\n";
}

echo "\nCredentials found:\n";
echo "- Bot Token: " . (isset($_ENV['SLACK_BOT_TOKEN']) && $_ENV['SLACK_BOT_TOKEN'] ? 'Yes' : 'No') . "\n";
echo "- Newlead Webhook: " . (isset($_ENV['SLACK_WEBHOOK_NEWLEAD']) && $_ENV['SLACK_WEBHOOK_NEWLEAD'] ? 'Yes' : 'No') . "\n";