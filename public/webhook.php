<?php
/**
 * Webhook endpoint to receive JSON data
 * Stores incoming webhook data in the webhooks directory
 * @return void
 */

// Load configuration
require_once __DIR__ . '/../config/bootstrap.php';

// Set JSON header
header('Content-Type: application/json');

// Get the raw POST data
$rawData = file_get_contents('php://input');

// Verify we received data
if (empty($rawData)) {
    http_response_code(400);
    echo json_encode(['error' => 'No data received']);
    exit;
}

// Decode JSON data
$data = json_decode($rawData, true);

// Verify JSON is valid
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON data']);
    exit;
}

// Initialize webhook controller
require_once __DIR__ . '/../app/controllers/WebhookController.php';
$controller = new WebhookController();

// Process the webhook
$result = $controller->handleWebhook($data, $rawData);

// Return response
http_response_code($result['status']);
echo json_encode($result['data']);