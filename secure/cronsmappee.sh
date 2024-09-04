#!/bin/bash
for ((i=1;i<=15;i++));
do
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/smappeepower.php" >/dev/null 2>&1 &
	sleep 3.95
done
#pkill -f "p1.php"
#/var/www/p1.php

