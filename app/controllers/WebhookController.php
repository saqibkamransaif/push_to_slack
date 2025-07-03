<?php
/**
 * WebhookController handles incoming webhook requests
 * Validates and stores webhook data
 */
class WebhookController {
    
    private $webhookModel;
    private $slackModel;
    private $logger;
    
    /**
     * Constructor initializes the webhook and slack models
     * @return void
     */
    public function __construct() {
        require_once BASE_PATH . '/app/models/WebhookModel.php';
        require_once BASE_PATH . '/app/models/SlackModel.php';
        require_once BASE_PATH . '/app/models/Logger.php';
        
        $this->webhookModel = new WebhookModel();
        $this->slackModel = new SlackModel();
        $this->logger = Logger::getInstance();
    }
    
    /**
     * Handles incoming webhook data
     * @param array $data Decoded JSON data
     * @param string $rawData Raw JSON string
     * @return array Response with status and data
     */
    public function handleWebhook($data, $rawData) {
        try {
            // Log incoming webhook
            $this->logger->info('Webhook received', [
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'size' => strlen($rawData)
            ]);
            
            // Check for event-type in multiple locations
            $eventType = null;
            
            // Check root level
            if (isset($data['event-type'])) {
                $eventType = $data['event-type'];
            }
            // Check in customData
            elseif (isset($data['customData']['event-type'])) {
                $eventType = $data['customData']['event-type'];
            }
            // Check in customData with underscore
            elseif (isset($data['customData']['event_type'])) {
                $eventType = $data['customData']['event_type'];
            }
            
            // Validate event type was found
            if (!$eventType) {
                $this->logger->error('Missing event-type in webhook', [
                    'received_keys' => array_keys($data),
                    'customData_keys' => isset($data['customData']) ? array_keys($data['customData']) : []
                ]);
                
                return [
                    'status' => 400,
                    'data' => [
                        'error' => 'Missing required field: event-type',
                        'hint' => 'Expected at root level or in customData.event-type',
                        'received_structure' => array_keys($data)
                    ]
                ];
            }
            
            // Add event type to data for storage
            $data['_extracted_event_type'] = $eventType;
            
            $this->logger->webhook($eventType, ['webhook_id' => 'pending'], 'received');
            
            // Store the webhook data
            $result = $this->webhookModel->storeWebhook($data, $rawData);
            
            if ($result['success']) {
                $this->logger->webhook($eventType, $result, 'stored');
                
                // Send notification to Slack
                $slackResult = $this->slackModel->sendNotification($eventType, $data);
                
                // Log Slack notification result
                if (!$slackResult['success']) {
                    $this->logger->slack($eventType, false, $slackResult['error'] ?? 'Unknown error');
                    $this->logger->error('Slack notification failed', [
                        'event_type' => $eventType,
                        'webhook_id' => $result['webhook_id'],
                        'error' => $slackResult['error'] ?? 'Unknown error',
                        'debug_info' => $slackResult['debug'] ?? null
                    ]);
                } else {
                    $this->logger->slack($eventType, true);
                    
                    // Delete webhook file if configured to do so
                    $deleteOnSuccess = filter_var($_ENV['DELETE_WEBHOOK_ON_SUCCESS'] ?? false, FILTER_VALIDATE_BOOLEAN);
                    if ($deleteOnSuccess) {
                        $deleted = $this->webhookModel->deleteWebhook(
                            $result['webhook_id'], 
                            $result['filename'] ?? null
                        );
                        
                        if ($deleted) {
                            $this->logger->info('Webhook file deleted after successful Slack send', [
                                'webhook_id' => $result['webhook_id'],
                                'event_type' => $eventType
                            ]);
                        } else {
                            $this->logger->error('Failed to delete webhook file', [
                                'webhook_id' => $result['webhook_id'],
                                'event_type' => $eventType
                            ]);
                        }
                    }
                }
                
                return [
                    'status' => 200,
                    'data' => [
                        'success' => true,
                        'message' => 'Webhook received and stored',
                        'webhook_id' => $result['webhook_id'],
                        'slack_notification' => $slackResult['success'] ? 'sent' : 'failed',
                        'slack_error' => !$slackResult['success'] ? ($slackResult['error'] ?? 'Check logs for details') : null
                    ]
                ];
            } else {
                $this->logger->error('Failed to store webhook', [
                    'event_type' => $eventType,
                    'error' => $result['error'] ?? 'Unknown storage error'
                ]);
                
                return [
                    'status' => 500,
                    'data' => ['error' => 'Failed to store webhook data']
                ];
            }
            
        } catch (Exception $e) {
            $this->logger->error('Exception in webhook handling', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'status' => 500,
                'data' => ['error' => 'Internal server error: ' . $e->getMessage()]
            ];
        }
    }
}