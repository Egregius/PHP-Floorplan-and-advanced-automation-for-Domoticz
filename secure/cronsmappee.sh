#!/bin/bash
for ((i=1;i<=30;i++));
do
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/smappeepower.php" >/dev/null 2>&1 &
	sleep 1.95
done


