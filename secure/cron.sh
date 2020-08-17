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

DOMOTICZ=`curl -s --connect-timeout 2 --max-time 5 "http://127.0.0.1:8080/json.htm?type=devices&rid=1"`
STATUS=`echo $DOMOTICZ | jq -r '.status'`
if [ "$STATUS" == "OK" ] ; then
    NOW=$(date +"%Y-%m-%d %H:%M:%S")
    MINUTE=$(date +"%M")
    CRON=""
    if [ $(($MINUTE%2)) -eq 0 ] ; then
        CRON="$CRON&cron120"
    fi
    if [ $(($MINUTE%3)) -eq 0 ] ; then
        CRON="$CRON&cron180"
    fi
    if [ $(($MINUTE%4)) -eq 0 ] ; then
        CRON="$CRON&cron240"
    fi
    if [ $(($MINUTE%5)) -eq 0 ] ; then
        CRON="$CRON&cron300"
    fi
    if [ $MINUTE -eq 0 ] ; then
        CRON="$CRON&cron3600"
    fi
	#0
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/cron.php?cron60&cron10$CRON" >/dev/null 2>&1 &
	sleep 8.867
	#10
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/cron.php?cron10" >/dev/null 2>&1 &
	sleep 9.998
	#20
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/cron.php?cron10" >/dev/null 2>&1 &
	sleep 9.998
	#30
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/cron.php?cron10" >/dev/null 2>&1 &
	sleep 9.998
	#40
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/cron.php?cron10" >/dev/null 2>&1 &
	sleep 9.998
	#50
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/cron.php?cron10"
	if [ $? -gt 0 ] ; then
		/usr/sbin/service apache2 restart
	fi
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
			#shutdown -r now
		fi
	fi
fi

# Extra check that apache2 is running.
ps cax | grep apache2
if [ $? -ne 0 ] ; then
	/usr/sbin/service apache2 stop
	/usr/sbin/service apache2 start
fi

ps cax | grep mysql
if [ $? -ne 0 ] ; then
	/usr/sbin/service mysql stop
	/usr/sbin/service mysql start
fi

# Remove these lines as they only upload my files to gitbub.
if [ $(($MINUTE%5)) -eq 0 ] ; then
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