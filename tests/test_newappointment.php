<?php
/**
 * Test script for newappointment event
 */

require_once __DIR__ . '/../config/bootstrap.php';

echo "=== New Appointment Test ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";
echo "============================\n\n";

// Test configuration
$webhookUrl = 'https://api.saqibkamran.com/webhook.php';

// Sample new appointment data matching your format
$testData = [
    'customData' => [
        'event-type' => 'newappointment',
        'name' => 'Sarah Johnson',
        'phone' => '+1 555 0187',
        'email' => 'sarah@example.com',
        'appointment_time' => '2024-01-20 14:00:00',
        'assignedTo' => 'Sales Team',
        'appointmentWith' => 'John Smith',
        'source' => 'Website Booking Form',
        'reschedule_link' => 'https://calendly.com/reschedule/abc123',
        'cancellation_link' => 'https://calendly.com/cancel/abc123',
        'calendar_name' => 'Sales Calendar'
    ]
];

echo "Testing New Appointment Webhook\n";
echo "===============================\n";
echo "Webhook URL: $webhookUrl\n";
echo "Event Type: newappointment\n";
echo "Customer: " . $testData['customData']['name'] . "\n";
echo "Email: " . $testData['customData']['email'] . "\n";
echo "Phone: " . $testData['customData']['phone'] . "\n";
echo "Appointment Time: " . $testData['customData']['appointment_time'] . "\n";
echo "Appointment With: " . $testData['customData']['appointmentWith'] . "\n";
echo "Calendar: " . $testData['customData']['calendar_name'] . "\n";
echo "Channel: daily-appointments\n";
echo "Includes: Reschedule & Cancel links\n\n";

// Check if webhook URL is configured
echo "Checking configuration...\n";
if (isset($_ENV['SLACK_WEBHOOK_NEWAPPOINTMENT']) && !empty($_ENV['SLACK_WEBHOOK_NEWAPPOINTMENT'])) {
    echo "✓ New appointment webhook URL configured\n";
    echo "  URL: " . substr($_ENV['SLACK_WEBHOOK_NEWAPPOINTMENT'], 0, 50) . "...\n";
} else {
    echo "✗ New appointment webhook URL not configured\n";
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
                echo "✓ Slack notification sent to #daily-appointments channel\n";
                echo "Check your Slack channel for the new appointment message.\n";
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
echo "📅 *New Appointment Scheduled*\n";
echo "\n";
echo "⏰ *APPOINTMENT TIME*\n";
echo "`2024-01-20 14:00:00`\n";
echo "\n";
echo "👤 *Customer Details*\n";
echo "• Name: *Sarah Johnson*\n";
echo "• Email: sarah@example.com\n";
echo "• Phone: +1 555 0187\n";
echo "• Source: Website Booking Form\n";
echo "\n";
echo "🏢 *Appointment Details*\n";
echo "• Assigned to: *Sales Team*\n";
echo "• Appointment with: *John Smith*\n";
echo "• Calendar: Sales Calendar\n";
echo "\n";
echo "🔗 *Quick Actions*\n";
echo "Reschedule Appointment\n";
echo "Cancel Appointment\n";
echo "\n";
echo "_Received: " . date('Y-m-d H:i:s') . "_\n";

echo "\nTest completed!\n";