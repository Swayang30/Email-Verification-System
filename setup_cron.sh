#!/bin/bash
# This script should set up a CRON job to run cron.php every 5 minutes.
# You need to implement the CRON setup logic here.

#!/bin/bash

# Absolute path to PHP and cron.php
PHP_PATH=$(which php)
CRON_FILE_PATH=$(cd "$(dirname "$0")" && pwd)/cron.php

# CRON job definition (every 5 minutes)
CRON_JOB="*/5 * * * * $PHP_PATH $CRON_FILE_PATH"

# Check if CRON job already exists
(crontab -l 2>/dev/null | grep -F "$CRON_JOB") >/dev/null

if [ $? -eq 0 ]; then
    echo "CRON job already exists. No changes made."
else
    (crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -
    echo "CRON job added successfully to run every 5 minutes."
fi
