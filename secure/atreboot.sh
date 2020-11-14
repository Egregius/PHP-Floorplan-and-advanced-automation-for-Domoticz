#!/bin/bash
touch /var/log/fail2ban.log
service mysql stop
service domoticz stop
service nginx stop
rsync -aP /mysqldb/ /temp/mysql/
rsync -aP /domoticz/ /temp/domoticz/
touch /temp/domoticz.log
touch /temp/phperror.log
chmod 666 /temp/*.log
mkdir -p /var/log/nginx/
chmod 755 /var/log/nginx
sleep 5
service mysql start
service nginx start
service domoticz start
sleep 5
service mysql start
service nginx start
service domoticz start
