# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

### Added
- New event type: `new_message_setter` for handling messages sent to setter
  - Channel: speed-to-lead
  - Fields: name, email, message_subject, message_body
  - Format: Contact name and email with message subject and body

### Changed
- Updated `recruitingsecretsdownload` event configuration
  - Changed channel from `lead-flow` to `speed-to-lead`
  - Updated webhook URL for speed-to-lead channel integration

### Features / Modules
- **Event Management**: Enhanced event type system with new message setter functionality
- **Slack Integration**: Improved channel routing and webhook management