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
        'first_name' => 'Contact first name',
        'last_name' => 'Contact last name',
        'email' => 'Contact email address',
        'phone' => 'Contact phone number',
        'source' => 'Lead source',
        'utmsource' => 'UTM source parameter',
        'utmterm' => 'UTM term parameter',
        'utmcampaign' => 'UTM campaign parameter',
        'utmmedium' => 'UTM medium parameter',
        'utmcontent' => 'UTM content parameter',
        'amount_paid' => 'Payment amount paid',
        'amount_due' => 'Payment amount due',
        'total_amount' => 'Total payment amount',
        'payment_date' => 'Payment creation date',
        'transaction_id' => 'Payment transaction ID',
        'invoice_number' => 'Invoice number',
        'recorded_by' => 'Payment recorded by'
    ],
    
    /**
     * Message template for new lead client success alerts
     */
    'message_template' => '🎉 *New Lead - Client Success Department* 🎉

📋 **Contact Information:**
• *Name:* {first_name} {last_name}
• *Email:* {email}
• *Phone:* {phone}
• *Source:* {source}

📊 **UTM Tracking:**
• *Source:* {utmsource}
• *Term:* {utmterm}
• *Campaign:* {utmcampaign}
• *Medium:* {utmmedium}
• *Content:* {utmcontent}

💰 **Payment Details:**
• *Amount Paid:* ${amount_paid}
• *Due Amount:* ${amount_due}
• *Total Amount:* ${total_amount}
• *Payment Date:* {payment_date}
• *Transaction ID:* {transaction_id}
• *Invoice Number:* {invoice_number}
• *Recorded By:* {recorded_by}

⏰ *Timestamp:* {timestamp}'
];