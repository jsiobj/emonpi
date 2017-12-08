#!/bin/sh
# Startup script for /var/log file structure backup and boot init

# Initializing /var/log file structure on startup
restore() {
    cp -Rpv --attributes-only /home/pi/emonpi/logtools/log.template/* /var/log
    if [ -d "/home/pi/data/logtools/log.template" ]; then
        /bin/cp -Rpv --attributes-only /home/pi/data/logtools/log.template/* /var/log
    fi
}

# Backuping current file structure on shutdown
backup() {
    mkdir  /home/pi/data/log.template
    cp -Rpvn --attributes-only /var/log/* /home/pi/data/log.template
}

case $1 in
    backup|restore)
       $1
       ;;

    *)
       echo "Unknown option"
       echo "usage $0 {backup|restore}"
       exit 1
esac
