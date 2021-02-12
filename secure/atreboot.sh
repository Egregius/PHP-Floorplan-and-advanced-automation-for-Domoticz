#!/bin/bash
mkdir -p /var/log
touch /var/log/fail2ban.log
service mysql stop
service domoticz stop
service nginx stop
rsync -aP /mysqldb/ /temp/mysql/
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
service mysql stop
service domoticz stop
service nginx stop
service mysql start
service nginx start
service domoticz start
sleep 5
service mysql start
service nginx start
service domoticz start
