#!/bin/bash
service mysql stop
service domoticz stop
service apache2 stop
rsync -aP /mysqldb/ /temp/mysql/
rsync -aP /domoticz/ /temp/domoticz/
touch /temp/domoticz.log
touch /temp/phperror.log
chmod 666 /temp/*.log
mkdir -p /var/log/apache2/
chmod 755 /var/log/apache2
sleep 5
service mysql start
service apache2 start
service domoticz start
