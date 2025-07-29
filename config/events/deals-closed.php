<?php
/**
 * Deals Closed Event Configuration
 * Handles deal closure notifications
 * 
 * Event Type: deals-closed
 * Channel: deals-closed
 * Webhook URL: Loaded from SLACK_WEBHOOK_DEALSCLOSED environment variable
 */

return [
    'name' => 'Deals Closed',
    'description' => 'Triggered when a deal is closed successfully',
    'slack_channel' => 'deals-closed',
    'webhook_env_var' => 'SLACK_WEBHOOK_DEALSCLOSED',
    'priority' => 'high',
    'notification_type' => 'immediate',
    
    /**
     * Expected fields in webhook data
     */
    'expected_fields' => [
        'Client Name' => 'Client/Contact full name',
        'AE' => 'Account Executive name',
        'document.totalAmount' => 'Total Amount',
        'Contract status' => 'Contract status (Signed/Accepted)',
        'Contract Type' => 'Type of contract'
    ],
    
    /**
     * Message template for deals closed
     */
    'message_template' => '🎉 *Deal Closed!*

*Client:* {Client Name}
*Account Executive:* {AE}
*Total Amount:* ${document.totalAmount}
*Contract Status:* {Contract status}
*Contract Type:* {Contract Type}'
];