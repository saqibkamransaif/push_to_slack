<?php
/**
 * New Lead Event Configuration
 * Handles new lead notifications (general leads, not from ebook downloads)
 * 
 * Event Type: newlead
 * Channel: speed-to-lead
 * Webhook URL: SLACK_WEBHOOK_NEWLEAD
 */

return [
    'name' => 'New Lead',
    'description' => 'Triggered when a new lead is added to the system (general leads, not from ebook downloads)',
    'slack_channel' => 'speed-to-lead',
    'webhook_env_var' => 'SLACK_WEBHOOK_NEWLEAD',
    'priority' => 'high',
    'notification_type' => 'immediate',
    
    /**
     * Expected fields in webhook data
     */
    'expected_fields' => [
        'name' => 'Lead full name',
        'email' => 'Lead email address',
        'phone' => 'Lead phone number',
        'source' => 'Lead source (e.g., website, campaign, contact form)',
        'assignedTo' => 'Staff member assigned to the lead',
        'utm-source' => 'UTM source parameter',
        'utm-medium' => 'UTM medium parameter',
        'utm-campaign' => 'UTM campaign parameter',
        'utm-content' => 'UTM content parameter',
        'utm-term' => 'UTM term parameter'
    ],
    
    /**
     * Message template for new leads
     */
    'message_template' => ':sparkles: *New Lead Alert!*
Name: {name}
Email: {email}
Phone: {phone}
Source: {source}
Assigned to: {assignedTo}

*UTM Parameters:*
Source: {utm-source}
Medium: {utm-medium}
Campaign: {utm-campaign}
Content: {utm-content}
Term: {utm-term}

Time: {timestamp}',

    /**
     * Lead priority mapping (optional)
     */
    'priority_emojis' => [
        'hot' => '🔥',
        'warm' => '🌡️', 
        'cold' => '❄️'
    ]
];