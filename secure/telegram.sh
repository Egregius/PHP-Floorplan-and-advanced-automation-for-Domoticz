#!/bin/bash
MSG="$1"
SILENT="$2"
TO="$3"
curl -X POST -d "text=$MSG&silent=$SILENT&to=$TO" "http://127.0.0.1/secure/telegram.php"
