#!/bin/bash
service mysql stop
rsync -aP /mysqldb/ /temp/mysql/
service mysql start
touch /temp/domoticz.log
touch /temp/phperror.log
chmod 666 /temp/*.log
service domoticz start
mkdir -p /var/log/apache2/
service apache2 stop
service apache2 start