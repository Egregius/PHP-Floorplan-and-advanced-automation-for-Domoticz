#!/bin/bash
MONITOR_DIR="/var/www/html/"
BACKUP_DIR="/Backup"
MQTT_HOST="192.168.30.22"
MQTT_TOPIC="d/floorplan_version"
execute_backup() {
    VERSION=$(date +%Y%m%d%H%M%S)
    TARGET="$BACKUP_DIR/$VERSION"
    LATEST=$(ls -1d "$BACKUP_DIR"/*/ 2>/dev/null | sort -r | head -n 1)
    mkdir -p "$TARGET"
    OPTS="-a --delete"
    [ -n "$LATEST" ] && OPTS="$OPTS --link-dest=$LATEST"
    rsync $OPTS "$MONITOR_DIR/" "$TARGET/"
    mosquitto_pub -h "$MQTT_HOST" -t "$MQTT_TOPIC" -m "$VERSION" -r
    echo "Update: $VERSION. Backup: $TARGET"
}
inotifywait -m -r -e close_write -e moved_to --format '%w%f' "$MONITOR_DIR" | while read FILE
do
    if [[ "$FILE" == "/var/www/html/index.php" ]] || [[ "$FILE" == *.png ]] || [[ "$FILE" == *.webp ]] || [[ "$FILE" == *.gz ]]; then
        sleep 1
        execute_backup
    fi
done