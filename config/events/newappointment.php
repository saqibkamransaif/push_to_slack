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
        'what_is_your_brokerages_website_or_social_media_provide_link' => 'Contact brokerage website or social media link',
        'if_you_were_able_to_find_a_way_to_grow_scale_your_business_would_you_be_ready_to_invest' => 'Ready to invest response',
        'we_only_work_with_brokerage_owners_and_team_leads_who_want_to_hire_more_agents_is_that_you' => 'Brokerage owner confirmation',
        'please_select_your_current_agent_count' => 'Current agent count',
        'please_select_your_target_agent_count' => 'Target agent count',
        'i_recognize_that_modern_day_brokerage_dedicates_significant_time_and_resources_to_developing_a_custom_roadmap_for_each_business_because_of_this_i_will_show_up_on_time_if_i_need_to_reschedule_the_meeting_i_will_provide_advance_notice' => 'Commitment acknowledgment response',
        'we_only_work_with_broker_owners_and_team_leaders_who_want_that_way_to_hire_more_agents_are_you_the_decision_maker' => 'Decision maker status',
        'biggest_challenge_for_scaling' => 'Biggest challenge for scaling',
        'currently_monthly_revenue' => 'Currently monthly revenue'
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
• *Website:* {what_is_your_brokerages_website_or_social_media_provide_link}


📊 Business Information:
• *Current Agent Count:* {please_select_your_current_agent_count}
• *Target Agent Count:* {please_select_your_target_agent_count}
• *Ready to Invest:* {if_you_were_able_to_find_a_way_to_grow_scale_your_business_would_you_be_ready_to_invest}
• *Brokerage Owner/Team Lead:* {we_only_work_with_brokerage_owners_and_team_leads_who_want_to_hire_more_agents_is_that_you}
• *Are You the Decision Maker:* {we_only_work_with_broker_owners_and_team_leaders_who_want_that_way_to_hire_more_agents_are_you_the_decision_maker}
• *Biggest challenge for scaling:* {biggest_challenge_for_scaling}
• *Currently Monthly Revenue:* {currently_monthly_revenue}


👥 Assignment:
• *Assigned to:* {assignedTo}
• *Appointment with:* {appointmentWith}
• *Calendar:* {calendar_name}


🔗 Actions:
<{reschedule_link}|📅 Reschedule> | <{cancellation_link}|❌ Cancel>

_Event Type: {event_type}_'
];