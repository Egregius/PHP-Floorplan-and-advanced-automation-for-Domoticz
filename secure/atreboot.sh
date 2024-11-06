#!/bin/bash
mkdir -p /var/log
/usr/sbin/service mysql stop
/usr/sbin/service domoticz stop
/usr/sbin/service nginx stop
/var/www/read_p1.py
#rsync -aP /var/lib/mysql/ /temp/mysql/
#rsync -aP /domoticz/ /temp/domoticz/
touch /temp/domoticz.log
touch /temp/phperror.log
chmod 666 /temp/*.log
mkdir -p /temp/php-file-cache
chmod 777 /temp/php-file-cache
mkdir -p /var/log/nginx/
chmod 755 /var/log/nginx
touch /var/log/nginx/access.log
touch /var/log/nginx/error.log
sleep 1
/usr/sbin/service mysql start
sleep 1
/usr/sbin/service nginx start
/usr/sbin/service domoticz start
/var/www/read_p1.py