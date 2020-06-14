#!/bin/bash
service mariadb stop
rsync -aP /mysqldb/ /temp/mysql/
service mariadb start
service domoticz stop
rsync -aP /domoticzdb/ /temp/domoticzdb/
touch /temp/domoticz.log
touch /temp/phperror.log
mkdir -p /temp/mysql/
chown mysql:mysql /temp/mysql/
chmod 666 /temp/*.log
service domoticz start
