<?php
/**
 * New Appointment No Confirmation Event Configuration
 * Handles appointment booking notifications for unconfirmed appointments
 * 
 * Event Type: newappointment_noconfirmation
 * Channel: sales-coordinator
 * Webhook URL: SLACK_WEBHOOK_NEWAPPOINTMENT_NOCONFIRMATION
 */

return [
    'name' => 'New Appointment - No Confirmation',
    'description' => 'Triggered when a new appointment is booked but customer has not confirmed via SMS',
    'slack_channel' => 'sales-coordinator',
    'webhook_env_var' => 'SLACK_WEBHOOK_NEWAPPOINTMENT_NOCONFIRMATION',
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
     * Custom message template for unconfirmed appointments
     */
    'message_template' => ':warning: *Appointment Needs Confirmation*

{name} has booked an appointment with {appointmentWith} at {appointment_time} on {calendar_name} but they haven\'t responded to the confirmation SMS yet. Please contact them to confirm the appointment.

*Quick Links:*
<{reschedule_link}|Reschedule Link>
<{cancellation_link}|Cancellation Link>

_Received: {timestamp}_'
];