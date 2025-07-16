<?php
/**
 * New Message Event Configuration
 * Handles new message notifications to sales coordinator
 * 
 * Event Type: new_message
 * Channel: sales-coordinator
 * Webhook URL: Loaded from SLACK_WEBHOOK_NEWMESSAGE environment variable
 */

return [
    'name' => 'New Message',
    'description' => 'Triggered when a new message is received from a contact',
    'slack_channel' => 'sales-coordinator',
    'webhook_url' => $_ENV['SLACK_WEBHOOK_NEWMESSAGE'] ?? '',
    'priority' => 'high',
    'notification_type' => 'immediate',
    
    /**
     * Expected fields in webhook data
     */
    'expected_fields' => [
        'name' => 'Contact full name',
        'email' => 'Contact email address',
        'message_subject' => 'Message subject line',
        'message_body' => 'Message body content'
    ],
    
    /**
     * Message template for new messages
     * Format: {{contact.name}} ({{contact.email}}) sent a new message: {{message.subject}} - {{message.body}}
     */
    'message_template' => '{name} ({email}) sent a new message: {message_subject} - {message_body}'
];