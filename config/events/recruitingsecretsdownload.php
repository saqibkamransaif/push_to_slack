<?php
/**
 * Recruiting Secrets Download Event Configuration
 * Triggered when someone downloads recruiting secrets content
 * 
 * Event Type: recruitingsecretsdownload
 * Channel: lead-flow
 * Webhook URL: SLACK_WEBHOOK_RECRUITINGSECRETSDOWNLOAD
 */

return [
    'name' => 'Recruiting Secrets Download',
    'description' => 'Triggered when someone downloads recruiting secrets content',
    'slack_channel' => 'lead-flow',
    'webhook_env_var' => 'SLACK_WEBHOOK_RECRUITINGSECRETSDOWNLOAD',
    'priority' => 'high',
    'notification_type' => 'immediate',
    
    /**
     * Expected fields in webhook data
     */
    'expected_fields' => [
        'name' => 'Lead name',
        'email' => 'Lead email address',
        'phone' => 'Lead phone number',
        'source' => 'Traffic source',
        'assignedTo' => 'Staff member assigned to the lead'
    ],
    
    /**
     * Message template for recruiting secrets downloads
     */
    'message_template' => ':scroll: *Recruiting Secrets Downloaded*
Name: {name}
Email: {email}
Phone: {phone}
Source: {source}
Assigned to: {assignedTo}
Time: {timestamp}'
];