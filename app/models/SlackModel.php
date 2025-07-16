<?php
/**
 * SlackModel handles Slack API integration
 * Supports both Webhook URLs and Bot Token methods
 */
class SlackModel {
    
    private $webhookUrls;
    private $botToken;
    private $eventTypes;
    private $logger;
    
    /**
     * Constructor loads Slack configuration
     * @return void
     */
    public function __construct() {
        require_once BASE_PATH . '/app/models/Logger.php';
        $this->logger = Logger::getInstance();
        
        // Load event types configuration first
        $this->eventTypes = require BASE_PATH . '/config/event_types.php';
        
        // Load event-specific webhook URLs dynamically
        $this->webhookUrls = [];
        if (is_array($this->eventTypes)) {
            foreach ($this->eventTypes as $eventType => $config) {
                // Check if webhook URL is directly configured
                if (isset($config['webhook_url'])) {
                    $this->webhookUrls[$eventType] = $config['webhook_url'];
                } else {
                    // Fallback to environment variable
                    $envVar = $config['webhook_env_var'] ?? 'SLACK_WEBHOOK_' . strtoupper($eventType);
                    $this->webhookUrls[$eventType] = $_ENV[$envVar] ?? null;
                }
            }
        }
        
        // Bot token as alternative
        $this->botToken = $_ENV['SLACK_BOT_TOKEN'] ?? null;
        
        $this->logger->debug('SlackModel initialized', [
            'has_bot_token' => !empty($this->botToken),
            'webhook_urls_configured' => array_map(function($url) {
                return !empty($url);
            }, $this->webhookUrls)
        ]);
    }
    
    /**
     * Sends a message to Slack using the appropriate method
     * @param string $eventType The event type
     * @param array $data The webhook data
     * @param string $channel Override default channel (optional)
     * @return array Result with success status
     */
    public function sendNotification($eventType, $data, $channel = null) {
        // Check if event type is configured
        if (!isset($this->eventTypes[$eventType])) {
            $this->logger->error('Unknown event type for Slack', ['event_type' => $eventType]);
            return [
                'success' => false,
                'error' => 'Unknown event type: ' . $eventType,
                'debug' => [
                    'available_types' => array_keys($this->eventTypes)
                ]
            ];
        }
        
        $eventConfig = $this->eventTypes[$eventType];
        $targetChannel = $channel ?: $eventConfig['slack_channel'];
        
        $this->logger->debug('Preparing Slack notification', [
            'event_type' => $eventType,
            'channel' => $targetChannel,
            'has_custom_data' => isset($data['customData'])
        ]);
        
        // Format the message
        $message = $this->formatMessage($eventConfig['message_template'], $data);
        
        // Send via appropriate method - prioritize webhook URLs for specific channels
        if (isset($this->webhookUrls[$eventType]) && $this->webhookUrls[$eventType]) {
            $this->logger->debug('Sending via Webhook URL for event: ' . $eventType);
            return $this->sendViaWebhook($message, $this->webhookUrls[$eventType], $eventType);
        } elseif ($this->botToken) {
            $this->logger->debug('Sending via Bot Token to channel: ' . $targetChannel);
            return $this->sendViaAPI($message, $targetChannel);
        }
        
        $this->logger->error('No Slack credentials configured', [
            'event_type' => $eventType,
            'has_bot_token' => !empty($this->botToken),
            'has_webhook' => !empty($this->webhookUrls[$eventType])
        ]);
        
        return [
            'success' => false,
            'error' => 'No Slack credentials configured for event type: ' . $eventType,
            'debug' => [
                'bot_token_configured' => !empty($this->botToken),
                'webhook_configured' => !empty($this->webhookUrls[$eventType])
            ]
        ];
    }
    
    /**
     * Formats a message template with actual data
     * @param string $template Message template with {placeholders}
     * @param array $data Data to replace placeholders
     * @return string Formatted message
     */
    private function formatMessage($template, $data) {
        // Only use customData for formatting
        $customData = $data['customData'] ?? [];
        
        // Add timestamp if not present in customData
        if (!isset($customData['timestamp'])) {
            $customData['timestamp'] = date('Y-m-d H:i:s');
        }
        
        // Add dynamic status emoji and heading using event-specific logic
        if (isset($customData['status']) && isset($customData['event-type'])) {
            $eventType = $customData['event-type'];
            $statusInfo = $this->getStatusEmoji($customData['status'], $eventType);
            $customData['statusEmoji'] = $statusInfo['emoji'];
            $customData['statusHeading'] = $statusInfo['heading'];
        }
        
        // Replace placeholders with values from customData only
        foreach ($customData as $key => $value) {
            if (!is_array($value)) {
                $template = str_replace('{' . $key . '}', $value, $template);
            }
        }
        
        // Remove any remaining placeholders
        $template = preg_replace('/\{[^}]+\}/', 'N/A', $template);
        
        return $template;
    }
    
