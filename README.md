# Webhook to Slack Integration

A PHP-based webhook system that receives JSON data and forwards it to Slack channels with configurable event types and message formats.

## Features

- **Event-Based Routing**: Route different webhook events to specific Slack channels
- **Modular Configuration**: Separate configuration files for each event type
- **Flexible Slack Integration**: Support for both Slack Webhooks and Bot Token API
- **Comprehensive Logging**: Detailed logging with automatic rotation and cleanup
- **Auto-Deletion**: Webhook files are automatically deleted after successful processing
- **Error Handling**: Robust error handling with detailed debug information
- **MVC Architecture**: Clean, maintainable code structure

## Supported Event Types

- **newlead**: New lead notifications (routed to #lead-flow channel)
- **newappointment**: New appointment notifications
- **ebooklead**: Ebook download lead notifications
- **contract_status_updates**: Contract status changes with dynamic emojis
- **recruitingsecretsdownload**: Recruiting content downloads (routed to #lead-flow channel)

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd push_to_slack
   ```

2. **Set up environment variables**
   ```bash
   cp sample-env .env
   ```

3. **Configure your Slack credentials in `.env`**
   - Add your Slack webhook URLs for each event type
   - Or configure a Bot Token for flexible channel posting

4. **Set up web server**
   - Point your web server document root to the `public` directory
   - Ensure PHP has write permissions for `webhooks` and `logs` directories

5. **Configure webhook endpoint**
   - Your webhook URL will be: `https://your-domain.com/webhook.php`

## Configuration

### Environment Variables

Copy `sample-env` to `.env` and configure:

```bash
# Slack Webhook URLs (one per channel)
SLACK_WEBHOOK_NEWLEAD=https://hooks.slack.com/services/YOUR/WEBHOOK/URL
SLACK_WEBHOOK_NEWAPPOINTMENT=https://hooks.slack.com/services/YOUR/WEBHOOK/URL
SLACK_WEBHOOK_EBOOKLEAD=https://hooks.slack.com/services/YOUR/WEBHOOK/URL
SLACK_WEBHOOK_CONTRACT_STATUS_UPDATES=https://hooks.slack.com/services/YOUR/WEBHOOK/URL
SLACK_WEBHOOK_RECRUITINGSECRETSDOWNLOAD=https://hooks.slack.com/services/YOUR/WEBHOOK/URL

# Or use Bot Token (can post to any channel)
SLACK_BOT_TOKEN=xoxb-your-bot-token-here

# Application settings
APP_URL=https://your-domain.com
WEBHOOK_SECRET=your_webhook_secret_here

# Logging
ENABLE_LOGGING=true
ENABLE_ERROR_LOGGING=true
ENABLE_DEBUG_LOGGING=false
DELETE_WEBHOOK_ON_SUCCESS=true
```

### Event Configuration

Each event type has its own configuration file in `config/events/`:

- `newlead.php` - New lead configuration
- `newappointment.php` - New appointment configuration  
- `ebooklead.php` - Ebook lead configuration
- `contract_status_updates.php` - Contract status configuration
- `recruitingsecretsdownload.php` - Recruiting secrets configuration

## Usage

### Webhook Format

Send POST requests to your webhook endpoint with JSON data:

```json
{
  "customData": {
    "event-type": "newlead",
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+1 555 0123",
    "source": "Website Contact Form",
    "assignedTo": "Sales Team"
  }
}
```

### Contract Status Updates

For contract status updates, include a `status` field:

```json
{
  "customData": {
    "event-type": "contract_status_updates",
    "documentTitle": "Service Agreement",
    "clientName": "John Doe",
    "clientEmail": "john@example.com",
    "status": "Signed",
    "time": "2024-01-15 10:30:00"
  }
}
```

Supported status types:
- **Sent** 📤 - Contract has been sent
- **Viewed** 👀 - Contract has been viewed  
- **Signed** ✅ - Contract has been signed
- **Completed** 🎉 - Contract process completed

## Testing

Test scripts are provided in the `tests/` directory:

```bash
# Test new lead webhook
php tests/test_newlead.php

# Test contract status updates
php tests/test_contract_status.php

# Test recruiting secrets download
php tests/test_recruiting_secrets.php
```

## File Structure

```
push_to_slack/
├── app/
│   ├── controllers/
│   │   └── WebhookController.php
│   └── models/
│       ├── Logger.php
│       ├── SlackModel.php
│       └── WebhookModel.php
├── config/
│   ├── events/
│   │   ├── newlead.php
│   │   ├── newappointment.php
│   │   ├── ebooklead.php
│   │   ├── contract_status_updates.php
│   │   └── recruitingsecretsdownload.php
│   ├── bootstrap.php
│   └── event_types.php
├── public/
│   └── webhook.php
├── tests/
│   ├── test_newlead.php
│   ├── test_contract_status.php
│   └── test_recruiting_secrets.php
├── webhooks/ (auto-created)
├── logs/ (auto-created)
├── .env (create from sample-env)
├── sample-env
└── README.md
```

## Logging

The system provides comprehensive logging:

- **Application logs**: `logs/app_YYYY-MM-DD.log`
- **Error logs**: `logs/error_YYYY-MM-DD.log`
- **Automatic rotation**: When logs exceed 10MB
- **Auto-cleanup**: Logs older than 24 hours are automatically deleted

Log levels:
- `INFO` - General information
- `ERROR` - Error conditions
- `DEBUG` - Detailed debug information
- `WEBHOOK` - Webhook-specific events

## Security Features

- Webhook secret validation (optional)
- Environment variable isolation
- Input validation and sanitization
- Error message sanitization in production
- Automatic file cleanup

## Adding New Event Types

1. **Create event configuration file**
   ```php
   // config/events/your_event.php
   return [
       'name' => 'Your Event',
       'description' => 'Description of your event',
       'slack_channel' => 'your-channel',
       'webhook_env_var' => 'SLACK_WEBHOOK_YOUR_EVENT',
       'expected_fields' => [
           'field1' => 'Description',
           'field2' => 'Description'
       ],
       'message_template' => ':emoji: *Event Title*\nField1: {field1}\nField2: {field2}'
   ];
   ```

2. **Add environment variable**
   ```bash
   SLACK_WEBHOOK_YOUR_EVENT=https://hooks.slack.com/services/YOUR/WEBHOOK/URL
   ```

3. **Send webhook with event-type**
   ```json
   {
     "customData": {
       "event-type": "your_event",
       "field1": "value1",
       "field2": "value2"
     }
   }
   ```

## Troubleshooting

### Common Issues

1. **"No Slack credentials configured"**
   - Ensure webhook URLs or bot token are set in `.env`
   - Check that environment variables are loading correctly

2. **"channel_not_found" error**
   - Verify channel names match exactly (without #)
   - For private channels, use webhook URLs instead of bot token
   - Ensure bot is added to private channels

3. **Webhook files not being deleted**
   - Check `DELETE_WEBHOOK_ON_SUCCESS=true` in `.env`
   - Verify write permissions on `webhooks` directory

### Debug Mode

Enable debug logging for detailed troubleshooting:

```bash
ENABLE_DEBUG_LOGGING=true
```

This will log detailed information about:
- Slack API calls and responses
- Webhook processing steps
- Configuration loading
- Error details

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is open source and available under the [MIT License](LICENSE).

## Support

For issues and questions:
1. Check the troubleshooting section
2. Review the logs for detailed error information
3. Open an issue on GitHub with:
   - Error messages from logs
   - Your configuration (without sensitive data)
   - Steps to reproduce the issue
