#!/bin/bash
for ((i=1;i<=15;i++));
do
	/usr/bin/php8.0 /var/www/html/secure/smappeepower.php >/dev/null 2>&1 &
	sleep 3.95
done


