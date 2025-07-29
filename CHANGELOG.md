# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

### Added
- New event type: `new_message_setter` for handling messages sent to setter
  - Channel: speed-to-lead
  - Fields: name, email, message_subject, message_body
  - Format: Contact name and email with message subject and body
- **Automatic Webhook Cleanup System**
  - Configurable automatic deletion of old webhook files after 12 hours (default)
  - Environment variables: `WEBHOOK_CLEANUP_ENABLED`, `WEBHOOK_CLEANUP_HOURS`
  - Dual cleanup mechanism: scheduled cron job + automatic cleanup during webhook processing
  - Management script: `scripts/setup_webhook_cleanup.php` for configuration and testing

### Changed
- Updated `recruitingsecretsdownload` event configuration
  - Changed channel from `lead-flow` to `speed-to-lead`
  - Updated webhook URL for speed-to-lead channel integration
- **Webhook Configuration Standardization**
  - All event configurations now use `webhook_env_var` pattern consistently
  - Eliminated direct `$_ENV` access in event configuration files
  - Improved webhook URL management through environment variables only

### Features / Modules
- **Event Management**: Enhanced event type system with new message setter functionality
- **Slack Integration**: Improved channel routing and webhook management
- **Storage Management**: Automated cleanup system for optimal space utilization
- **Security**: Ensured all webhook URLs are stored only in .env file with no hardcoded references