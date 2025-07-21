<?php
/**
 * No Show Rebook Event Configuration
 * Handles no-show appointment alerts with rebooking information
 * 
 * Event Type: no_show_rebook
 * Channel: no-shows-rebook
 * Webhook URL: Loaded from SLACK_WEBHOOK_NOSHOWREBOOK environment variable
 */

return [
    'name' => 'No Show Rebook',
    'description' => 'Triggered when a contact has a no-show appointment that needs rebooking',
    'slack_channel' => 'no-shows-rebook',
    'webhook_url' => $_ENV['SLACK_WEBHOOK_NOSHOWREBOOK'] ?? '',
    'priority' => 'high',
    'notification_type' => 'immediate',
    
    /**
     * Expected fields in webhook data
     */
    'expected_fields' => [
        'name' => 'Contact full name',
        'email' => 'Contact email address',
        'phone' => 'Contact phone number',
        'appointment_time' => 'Original appointment date/time',
        'reschedule_link' => 'Link for rescheduling appointment'
    ],
    
    /**
     * Message template for no-show rebook alerts
     * Format: No-Show Appointment Alert with contact details and reschedule link
     */
    'message_template' => '🚨 *No-Show Appointment Alert*

📋 **Contact Information:**
• *Name:* {name}
• *Email:* {email}
• *Phone:* {phone}

⏰ **Appointment Details:**
• *Original Time:* {appointment_time}
• *Status:* No-Show

🔗 **Action Required:**
• *Reschedule Link:* {reschedule_link}

_Please follow up with the client to reschedule their appointment._'
];