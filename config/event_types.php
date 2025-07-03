<?php
/**
 * Event Types Configuration Loader
 * Loads event configurations from separate files
 * @return array Event types configuration
 */

$eventTypes = [];
$eventsDir = __DIR__ . '/events';

// Load all event configuration files
if (is_dir($eventsDir)) {
    $eventFiles = glob($eventsDir . '/*.php');
    
    foreach ($eventFiles as $file) {
        $eventType = basename($file, '.php');
        $config = require $file;
        
        if (is_array($config)) {
            $eventTypes[$eventType] = $config;
        }
    }
}

return $eventTypes;

/**
 * Configuration Notes:
 * 
 * 1. Event Type Key: Must match the 'event-type' value sent in webhooks
 * 2. Slack Channel: Default channel for each event type (configurable)
 * 3. Priority: Determines urgency of notification
 * 4. Notification Type: 
 *    - 'immediate': Send to Slack right away
 *    - 'batch': Can be grouped if multiple arrive
 * 5. Expected Fields: Document what data should be included
 * 6. Message Template: Uses {fieldName} placeholders for dynamic content
 * 
 * Adding New Event Types:
 * - Add new array entry with unique key
 * - Define all required properties
 * - Create appropriate message template
 * - Update Slack channel mapping as needed
 */