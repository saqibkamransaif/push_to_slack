<?php
/**
 * WebhookModel handles data storage for webhooks
 * Stores webhook data in JSON files within webhooks directory
 */
class WebhookModel {
    
    private $webhooksDir;
    
    /**
     * Constructor sets up webhooks directory path
     * @return void
     */
    public function __construct() {
        $this->webhooksDir = BASE_PATH . '/webhooks';
        
        // Ensure webhooks directory exists
        if (!is_dir($this->webhooksDir)) {
            mkdir($this->webhooksDir, 0755, true);
        }
    }
    
    /**
     * Stores webhook data in a JSON file
     * @param array $data Decoded webhook data
     * @param string $rawData Raw JSON string
     * @return array Result with success status and webhook_id
     */
    public function storeWebhook($data, $rawData) {
        try {
            // Generate unique webhook ID
            $webhookId = $this->generateWebhookId();
            
            // Determine event type from multiple possible locations
            $eventType = $data['_extracted_event_type'] ?? 
                         $data['event-type'] ?? 
                         $data['customData']['event-type'] ?? 
                         $data['customData']['event_type'] ?? 
                         'unknown';
            
            // Create webhook record
            $webhookRecord = [
                'webhook_id' => $webhookId,
                'received_at' => date('Y-m-d H:i:s'),
                'event_type' => $eventType,
                'raw_data' => $rawData,
                'parsed_data' => $data
            ];
            
            // Create filename with timestamp
            $filename = sprintf(
                '%s_%s_%s.json',
                date('Y-m-d_H-i-s'),
                $eventType,
                $webhookId
            );
            
            $filepath = $this->webhooksDir . '/' . $filename;
            
            // Store webhook data
            $result = file_put_contents(
                $filepath,
                json_encode($webhookRecord, JSON_PRETTY_PRINT)
            );
            
            if ($result !== false) {
                return [
                    'success' => true,
                    'webhook_id' => $webhookId,
                    'filename' => $filename
                ];
            }
            
            return ['success' => false];
            
        } catch (Exception $e) {
            error_log('Failed to store webhook: ' . $e->getMessage());
            return ['success' => false];
        }
    }
    
    /**
     * Deletes a webhook file
     * @param string $webhookId Webhook ID
     * @param string $filename Optional filename if known
     * @return bool Success status
     */
    public function deleteWebhook($webhookId, $filename = null) {
        try {
            if ($filename && file_exists($this->webhooksDir . '/' . $filename)) {
                // Delete specific file if filename is provided
                return unlink($this->webhooksDir . '/' . $filename);
            }
            
            // Search for file by webhook ID
            $pattern = $this->webhooksDir . '/*' . $webhookId . '.json';
            $files = glob($pattern);
            
            if (!empty($files)) {
                foreach ($files as $file) {
                    if (unlink($file)) {
                        return true;
                    }
                }
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log('Failed to delete webhook file: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generates a unique webhook ID
     * @return string Unique webhook identifier
     */
    private function generateWebhookId() {
        return uniqid('webhook_', true);
    }
}