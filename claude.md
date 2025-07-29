When writing code, follow these requirements:

1. Use MVC framework structure
2. Keep files short and logically organized - refactor long files into smaller, purpose-specific modules
3. Add clear comments for every function describing its purpose and return value
4. Store all environment variables in .env file, include sample-env for GitHub, add .env to .gitignore
5. Use descriptive variable names and appropriate data types for performance and readability
6. Error handling, log maintainance is an essential part of the project. Maintain separate files for logs and errors, these would be temporary files with auto-deletion mechanism after 24 hours. 
7. Always put variables / slack webhook urls in .env file


For code updates: Only provide the modified function code with brief instructions on placement, not complete files.

This application receives a webhook that contains json data. We'll select the information from the json data and will send that to slack. The slack channels and custom message will be configured separately so that this application can be used in generic cases instead of very specific cases. We would be able to select the channels we want to send information too, and then we'll also define the messages format (with data) we'll send to each channel and we'll use switch statements to define the cases where we'll choose what type of message needs to be sent to what channel and it will look for the format of the data and will then send to the selected channel. We'll send event-type in the json data and based on that event-type the channel and message format will be selected. 

Technologies:
Backend: PHP
Frontend: Bootstrap

webhook url: https://api.saqibkamran.com/public/webhook.php


I will tell claude following instructions when I need to send new notification to slack:

Add the following event-type:
event_type: "new_message_setter"
channel_name: "speed-to-lead"
slack_webhook: https://hooks.slack.com/services/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
Example Webhook:


Custom Data
These custom key-value pairs will be included along with the standard data

event_type
new_message_setter

name
{{contact.name}}

email
{{contact.email}}

message_subject
{{message.subject}}

message_body
{{message.body}}


Format to send to slack:
{
  "text": "{{contact.name}} ({{contact.email}}): 
    {{message.subject}}
    {{message.body}}"
}
