# Cron Setup for Automatic Log Cleanup

## Setting up the Cron Job

To automatically clean up logs older than 24 hours, add this cron job:

### 1. Open crontab editor
```bash
crontab -e
```

### 2. Add the following line
```bash
# Run log cleanup every hour
0 * * * * /usr/bin/php /path/to/push_to_slack/scripts/cleanup_logs.php >> /path/to/push_to_slack/logs/cleanup.log 2>&1
```

Replace `/path/to/push_to_slack` with your actual project path.

### 3. Alternative schedules

- Every 6 hours: `0 */6 * * *`
- Every day at 3 AM: `0 3 * * *`
- Twice daily (3 AM and 3 PM): `0 3,15 * * *`

## Manual Testing

Test the cleanup script manually:
```bash
php scripts/cleanup_logs.php
```

## What Gets Cleaned

By default, the script removes:
- Log files older than 24 hours from `/logs` directory
- Rotated log files (*.log.*)

To also clean webhook JSON files, uncomment the webhook cleanup section in the script.

## Monitoring

The cleanup script itself logs its activity to the application logs, so you can monitor cleanup operations.