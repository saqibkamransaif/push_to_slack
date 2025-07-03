<?php
/**
 * Ebook Lead Event Configuration  
 * Handles ebook download lead notifications
 * 
 * Event Type: ebooklead
 * Channel: marketing-leads
 * Webhook URL: SLACK_WEBHOOK_EBOOKLEAD
 */

return [
    'name' => 'Ebook Download Lead',
    'description' => 'Triggered when a new lead comes in with ebook downloaded',
    'slack_channel' => 'marketing-leads',
    'webhook_env_var' => 'SLACK_WEBHOOK_EBOOKLEAD',
    'priority' => 'medium',
    'notification_type' => 'batch', // Can batch these if many come at once
    
    /**
     * Expected fields in webhook data
     */
    'expected_fields' => [
        'leadName' => 'Lead name',
        'email' => 'Lead email address',
        'ebookTitle' => 'Downloaded ebook title',
        'downloadDate' => 'Download date/time',
        'source' => 'Traffic source',
        'marketingConsent' => 'Marketing consent status'
    ],
    
    /**
     * Message template for ebook leads
     */
    'message_template' => ':books: *New Ebook Download*
Lead: {leadName}
Email: {email}
Ebook: {ebookTitle}
Downloaded: {downloadDate}
Source: {source}
Marketing Consent: {marketingConsent}'
];