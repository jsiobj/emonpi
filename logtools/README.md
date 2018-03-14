# Log tools for EmonPi
## Introdution
Those little tools were created because /var/log is mounted on a tmpfs to preserve SD card. This creates a few issues : non existing log files prevents somes services from starting, log files are lost across reboots.

## Files and directories
`~/emonpi/logtools/log.template` contains "log files template" that is directories structure and empty log files that are needed on a fresh install
`~/data/logtools/log.archive` is a full backup of `/var/log`

## What does this do
- At startup (oneshot service) : Content of `~/data/logtools/log.backup` and content of `~/emonpi/logtools/log.template` are copied over to `/var/log/`
- Every hour (crontab.hourly) and on shutdown (oneshot service) : /var/log is archived to `~/data/logtools/log.backup`
