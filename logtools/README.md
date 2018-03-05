# Log tools for EmonPi
## Introdution
Those little tools were created because /var/log is mounted on a tmpfs to preserve SD card. This creates a few issues : non existing log files prevents somes services from starting, log files are lost across reboots.

## Files and directories
`~/emonpi/logtools/log.template` contains "log files template" that is directories structure and empty log files that are needed on a fresh install
`~/data/logtools/log.template` log file templates (ie directories and empty log files) created from actual `/var/log` directory (see below)
`~/data/logtools/log.archive` is a full archive of `/var/log`

## What does this do
- At startup : Content of `~/emonpi/logtools/log.template` and `~/data/logtools/log.template` are copied over to `/var/log/`
- On shutdown : Structure (directories and files with no content) is copied to `~/data/logtools/log.template` so it can be restored on next boot
- Every hour (crontab.hourly) : /var/log is archived to `~/data/logtools/log.archive`
