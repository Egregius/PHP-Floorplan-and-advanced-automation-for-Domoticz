#!/bin/bash
MONITOR_DIR="/var/www/"
BACKUP_DIR="/Backup"
LOCK_FILE="/tmp/backup_cleanup_last_run"
EXCLUDE_FILE="/var/www/html/secure/backup_exclude.list"
LOG_BASE="/var/log/fp-monitor"
LAST_BACKUP=0

get_log() {
    echo "${LOG_BASE}-$(date +%Y-%m).log"
}
echo "$(date '+%Y-%m-%d %H:%M:%S') - fp-monitor started" >> "$(get_log)"

execute_backup() {
    local files=("/var/www/html/index.php" "/var/www/html/scripts/floorplan.js.gz" "/var/www/html/styles/floorplan.css.gz")
    local max_ts=$(stat -c %Y "${files[@]}" | sort -rn | head -n 1)
    local mqtt_version=$(date -d "@$max_ts" +%Y%m%d%H%M%S)
    /usr/bin/mosquitto_pub -h "192.168.30.22" -t "d/floorplan_version" -m "$mqtt_version" -r
    local backup_version=$(/bin/date +%Y%m%d%H%M%S)
    local target="$BACKUP_DIR/$backup_version"
    local latest=$(/bin/ls -1d "$BACKUP_DIR"/*/ 2>/dev/null | /usr/bin/sort -r | /usr/bin/head -n 1)
    /bin/mkdir -p "$target"
    OPTS="-a --delete --size-only --prune-empty-dirs --exclude-from=$EXCLUDE_FILE"
    [ -n "$latest" ] && OPTS="$OPTS --link-dest=$latest"
    echo "$(date '+%Y-%m-%d %H:%M:%S') - Start backup: $backup_version" >> "$(get_log)"
    /usr/bin/rsync $OPTS "$MONITOR_DIR/" "$target/" >> "$(get_log)" 2>&1
    if [ $? -eq 0 ]; then
        /usr/bin/find "$target" -type d -empty -delete
        mkdir -p "$target/__changes"
        /usr/bin/find "$target" -path "$target/__changes" -prune -o -type f -links 1 -print | while read -r file; do
            # Bepaal het relatieve pad
            rel_path="${file#$target/}"
            # Maak de mappenstructuur aan in 'changes'
            mkdir -p "$(dirname "$target/__changes/$rel_path")"
            # Maak de hard link
            ln "$file" "$target/__changes/$rel_path"
        done
        echo "$(date '+%Y-%m-%d %H:%M:%S') - Backup succesvol: $target (MQTT: $mqtt_version)" >> "$(get_log)"
    else
        echo "$(date '+%Y-%m-%d %H:%M:%S') - ERROR: Rsync gefaald!" >> "$(get_log)"
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
                echo "$(date '+%Y-%m-%d %H:%M:%S') - Verwijderen: $d" >> "$(get_log)"
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
            [[ $keep -eq 0 ]] && { echo "$(date '+%Y-%m-%d %H:%M:%S') - Retentie: $d" >> "$(get_log)"; /bin/rm -rf "$d"; }
        done
    fi
    /bin/date +%Y-%m-%d > "$LOCK_FILE"
}
/usr/bin/inotifywait -m -r -e close_write -e moved_to --format '%w%f' "$MONITOR_DIR" | while read FILE
do
    if [[ "$FILE" == "/var/www/html/secure/fp-monitor.sh" ]]; then
        echo "$(date '+%Y-%m-%d %H:%M:%S') - Monitor script gewijzigd, service herstarten..." >> "$(get_log)"
        /usr/bin/systemctl restart fp-monitor.service
        exit 0
    fi
    case "$(basename "$FILE")" in
        *.php|*.png|*.webp|*.gz|*.sh|*.py)
            NOW=$(date +%s)
            if (( NOW - LAST_BACKUP < 5 )); then continue; fi
            echo "$(date '+%Y-%m-%d %H:%M:%S') - Event voor: $FILE (start backup)" >> "$(get_log)"
            LAST_BACKUP=$(date +%s)
            execute_backup
            cleanup
            ;;
    esac
done