    /**
     * Gets emoji and heading based on status using event-specific configuration
     * @param string $status Status value
     * @param string $eventType Event type
     * @return array Emoji and heading
     */
    private function getStatusEmoji($status, $eventType) {
        // Check if event has a custom status handler
        if (isset($this->eventTypes[$eventType]['status_handler'])) {
            $handler = $this->eventTypes[$eventType]['status_handler'];
            if (is_callable($handler)) {
                return $handler($status);
            }
        }
        
        // Check if event has valid_statuses configuration
        if (isset($this->eventTypes[$eventType]['valid_statuses'])) {
            $statusLower = strtolower(trim($status));
            $validStatuses = $this->eventTypes[$eventType]['valid_statuses'];
            
            if (isset($validStatuses[$statusLower])) {
                return [
                    'emoji' => $validStatuses[$statusLower]['emoji'],
                    'heading' => $validStatuses[$statusLower]['heading']
                ];
            }
        }
        
        // Default fallback
        return [
            'emoji' => '📋',
            'heading' => 'Status Update'
        ];
    }
    
    /**
     * Sends message via Slack Web API (using Bot Token)
     * @param string $message The message to send
     * @param string $channel Target channel
     * @return array Result
     */
    private function sendViaAPI($message, $channel) {
        $url = 'https://slack.com/api/chat.postMessage';
        
        // Ensure channel has # prefix
        if (!str_starts_with($channel, '#') && !str_starts_with($channel, '@')) {
            $channel = '#' . $channel;
        }
        
        $payload = [
            'channel' => $channel,
            'text' => $message,
            'mrkdwn' => true
        ];
        
        $this->logger->debug('Sending via Slack API', [
            'channel' => $channel,
            'token_length' => strlen($this->botToken)
        ]);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->botToken
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            $this->logger->error('cURL error when calling Slack API', ['error' => $curlError]);
            return [
                'success' => false,
                'error' => 'Network error: ' . $curlError
            ];
        }
        
        $result = json_decode($response, true);
        
        if ($httpCode === 200 && isset($result['ok']) && $result['ok']) {
            $this->logger->debug('Slack API call successful');
            return ['success' => true];
        }
        
        $this->logger->error('Slack API call failed', [
            'http_code' => $httpCode,
            'error' => $result['error'] ?? 'Unknown',
            'response' => $result
        ]);
        
        return [
            'success' => false,
            'error' => $result['error'] ?? 'Failed to send to Slack',
            'debug' => [
                'http_code' => $httpCode,
                'slack_error' => $result['error'] ?? null,
                'slack_response' => $result
            ]
        ];
    }
    
    /**
     * Sends message via Slack Incoming Webhook
     * @param string $message The message to send  
     * @param string $webhookUrl The specific webhook URL to use
     * @param string $eventType Event type for logging
     * @return array Result
     */
    private function sendViaWebhook($message, $webhookUrl, $eventType = null) {
        $payload = [
            'text' => $message,
            'mrkdwn' => true
        ];
        
        $this->logger->debug('Sending to Slack webhook', [
            'url_length' => strlen($webhookUrl),
            'message_length' => strlen($message)
        ]);
        
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
        
        if ($curlError) {
            $this->logger->error('cURL error when sending to Slack', [
                'error' => $curlError,
                'event_type' => $eventType
            ]);
            return [
                'success' => false,
                'error' => 'Network error: ' . $curlError,
                'debug' => ['curl_error' => $curlError]
            ];
        }
        
        if ($httpCode === 200 && $response === 'ok') {
            $this->logger->debug('Slack webhook successful');
            return ['success' => true];
        }
        
        $this->logger->error('Slack webhook failed', [
            'http_code' => $httpCode,
            'response' => $response,
            'event_type' => $eventType
        ]);
        
        return [
            'success' => false,
            'error' => 'Webhook returned status ' . $httpCode . ': ' . $response,
            'debug' => [
                'http_code' => $httpCode,
                'response' => $response
            ]
        ];
    }
}