# Webhook Format Documentation

## Required Structure

All webhooks must send data in the following format:

```json
{
    "customData": {
        "event-type": "EVENT_TYPE_HERE",
        // All other data fields go here
    }
}
```

**Important:** Only data inside `customData` will be processed. Everything else in the webhook payload is ignored.

## Event Types and Required Fields

### 1. New Lead (`newlead`)
```json
{
    "customData": {
        "event-type": "newlead",
        "name": "Saqib Kamran",
        "email": "email@saqibkamran.com",
        "phone": "+92 303 8001800",
        "source": "Website",
        "assignedTo": "Naomi Shtaiwi"
    }
}
```

### 2. New Appointment (`newappointment`)
```json
{
    "customData": {
        "event-type": "newappointment",
        "customerName": "Jane Smith",
        "appointmentDate": "2025-07-05",
        "appointmentTime": "2:00 PM",
        "service": "Consultation",
        "duration": "1 hour",
        "assignedTo": "Dr. Johnson"
    }
}
```

### 3. Ebook Lead (`ebooklead`)
```json
{
    "customData": {
        "event-type": "ebooklead",
        "leadName": "Bob Wilson",
        "email": "bob@example.com",
        "ebookTitle": "Ultimate Guide",
        "downloadDate": "2025-07-03 15:30:00",
        "source": "Facebook Ad",
        "marketingConsent": "Yes"
    }
}
```

### 4. Contract Status Updates (`contract_status_updates`)
```json
{
    "customData": {
        "event-type": "contract_status_updates",
        "documentTitle": "Annual Service Agreement - Tech Corp",
        "clientName": "Tech Corp Inc.",
        "clientEmail": "contact@techcorp.com",
        "status": "Completed",
        "time": "2025-07-03 20:52:53"
    }
}
```

**Status Types with Emojis:**
- `Sent` → 📤 *Contract Sent*
- `Viewed` → 👀 *Contract Viewed* 
- `Signed` → ✍️ *Contract Signed*
- `Completed` → ✅ *Contract Completed*

## Notes

- The `event-type` field is **required** and must match one of the configured event types
- All field names in `customData` should match the placeholders in the message templates
- Fields not included will show as "N/A" in the Slack message
- The system automatically adds a timestamp if not provided