#!/bin/bash
MONITOR_DIR="/var/www/html/"
MQTT_HOST="192.168.2.22"
MQTT_TOPIC="d/floorplan_version"

log_change() {
    VERSION=$(date +%Y%m%d%H%M%S)
    mosquitto_pub -h "$MQTT_HOST" -t "$MQTT_TOPIC" -m "$VERSION" -r
    echo "Update verstuurd: $VERSION"
}
inotifywait -m -r -e close_write --format '%w%f' "$MONITOR_DIR" | while read FILE
do
    if [[ "$FILE" == *.php ]] || [[ "$FILE" == *.gz ]]; then
        log_change
    fi
done
