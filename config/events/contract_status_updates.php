<?php
/**
 * Contract Status Updates Event Configuration
 * Handles all contract status related notifications
 * 
 * Event Type: contract_status_updates
 * Channel: contract_status_updates
 * Webhook URL: SLACK_WEBHOOK_CONTRACT_STATUS_UPDATES
 */

return [
    'name' => 'Contract Status Update',
    'description' => 'Triggered when a contract status changes',
    'slack_channel' => 'contract_status_updates',
    'webhook_env_var' => 'SLACK_WEBHOOK_CONTRACT_STATUS_UPDATES',
    'priority' => 'high',
    'notification_type' => 'immediate',
    
    /**
     * Contract Status Types
     * Only these 4 statuses are valid for contracts
     */
    'valid_statuses' => [
        'sent' => [
            'emoji' => '📤',
            'heading' => 'Contract Sent',
            'description' => 'Contract has been sent to client'
        ],
        'viewed' => [
            'emoji' => '👀', 
            'heading' => 'Contract Viewed',
            'description' => 'Client has viewed the contract'
        ],
        'signed' => [
            'emoji' => '✍️',
            'heading' => 'Contract Signed', 
            'description' => 'Contract has been signed by client'
        ],
        'completed' => [
            'emoji' => '✅',
            'heading' => 'Contract Completed',
            'description' => 'Contract process is fully completed'
        ]
    ],
    
    /**
     * Expected fields in webhook data
     */
    'expected_fields' => [
        'documentTitle' => 'Document/contract title',
        'clientName' => 'Client name',
        'clientEmail' => 'Client email address', 
        'status' => 'Contract status (sent, viewed, signed, completed)',
        'time' => 'Time of the update'
    ],
    
    /**
     * Message template with dynamic placeholders
     * {statusEmoji} and {statusHeading} are automatically generated
     */
    'message_template' => '{statusEmoji} *{statusHeading}*
Document: {documentTitle}
Client: {clientName}
Email: {clientEmail}
Status: {status}
Time: {time}',

    /**
     * Status emoji mapping function
     * Returns emoji and heading based on status
     */
    'status_handler' => function($status) {
        $statusLower = strtolower(trim($status));
        
        $validStatuses = [
            'sent' => ['emoji' => '📤', 'heading' => 'Contract Sent'],
            'viewed' => ['emoji' => '👀', 'heading' => 'Contract Viewed'],
            'signed' => ['emoji' => '✍️', 'heading' => 'Contract Signed'],
            'completed' => ['emoji' => '✅', 'heading' => 'Contract Completed']
        ];
        
        return $validStatuses[$statusLower] ?? [
            'emoji' => '📋', 
            'heading' => 'Contract Status Update'
        ];
    }
];