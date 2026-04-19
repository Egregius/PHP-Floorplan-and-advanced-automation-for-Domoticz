#!/bin/bash
MONITOR_DIR="/var/www/html/"
BACKUP_DIR="/Backup"
MQTT_HOST="192.168.30.22"
MQTT_TOPIC="d/floorplan_version"
LOCK_FILE="/tmp/backup_cleanup_last_run"
EXCLUDE_FILE="/var/www/html/secure/backup_exclude.list"

execute_backup() {
    VERSION=$(/bin/date +%Y%m%d%H%M%S)
    /usr/bin/mosquitto_pub -h "$MQTT_HOST" -t "$MQTT_TOPIC" -m "$VERSION" -r
    TARGET="$BACKUP_DIR/$VERSION"
    LATEST=$(/bin/ls -1d "$BACKUP_DIR"/*/ 2>/dev/null | /usr/bin/sort -r | /usr/bin/head -n 1)
    /bin/mkdir -p "$TARGET"
    
    OPTS="-a --delete --exclude-from=$EXCLUDE_FILE"
    [ -n "$LATEST" ] && OPTS="$OPTS --link-dest=$LATEST"
    
    echo "Start backup: $(date)" >> /var/log/fp-monitor.log
    /usr/bin/rsync $OPTS "$MONITOR_DIR/" "$TARGET/" >> /var/log/fp-monitor.log 2>&1
    
    if [ $? -eq 0 ]; then
        echo "Backup succesvol: $TARGET" >> /var/log/fp-monitor.log
    else
        echo "ERROR: Rsync gefaald! Controleer /var/log/fp-monitor.log"
    fi
}

cleanup() {
    if [[ -f "$LOCK_FILE" && "$(/bin/cat "$LOCK_FILE")" == "$(/bin/date +%Y-%m-%d)" ]]; then return; fi
    cd "$BACKUP_DIR" || return
    local now=$(/bin/date +%s)
    local dirs=($(/bin/ls -1d * 2>/dev/null | /usr/bin/sort -r))
    local count=${#dirs[@]}
    if [ "$count" -gt 20 ]; then
        for (( i=20; i<count; i++ )); do
            local d="${dirs[$i]}"
            local ts=$(/bin/date -d "${d:0:4}-${d:4:2}-${d:6:2} ${d:8:2}:${d:10:2}:${d:12:2}" +%s 2>/dev/null)
            if [ $? -ne 0 ]; then continue; fi
            local age=$(( (now - ts) / 86400 ))
            local keep=0
            if [ "$age" -gt 365 ]; then
                echo "Verwijderen (ouder dan 1 jaar): $d"
                /bin/rm -rf "$d"; continue
            fi
            if [ "$age" -gt 30 ]; then
                prev="${dirs[$((i-1))]}"
                [[ "${d:0:6}" != "${prev:0:6}" ]] && keep=1
            elif [ "$age" -gt 14 ]; then
                prev="${dirs[$((i-1))]}"
                [[ "${d:0:7}" != "${prev:0:7}" ]] && keep=1
            elif [ "$age" -gt 7 ]; then
                prev="${dirs[$((i-1))]}"
                [[ "${d:0:8}" != "${prev:0:8}" ]] && keep=1
            elif [ "$age" -gt 2 ]; then
                prev="${dirs[$((i-1))]}"
                [[ "${d:0:9}" != "${prev:0:9}" ]] && keep=1
            elif [ "$age" -gt 1 ]; then
                prev="${dirs[$((i-1))]}"
                [[ "${d:0:10}" != "${prev:0:10}" ]] && keep=1
            else
                prev="${dirs[$((i-1))]}"
                [[ "${d:0:11}" != "${prev:0:11}" ]] && keep=1
            fi
            [[ $keep -eq 0 ]] && { echo "Verwijderen (retentie): $d"; /bin/rm -rf "$d"; }
        done
    fi
    /bin/date +%Y-%m-%d > "$LOCK_FILE"
}

/usr/bin/inotifywait -m -r -e close_write -e moved_to --format '%w%f' "$MONITOR_DIR" | while read FILE
do
	echo $FILE  >> /var/log/fp-monitor.log 2>&1
    if [[ "$FILE" == *.php ]] || [[ "$FILE" == *.png ]] || [[ "$FILE" == *.webp ]] || [[ "$FILE" == *.gz ]]; then
        /bin/sleep 2
        while read -t 2 -r; do :; done
        execute_backup
        cleanup
    fi
done