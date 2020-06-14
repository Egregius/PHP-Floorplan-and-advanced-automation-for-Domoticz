#!/bin/bash
touch /temp/domoticz.log
touch /temp/phperror.log
mkdir -p /temp/mysql/
chown mysql:mysql /temp/mysql/
service mariadb stop
rsync -aP /mysqldb/ /temp/mysql/
chmod 666 /temp/*.log
