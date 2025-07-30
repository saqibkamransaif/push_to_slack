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
        'event_type' => 'Event type identifier',
        'source' => 'Appointment source (e.g., website, phone, referral)',
        'reschedule_link' => 'Link to reschedule the appointment',
        'cancellation_link' => 'Link to cancel the appointment',
        'calendar_name' => 'Calendar name where appointment is scheduled',
        'brokerage_name' => 'Contact brokerage name',
        'brokerage_website' => 'Contact brokerage website or social media link',
        'ready_to_invest' => 'Ready to invest response',
        'we_only_work_with_brokerage_owners_and_team_leads_who_want_to_hire_more_agents_is_that_you' => 'Brokerage owner confirmation',
        'please_select_your_current_agent_count' => 'Current agent count',
        'please_select_your_target_agent_count' => 'Target agent count',
        'commitment_acknowledgment' => 'Commitment acknowledgment response',
        'are_you_the_decision_maker' => 'Decision maker status',
        'goal_commitment_2025_scale_1_10' => 'Goal commitment 2025 (scale 1-10)'
    ],
    
    /**
     * Message template for appointments with enhanced styling
     */
    'message_template' => '📅 *New Appointment Scheduled*

    
⏰ Appointment Time: `{appointment_time}`


👤 Contact Information:
• *Name:* {name}
• *Email:* {email}
• *Phone:* {phone}
• *Source:* {source}


🏢 Brokerage Details:
• *Brokerage:* {brokerage_name}
• *Website:* {brokerage_website}


📊 Business Information:
• *Current Agent Count:* {please_select_your_current_agent_count}
• *Target Agent Count:* {please_select_your_target_agent_count}
• *Ready to Invest:* {ready_to_invest}
• *Brokerage Owner/Team Lead:* {we_only_work_with_brokerage_owners_and_team_leads_who_want_to_hire_more_agents_is_that_you}
• *Are You the Decision Maker:* {are_you_the_decision_maker}
• *Goal Commitment 2025 (1-10):* {goal_commitment_2025_scale_1_10}


👥 Assignment:
• *Assigned to:* {assignedTo}
• *Appointment with:* {appointmentWith}
• *Calendar:* {calendar_name}


🔗 Actions:
<{reschedule_link}|📅 Reschedule> | <{cancellation_link}|❌ Cancel>

_Event Type: {event_type}_'
];