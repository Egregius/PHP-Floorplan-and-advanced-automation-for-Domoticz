#!/bin/bash

# Maximale retries en interval
MAX_RETRIES=5
SLEEP_INTERVAL=1
FPS=""

for i in $(seq 1 $MAX_RETRIES); do
    # Actuele refresh rate ophalen
    FPS=$(adb shell dumpsys SurfaceFlinger | grep -Eo '([0-9]+\.[0-9]+) Hz' | head -1 | grep -Eo '[0-9]+\.[0-9]+')
    
    if [ -n "$FPS" ]; then
        echo "Found refresh rate: $FPS Hz"
        break
    else
        echo "No refresh rate yet, retry $i/$MAX_RETRIES..."
        sleep $SLEEP_INTERVAL
    fi
done

# Fallback als het niet lukt
if [ -z "$FPS" ]; then
    echo "Failed to get refresh rate, using default 60 Hz"
    FPS=60
fi

# Refresh-rate instellen
adb shell wm refresh-rate $FPS

# Fully starten
adb shell am start -n de.ozerov.fully/.MainActivity

echo "Fully started with refresh rate $FPS Hz"
