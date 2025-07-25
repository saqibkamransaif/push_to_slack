<?php
/**
 * New Lead Client Success Event Configuration
 * Handles new lead notifications for client success department with payment information
 * 
 * Event Type: newlead-client-success
 * Channel: client-success-department
 * Webhook URL: SLACK_WEBHOOK_NEWLEAD_CLIENT_SUCCESS
 */

return [
    'name' => 'New Lead - Client Success',
    'description' => 'Triggered when a new lead with payment is added for client success department',
    'slack_channel' => 'client-success-department',
    'webhook_url' => $_ENV['SLACK_WEBHOOK_NEWLEAD_CLIENT_SUCCESS'] ?? '',
    'priority' => 'high',
    'notification_type' => 'immediate',
    
    /**
     * Expected fields in webhook data
     */
    'expected_fields' => [
        'event-type' => 'newlead-client-success',
        'First Name' => 'Contact first name',
        'Last Name' => 'Contact last name',
        'Email' => 'Contact email address',
        'Phone' => 'Contact phone number',
        'Source' => 'Lead source',
        'UTM Source' => 'UTM source parameter',
        'UTM Term' => 'UTM term parameter',
        'UTM Campaign' => 'UTM campaign parameter',
        'UTM Medium' => 'UTM medium parameter',
        'UTM Content' => 'UTM content parameter',
        'Payment Paid' => 'Payment amount paid',
        'Due Amount' => 'Payment amount due',
        'Total Amount' => 'Total payment amount',
        'Payment Date' => 'Payment creation date',
        'Transaction ID' => 'Payment transaction ID',
        'Invoice Number' => 'Invoice number',
        'Recorded By' => 'Payment recorded by'
    ],
    
    /**
     * Message template for new lead client success alerts
     */
    'message_template' => '🎉 *New Lead - Client Success Department* 🎉

📋 **Contact Information:**
• *Name:* {First Name} {Last Name}
• *Email:* {Email}
• *Phone:* {Phone}
• *Source:* {Source}

📊 **UTM Tracking:**
• *Source:* {UTM Source}
• *Term:* {UTM Term}
• *Campaign:* {UTM Campaign}
• *Medium:* {UTM Medium}
• *Content:* {UTM Content}

💰 **Payment Details:**
• *Amount Paid:* ${Payment Paid}
• *Due Amount:* ${Due Amount}
• *Total Amount:* ${Total Amount}
• *Payment Date:* {Payment Date}
• *Transaction ID:* {Transaction ID}
• *Invoice Number:* {Invoice Number}
• *Recorded By:* {Recorded By}

⏰ *Timestamp:* {timestamp}'
];