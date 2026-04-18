#!/bin/bash
MONITOR_DIR="/var/www/html/"
BACKUP_DIR="/Backup"
MQTT_HOST="192.168.30.22"
MQTT_TOPIC="d/floorplan_version"
LOCK_FILE="/tmp/backup_cleanup_last_run"

execute_backup() {
    VERSION=$(date +%Y%m%d%H%M%S)
    mosquitto_pub -h "$MQTT_HOST" -t "$MQTT_TOPIC" -m "$VERSION" -r
    TARGET="$BACKUP_DIR/$VERSION"
    LATEST=$(ls -1d "$BACKUP_DIR"/*/ 2>/dev/null | sort -r | head -n 1)
    mkdir -p "$TARGET"
    OPTS="-a --delete"
    [ -n "$LATEST" ] && OPTS="$OPTS --link-dest=$LATEST"
    rsync $OPTS "$MONITOR_DIR/" "$TARGET/"
    echo "Backup: $TARGET"
}

cleanup() {
    if [[ -f "$LOCK_FILE" && "$(cat "$LOCK_FILE")" == "$(date +%Y-%m-%d)" ]]; then return; fi
    cd "$BACKUP_DIR" || return
    local now=$(date +%s)
    local dirs=($(ls -1d * 2>/dev/null | sort -r))
    local count=${#dirs[@]}
    if [ "$count" -gt 20 ]; then
        for (( i=20; i<count; i++ )); do
            local d="${dirs[$i]}"
            local ts=$(date -d "${d:0:4}-${d:4:2}-${d:6:2} ${d:8:2}:${d:10:2}:${d:12:2}" +%s)
            local age=$(( (now - ts) / 86400 ))
            local keep=0
            if [ "$age" -gt 365 ]; then rm -rf "$d"; continue; fi
            if [ "$age" -gt 30 ]; then
                prev="${dirs[$((i-1))]}"
                [[ "${d:0:6}" != "${prev:0:6}" ]] && keep=1
            elif [ "$age" -gt 7 ]; then
                prev="${dirs[$((i-1))]}"
                [[ "${d:0:8}" != "${prev:0:8}" ]] && keep=1
            else
                prev="${dirs[$((i-1))]}"
                [[ "${d:0:10}" != "${prev:0:10}" ]] && keep=1
            fi
            [[ $keep -eq 0 ]] && rm -rf "$d"
        done
    fi
    date +%Y-%m-%d > "$LOCK_FILE"
}

inotifywait -m -r -e close_write -e moved_to --format '%w%f' "$MONITOR_DIR" | while read FILE
do
    if [[ "$FILE" == "/var/www/html/index.php" ]] || [[ "$FILE" == *.png ]] || [[ "$FILE" == *.webp ]] || [[ "$FILE" == *.gz ]]; then
        while read -t 2 -r; do :; done
        execute_backup
        cleanup
    fi
done