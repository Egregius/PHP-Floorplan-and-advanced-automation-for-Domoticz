#!/bin/bash
service mysql stop
rsync -aP /mysqldb/ /temp/mysql/
service mysql start
touch /temp/domoticz.log
touch /temp/phperror.log
chmod 666 /temp/*.log
service domoticz start
