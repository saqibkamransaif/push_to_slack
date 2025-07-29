<?php
/**
 * New Message Setter Event Configuration
 * Triggered when a contact sends a new message to setter
 * 
 * Event Type: new_message_setter
 * Channel: speed-to-lead
 * Webhook URL: SLACK_WEBHOOK_NEW_MESSAGE_SETTER
 */

return [
    'name' => 'New Message Setter',
    'description' => 'Triggered when a contact sends a new message to setter',
    'slack_channel' => 'speed-to-lead',
    'webhook_env_var' => 'SLACK_WEBHOOK_SPEED_TO_LEAD_MESSAGE',
    'priority' => 'high',
    'notification_type' => 'immediate',
    
    /**
     * Expected fields in webhook data
     */
    'expected_fields' => [
        'name' => 'Contact name',
        'email' => 'Contact email address',
        'message_subject' => 'Message subject line',
        'message_body' => 'Message content body'
    ],
    
    /**
     * Message template for new message setter notifications
     */
    'message_template' => '{name} ({email}):
{message_subject}
{message_body}'
];