#!/bin/bash

: '
crontab -e
# m h  dom mon dow   command
* * * * * /usr/bin/nice -n20 /var/www/html/secure/cron.sh >/dev/null 2>&1
* * * * * /usr/bin/nice -n20 /var/www/html/secure/cronsmappee.sh >/dev/null 2>&1
0 0 * * * /usr/bin/nice -n20 curl -s "http://127.0.0.1/secure/cleandomoticzdb.php" >/dev/null 2>&1
1 0 * * * /usr/bin/nice -n20 /var/www/_SQLBackup/sqldaily.sh >/dev/null 2>&1
*/5 * * * * /usr/bin/nice -n20 /var/www/html/secure/cleandisk.sh >/dev/null 2>&1
'

#0
wget -O /dev/null -o /dev/null "http://127.0.0.1/secure/cron.php" >/dev/null 2>&1 &
sleep 9.878
#10
wget -O /dev/null -o /dev/null "http://127.0.0.1/secure/cron.php" >/dev/null 2>&1 &
sleep 9.998
#20
wget -O /dev/null -o /dev/null "http://127.0.0.1/secure/cron.php" >/dev/null 2>&1 &
sleep 9.998
#30
wget -O /dev/null -o /dev/null "http://127.0.0.1/secure/cron.php" >/dev/null 2>&1 &
sleep 9.998
#40
wget -O /dev/null -o /dev/null "http://127.0.0.1/secure/cron.php" >/dev/null 2>&1 &
sleep 9.998
#50
wget -O /dev/null -o /dev/null "http://127.0.0.1/secure/cron.php" >/dev/null 2>&1 &

ps cax | grep domoticz
if [ $? -ne 0 ] ; then
	/usr/sbin/service domoticz.sh stop
	/usr/sbin/service domoticz.sh start
fi

ps cax | grep nginx
if [ $? -ne 0 ] ; then
	/usr/sbin/service nginx stop
	/usr/sbin/service nginx start
fi

ps cax | grep php-fpm7.4
if [ $? -ne 0 ] ; then
	/usr/sbin/service php7.4-fpm stop
	/usr/sbin/service php7.4-fpm start
fi

ps cax | grep mysql
if [ $? -ne 0 ] ; then
	/usr/sbin/service mysql stop
	/usr/sbin/service mysql start
fi

# Remove these lines as they only upload my files to gitbub.
MINUTE=$(date +"%M")
if [ $(($MINUTE%10)) -eq 0 ] ; then
	LAST=$(find /var/www/html -type f ! -name '_*' ! -path "*/stills/*" ! -path "*/sounds/*" ! -path "*/.git/*" ! -path "*/.github/*" ! -path "*/pass2php/*" ! -path "*/phpMyAdmin/*" ! -path "*/google-api-php-client/*" ! -path "*/archive/*" -printf '%T@\n' | sort -n | tail -1 | cut -f1- -d" ")
	PREV=$(cat "/temp/timestampappcache.txt")
	echo $LAST>"/temp/timestampappcache.txt"
	if [ "$LAST" != "$PREV" ]
	then
		cd /var/www/html/
		/usr/bin/nice -n20 git add .
		/usr/bin/nice -n20 git commit -am "Update"
		/usr/bin/nice -n20 git push origin master
	fi
fi
END Github

DOMOTICZ=`curl -s --connect-timeout 2 --max-time 5 "http://127.0.0.1:8080/json.htm?type=devices&rid=1"`
STATUS=`echo $DOMOTICZ | jq -r '.status'`
if [ "$STATUS" == "OK" ] ; then
	exit
else
	sleep 20
	DOMOTICZ=`curl -s --connect-timeout 2 --max-time 5 "http://127.0.0.1:8080/json.htm?type=devices&rid=1"`
	STATUS2=`echo $DOMOTICZ | jq -r '.status'`
	if [ "$STATUS2" == "OK" ] ; then
		exit
	else
		sleep 20
		DOMOTICZ=`curl -s --connect-timeout 2 --max-time 5 "http://127.0.0.1:8080/json.htm?type=devices&rid=1"`
		STATUS3=`echo $DOMOTICZ | jq -r '.status'`
		if [ "$STATUS3" == "OK" ] ; then
			exit
		else
			/usr/sbin/service domoticz stop
			/usr/sbin/service domoticz start
		fi
	fi
fi