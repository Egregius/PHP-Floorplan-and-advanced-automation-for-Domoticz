#!/bin/bash
MONITOR_DIR="/var/www/"
BACKUP_DIR="/Backup"
LOCK_FILE="/tmp/backup_cleanup_last_run"
EXCLUDE_FILE="/var/www/backup_exclude.list"
LOG_BASE="/var/log/fp-monitor"

get_log() {
    echo "${LOG_BASE}-$(date +%Y-%m).log"
}

execute_backup() {
    VERSION=$(/bin/date +%Y%m%d%H%M%S)
    TARGET="$BACKUP_DIR/$VERSION"
    LATEST=$(/bin/ls -1d "$BACKUP_DIR"/*/ 2>/dev/null | /usr/bin/sort -r | /usr/bin/head -n 1)
    /bin/mkdir -p "$TARGET"
    
    OPTS="-a --delete --size-only --prune-empty-dirs --exclude-from=$EXCLUDE_FILE"
    [ -n "$LATEST" ] && OPTS="$OPTS --link-dest=$LATEST"
    
    echo "$(date '+%Y-%m-%d %H:%M:%S') - Start backup: $VERSION" >> "$(get_log)"
    /usr/bin/rsync $OPTS "$MONITOR_DIR/" "$TARGET/" >> "$(get_log)" 2>&1
    
    if [ $? -eq 0 ]; then
        /usr/bin/find "$TARGET" -type d -empty -delete
        /usr/bin/mosquitto_pub -h "192.168.30.22" -t "d/floorplan_version" -m "$VERSION" -r
        echo "$(date '+%Y-%m-%d %H:%M:%S') - Backup succesvol: $TARGET" >> "$(get_log)"
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
                echo "$(date '+%Y-%m-%d %H:%M:%S') - Verwijderen (ouder dan 1 jaar): $d" >> "$(get_log)"
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
            [[ $keep -eq 0 ]] && { echo "$(date '+%Y-%m-%d %H:%M:%S') - Verwijderen (retentie): $d" >> "$(get_log)"; /bin/rm -rf "$d"; }
        done
    fi
    /bin/date +%Y-%m-%d > "$LOCK_FILE"
}

/usr/bin/inotifywait -m -r -e close_write -e moved_to --format '%w%f' "$MONITOR_DIR" | while read FILE
do
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $FILE" >> "$LOG_FILE";
	FILENAME=$(basename "$FILE")
    case "$FILENAME" in
		*.php|*.png|*.webp|*.gz|*.sh|*.py)
			/bin/sleep 2
			execute_backup
			cleanup
			;;
	esac
done