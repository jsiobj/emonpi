#!/bin/sh

echo "======== Log file backup" >> /var/log/varlog-backup.log
date >> /var/log/varlog-backup.log
rsync -av /var/log/* /home/pi/data/logtools/log.backup >> /var/log/varlog-backup.log 2>&1
echo "======== End" >> /var/log/varlog-backup.log
