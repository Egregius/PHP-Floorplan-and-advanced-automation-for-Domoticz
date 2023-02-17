#!/bin/bash
mkdir -p /var/log
/usr/sbin/service domoticz stop
/usr/sbin/service nginx stop
rsync -aP /domoticz/ /temp/domoticz/
touch /temp/domoticz.log
touch /temp/phperror.log
chmod 666 /temp/*.log
mkdir -p /temp/php-file-cache
chmod 777 /temp/php-file-cache
mkdir -p /var/log/nginx/
chmod 755 /var/log/nginx
touch /var/log/nginx/access.log
touch /var/log/nginx/error.log
touch /var/log/lgtv-error.log
chmod 666 /var/log/lgtv-error.log
sleep 5
/usr/sbin/service domoticz stop
/usr/sbin/service nginx stop
/usr/sbin/service mysql start
sleep 5
/usr/sbin/service nginx start
/usr/sbin/service domoticz start
