<?php
/**
 * New Lead Client Success Event Configuration
 * Handles new lead notifications for client success department
 * 
 * Event Type: newlead-client-success
 * Channel: client-success-department
 * Webhook URL: SLACK_WEBHOOK_NEWLEAD_CLIENT_SUCCESS
 */

return [
    'name' => 'New Lead - Client Success',
    'description' => 'Triggered when a new lead is assigned for client success department',
    'slack_channel' => 'client-success-department',
    'webhook_env_var' => 'SLACK_WEBHOOK_NEWLEAD_CLIENT_SUCCESS',
    'priority' => 'high',
    'notification_type' => 'immediate',
    
    /**
     * Expected fields in webhook data
     */
    'expected_fields' => [
        'event-type' => 'newlead-client-success',
        'first_name' => '{{contact.first_name}}',
        'last_name' => '{{contact.last_name}}',
        'email' => '{{contact.email}}',
        'phone' => '{{contact.phone}}',
        'assigned_user' => '{{user.name}}'
    ],
    
    /**
     * Message template for new lead client success alerts
     */
    'message_template' => '🎉 *New Lead - Client Success Department* 🎉

👤 **Lead Information:**
• *Name:* {first_name} {last_name}
• *Email:* {email}
• *Phone:* {phone}


• *Assigned to:* {assigned_user}

⏰ *Timestamp:* {timestamp}'
];