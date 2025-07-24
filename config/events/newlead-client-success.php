<?php

return [
    'event_type' => 'newlead-client-success',
    'channel_name' => 'client-success-department',
    'slack_webhook' => $_ENV['SLACK_WEBHOOK_NEWLEAD_CLIENT_SUCCESS'] ?? '',
    'message_template' => ':sparkles: *New Lead - Client Success* :sparkles:' . PHP_EOL . PHP_EOL .
        '*Name:* {first_name} {last_name}' . PHP_EOL .
        '*Email:* {email}' . PHP_EOL .
        '*Phone:* {phone}' . PHP_EOL .
        '*Source:* {source}' . PHP_EOL . PHP_EOL .
        '*UTM Tracking:*' . PHP_EOL .
        '• Source: {utmsource}' . PHP_EOL .
        '• Term: {utmterm}' . PHP_EOL .
        '• Campaign: {utmcampaign}' . PHP_EOL .
        '• Medium: {utmmedium}' . PHP_EOL .
        '• Content: {utmcontent}' . PHP_EOL . PHP_EOL .
        '*Payment Details:*' . PHP_EOL .
        '• Amount Paid: ${amount_paid}' . PHP_EOL .
        '• Due Amount: ${amount_due}' . PHP_EOL .
        '• Total Amount: ${total_amount}' . PHP_EOL .
        '• Payment Date: {payment_date}' . PHP_EOL .
        '• Transaction ID: {transaction_id}' . PHP_EOL .
        '• Invoice Number: {invoice_number}' . PHP_EOL .
        '• Recorded By: {recorded_by}',
    'expected_fields' => [
        'event-type' => 'newlead-client-success',
        'first_name' => '{{contact.first_name}}',
        'last_name' => '{{contact.last_name}}',
        'email' => '{{contact.email}}',
        'phone' => '{{contact.phone}}',
        'source' => '{{contact.source}}',
        'utmsource' => '{{contact.attributionSource.utmSource}}',
        'utmterm' => '{{contact.attributionSource.utmTerm}}',
        'utmcampaign' => '{{contact.attributionSource.utmCampaign}}',
        'utmmedium' => '{{contact.attributionSource.utmMedium}}',
        'utmcontent' => '{{contact.lastAttributionSource.utmContent}}',
        'amount_paid' => '{{payment.invoice.amount_paid}}',
        'amount_due' => '{{payment.invoice.amount_due}}',
        'total_amount' => '{{payment.total_amount}}',
        'payment_date' => '{{payment.created_on}}',
        'transaction_id' => '{{payment.transaction_id}}',
        'invoice_number' => '{{payment.invoice.number}}',
        'recorded_by' => '{{payment.invoice.recorded_by}}'
    ],
    'priority' => 'high',
    'notification_type' => 'lead_payment'
];