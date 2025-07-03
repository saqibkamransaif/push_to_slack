# Modular Event Structure

The webhook system now uses a **modular structure** where each event type has its own dedicated configuration file.

## Structure

```
config/
├── events/                    # Individual event configurations
│   ├── contract_status_updates.php  # Contract-specific logic
│   ├── newlead.php           # Lead-specific logic
│   ├── newappointment.php    # Appointment-specific logic
│   └── ebooklead.php         # Ebook-specific logic
└── event_types.php           # Loader that combines all events
```

## Benefits

1. **Separation of Concerns** - Each event type has its own file
2. **Easy Maintenance** - Modify one event without affecting others
3. **Custom Logic** - Each event can have specific status handlers
4. **Clean Organization** - Related code stays together
5. **Easy to Add** - New events are just new files

## Event Configuration Structure

Each event file (e.g., `contract_status_updates.php`) contains:

```php
return [
    'name' => 'Contract Status Update',
    'description' => 'Triggered when a contract status changes',
    'slack_channel' => 'contract_status_updates',
    'webhook_env_var' => 'SLACK_WEBHOOK_CONTRACT_STATUS_UPDATES',
    'priority' => 'high',
    'notification_type' => 'immediate',
    
    // Expected fields in webhook data
    'expected_fields' => [...],
    
    // Message template
    'message_template' => '...',
    
    // Custom status handling (optional)
    'valid_statuses' => [...],
    'status_handler' => function($status) { ... }
];
```

## Contract Status Updates Example

The `contract_status_updates.php` file includes:

- **4 specific status types**: Sent, Viewed, Signed, Completed
- **Status-specific emojis**: 📤, 👀, ✍️, ✅
- **Dynamic headings**: "Contract Sent", "Contract Viewed", etc.
- **Validation**: Only accepts the 4 defined statuses

## Adding New Event Types

### Option 1: Use the Helper Script
```bash
php scripts/add_event_type.php
```

### Option 2: Manual Creation
1. Create `config/events/your_event.php`
2. Add webhook URL to `.env`
3. Customize the configuration

## Environment Variables

Each event automatically maps to an environment variable:
- `contract_status_updates` → `SLACK_WEBHOOK_CONTRACT_STATUS_UPDATES`
- `newlead` → `SLACK_WEBHOOK_NEWLEAD`
- `custom_event` → `SLACK_WEBHOOK_CUSTOM_EVENT`

## Backward Compatibility

The system maintains **full backward compatibility** - existing webhooks continue to work exactly as before, but now benefit from the modular structure.

## Custom Status Handlers

Events can define custom logic for status handling:

```php
'status_handler' => function($status) {
    switch(strtolower($status)) {
        case 'urgent':
            return ['emoji' => '🚨', 'heading' => 'Urgent Update'];
        case 'normal':
            return ['emoji' => '📋', 'heading' => 'Standard Update'];
    }
}
```

This modular approach makes the system highly maintainable and extensible! 🚀