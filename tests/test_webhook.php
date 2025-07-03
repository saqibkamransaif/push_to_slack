<?php
/**
 * Test script to send sample webhook data
 * Can be used for local testing or to test deployed webhook
 * @return void
 */

// Configuration
$webhookUrl = 'http://localhost/push_to_slack/public/webhook.php'; // Change this to your URL
// $webhookUrl = 'https://api.saqibkamran.com/webhook.php'; // Use this when SSL is fixed

// Sample webhook data - All data in customData
$testData = [
    'customData' => [
        'event-type' => 'newlead',
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '+1 555 0123',
        'source' => 'Website Contact Form',
        'assignedTo' => 'Sales Team'
    ]
];

// Test data for new appointment
/*
$testData = [
    'customData' => [
        'event-type' => 'newappointment',
        'customerName' => 'Jane Smith',
        'appointmentDate' => '2025-07-05',
        'appointmentTime' => '2:00 PM',
        'service' => 'Consultation',
        'duration' => '1 hour',
        'assignedTo' => 'Dr. Johnson'
    ]
];
*/

// Test data for ebook lead
/*
$testData = [
    'customData' => [
        'event-type' => 'ebooklead',
        'leadName' => 'Bob Wilson',
        'email' => 'bob@example.com',
        'ebookTitle' => 'Ultimate Guide to Digital Marketing',
        'downloadDate' => date('Y-m-d H:i:s'),
        'source' => 'Facebook Ad',
        'marketingConsent' => 'Yes'
    ]
];
*/

// Send webhook
$ch = curl_init($webhookUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For testing only
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // For testing only

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Display results
echo "Webhook URL: $webhookUrl\n";
echo "HTTP Status: $httpCode\n";
if ($error) {
    echo "Error: $error\n";
}
echo "Response: $response\n\n";

// Check if webhook was stored
if ($httpCode == 200) {
    $responseData = json_decode($response, true);
    if (isset($responseData['webhook_id'])) {
        echo "Success! Webhook ID: " . $responseData['webhook_id'] . "\n";
    }
}