<?php
/**
 * Test script for newappointment_noconfirmation event
 */

require_once __DIR__ . '/../config/bootstrap.php';

echo "=== New Appointment No Confirmation Test ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";
echo "=============================================\n\n";

// Test configuration
$webhookUrl = 'https://api.saqibkamran.com/webhook.php';

// Sample new appointment no confirmation data matching your format
$testData = [
    'customData' => [
        'event-type' => 'newappointment_noconfirmation',
        'name' => 'Michael Chen',
        'phone' => '+1 555 0198',
        'email' => 'michael@example.com',
        'appointment_time' => '2024-01-22 09:30:00',
        'assignedTo' => 'Sales Coordinator',
        'appointmentWith' => 'Dr. Smith',
        'source' => 'Online Booking',
        'reschedule_link' => 'https://calendly.com/reschedule/def456',
        'cancellation_link' => 'https://calendly.com/cancel/def456',
        'calendar_name' => 'Medical Consultations'
    ]
];

echo "Testing New Appointment No Confirmation Webhook\n";
echo "===============================================\n";
echo "Webhook URL: $webhookUrl\n";
echo "Event Type: newappointment_noconfirmation\n";
echo "Customer: " . $testData['customData']['name'] . "\n";
echo "Email: " . $testData['customData']['email'] . "\n";
echo "Phone: " . $testData['customData']['phone'] . "\n";
echo "Appointment Time: " . $testData['customData']['appointment_time'] . "\n";
echo "Appointment With: " . $testData['customData']['appointmentWith'] . "\n";
echo "Calendar: " . $testData['customData']['calendar_name'] . "\n";
echo "Channel: sales-coordinator\n";
echo "Status: Needs SMS confirmation\n";
echo "Includes: Reschedule & Cancel links\n\n";

// Check if webhook URL is configured
echo "Checking configuration...\n";
if (isset($_ENV['SLACK_WEBHOOK_NEWAPPOINTMENT_NOCONFIRMATION']) && !empty($_ENV['SLACK_WEBHOOK_NEWAPPOINTMENT_NOCONFIRMATION'])) {
    echo "✓ New appointment no confirmation webhook URL configured\n";
    echo "  URL: " . substr($_ENV['SLACK_WEBHOOK_NEWAPPOINTMENT_NOCONFIRMATION'], 0, 50) . "...\n";
} else {
    echo "✗ New appointment no confirmation webhook URL not configured\n";
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
                echo "✓ Slack notification sent to #sales-coordinator channel\n";
                echo "Check your Slack channel for the unconfirmed appointment message.\n";
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
echo "⚠️ *Appointment Needs Confirmation*\n";
echo "\n";
echo "Michael Chen has booked an appointment with Dr. Smith at 2024-01-22 09:30:00 on Medical Consultations but they haven't responded to the confirmation SMS yet. Please contact them to confirm the appointment.\n";
echo "\n";
echo "*Quick Links:*\n";
echo "Reschedule Link\n";
echo "Cancellation Link\n";
echo "\n";
echo "_Received: " . date('Y-m-d H:i:s') . "_\n";

echo "\nTest completed!\n";