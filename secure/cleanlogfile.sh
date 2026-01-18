#!/bin/bash
#!/usr/bin/env bash

declare -A LOGS=(
  ["/temp/domoticz.log"]=5000
  ["/temp/mqttpublish.log"]=1000
  ["/temp/floorplan-access.log"]=500
  ["/temp/Temps.log"]=500
  ["/temp/Rains.log"]=500
  ["/temp/Winds.log"]=500
  ["/temp/opcache.log"]=500
  ["/temp/phperror.log"]=500
)

for file in "${!LOGS[@]}"; do
	lines=$(( LOGS[$file] * 9 / 10 ))
	tmp="${file}.tmp"
	if [ "$(wc -l < "$file")" -gt "$lines" ]; then
		tail -n "$lines" "$file" > "$tmp" && mv "$tmp" "$file"
		chmod 766 "$file"
	fi
done
exit 0
