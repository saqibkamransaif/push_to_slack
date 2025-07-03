<?php
/**
 * New Appointment Event Configuration
 * Handles appointment booking notifications
 * 
 * Event Type: newappointment
 * Channel: daily-appointments
 * Webhook URL: SLACK_WEBHOOK_NEWAPPOINTMENT
 */

return [
    'name' => 'New Appointment',
    'description' => 'Triggered when a new appointment has been booked',
    'slack_channel' => 'daily-appointments',
    'webhook_env_var' => 'SLACK_WEBHOOK_NEWAPPOINTMENT',
    'priority' => 'high',
    'notification_type' => 'immediate',
    
    /**
     * Expected fields in webhook data
     */
    'expected_fields' => [
        'name' => 'Customer name',
        'phone' => 'Customer phone number',
        'email' => 'Customer email address',
        'appointment_time' => 'Scheduled appointment date and time',
        'assignedTo' => 'Staff member assigned to the appointment',
        'appointmentWith' => 'Staff member the appointment is with',
        'source' => 'Appointment source (e.g., website, phone, referral)',
        'reschedule_link' => 'Link to reschedule the appointment',
        'cancellation_link' => 'Link to cancel the appointment',
        'calendar_name' => 'Calendar name where appointment is scheduled'
    ],
    
    /**
     * Message template for appointments with enhanced styling
     */
    'message_template' => ':calendar: *New Appointment Scheduled*

:alarm_clock: *APPOINTMENT TIME*
`{appointment_time}`

:bust_in_silhouette: *Customer Details*
• Name: *{name}*
• Email: {email}
• Phone: {phone}
• Source: {source}

:office: *Appointment Details*
• Assigned to: *{assignedTo}*
• Appointment with: *{appointmentWith}*
• Calendar: {calendar_name}

:link: *Quick Actions*
<{reschedule_link}|Reschedule Appointment>
<{cancellation_link}|Cancel Appointment>

_Received: {timestamp}_'
];