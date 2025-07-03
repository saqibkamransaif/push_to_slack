<?php
/**
 * New Appointment Event Configuration
 * Handles appointment booking notifications
 * 
 * Event Type: newappointment
 * Channel: appointments
 * Webhook URL: SLACK_WEBHOOK_NEWAPPOINTMENT
 */

return [
    'name' => 'New Appointment',
    'description' => 'Triggered when an appointment has been booked',
    'slack_channel' => 'appointments',
    'webhook_env_var' => 'SLACK_WEBHOOK_NEWAPPOINTMENT',
    'priority' => 'high',
    'notification_type' => 'immediate',
    
    /**
     * Expected fields in webhook data
     */
    'expected_fields' => [
        'customerName' => 'Customer name',
        'appointmentDate' => 'Appointment date',
        'appointmentTime' => 'Appointment time',
        'service' => 'Service type',
        'duration' => 'Appointment duration',
        'assignedTo' => 'Staff member assigned'
    ],
    
    /**
     * Message template for appointments
     */
    'message_template' => ':calendar: *New Appointment Booked*
Customer: {customerName}
Date: {appointmentDate}
Time: {appointmentTime}
Service: {service}
Duration: {duration}
Assigned to: {assignedTo}'
];