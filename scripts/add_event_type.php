<?php
/**
 * Helper script to add new event types using the new modular structure
 * Usage: php scripts/add_event_type.php
 */

echo "=== Add New Event Type ===\n\n";

// Get event type key
echo "Enter event type key (e.g., 'orderplaced'): ";
$eventKey = trim(fgets(STDIN));

// Get event name
echo "Enter event name (e.g., 'Order Placed'): ";
$eventName = trim(fgets(STDIN));

// Get description
echo "Enter description: ";
$description = trim(fgets(STDIN));

// Get default channel
echo "Enter default Slack channel (without #): ";
$channel = trim(fgets(STDIN));

// Get webhook URL
echo "Enter Slack webhook URL: ";
$webhookUrl = trim(fgets(STDIN));

// Generate the event configuration file
$configContent = "<?php
/**
 * $eventName Event Configuration
 * $description
 * 
 * Event Type: $eventKey
 * Channel: $channel
 * Webhook URL: SLACK_WEBHOOK_" . strtoupper($eventKey) . "
 */

return [
    'name' => '$eventName',
    'description' => '$description',
    'slack_channel' => '$channel',
    'webhook_env_var' => 'SLACK_WEBHOOK_" . strtoupper($eventKey) . "',
    'priority' => 'medium',
    'notification_type' => 'immediate',
    
    /**
     * Expected fields in webhook data
     */
    'expected_fields' => [
        // Add your expected fields here
        // 'field1' => 'Description of field1',
        // 'field2' => 'Description of field2'
    ],
    
    /**
     * Message template
     */
    'message_template' => ':bell: *$eventName*
Details: {field1}
Time: {timestamp}'
];";

// Create the event file
$eventFile = __DIR__ . '/../config/events/' . $eventKey . '.php';
file_put_contents($eventFile, $configContent);

echo "\n=== Event Type Created Successfully! ===\n";
echo "✓ Created file: config/events/$eventKey.php\n";

// Update .env file
$envFile = __DIR__ . '/../.env';
$envLine = "\n# Webhook for $eventName ($channel channel)\nSLACK_WEBHOOK_" . strtoupper($eventKey) . "=$webhookUrl\n";

if (file_exists($envFile)) {
    file_put_contents($envFile, $envLine, FILE_APPEND);
    echo "✓ Added webhook URL to .env file\n";
}

// Update sample-env file
$sampleEnvFile = __DIR__ . '/../sample-env';
$sampleEnvLine = "\n# Webhook for $eventName ($channel channel)\nSLACK_WEBHOOK_" . strtoupper($eventKey) . "=https://hooks.slack.com/services/YOUR/WEBHOOK/URL\n";

if (file_exists($sampleEnvFile)) {
    file_put_contents($sampleEnvFile, $sampleEnvLine, FILE_APPEND);
    echo "✓ Added example to sample-env file\n";
}

echo "\n=== Next Steps ===\n";
echo "1. ✓ Event configuration created\n";
echo "2. ✓ Environment variables added\n";
echo "3. Edit config/events/$eventKey.php to customize:\n";
echo "   - expected_fields\n";
echo "   - message_template\n";
echo "   - Add status handlers if needed\n";
echo "4. Test with: php tests/test_webhook.php\n";

echo "\nDone! Your new event type '$eventKey' is ready to use!\n";