#!/bin/bash
service mariadb stop
rsync -aP /mysqldb/ /temp/mysql/
service mariadb start
touch /temp/domoticz.log
touch /temp/phperror.log
chmod 666 /temp/*.log
service domoticz start
