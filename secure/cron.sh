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
	echo $NOW   $MINUTE $CRON >> /run/cronlog
	echo OK
	#0
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/cron.php?cron10&verwarming&rolluiken&cron60$CRON" >/dev/null 2>&1 &
	sleep 8.859
	#10
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/cron.php?cron10" >/dev/null 2>&1 &
	sleep 9.998
	#20
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/cron.php?cron10&verwarming&rolluiken" >/dev/null 2>&1 &
	sleep 9.998
	#30
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/cron.php?cron10" >/dev/null 2>&1 &
	sleep 9.998
	#40
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/cron.php?cron10&verwarming&rolluiken" >/dev/null 2>&1 &
	sleep 9.998
	#50
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/cron.php?cron10"
	if [ $? -gt 0 ] ; then
		service apache2 restart
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
			shutdown -r now
		fi
	fi
fi

ps cax | grep httpd
if [ $? -eq 0 ] ; then
	service apache2 restart
fi


if [ $(($MINUTE%5)) -eq 0 ] ; then
#	LAST=$(find /var/www/html -type f ! -name 'floorplan.appcache' ! -name '_*' ! -path "*/stills/*" ! -path "*/.git/*" ! -path "*/.github/*" ! -path "*/pass2php/*" ! -path "*/phpMyAdmin/*" ! -path "*/google-api-php-client/*" ! -path "*/archive/*" -printf '%T@\n' | sort -n | tail -1 | cut -f1- -d" ")
#	PREV=$(cat "/temp/timestampappcache.txt")
#	echo $LAST>"/temp/timestampappcache.txt"
#	if [ "$LAST" != "$PREV" ]
#	then
#		awk -v timestamp=$(date +%s) 'NR == 2 { $2 = timestamp } 1' /var/www/html/floorplan.appcache > /temp/floorplan.appcache
#		mv /temp/floorplan.appcache /var/www/html/floorplan.appcache
#	fi
	
	LAST=$(find /var/www/html -type f ! -name 'floorplan.appcache' ! -path "*/stills/*" ! -path "*/.git/*" ! -path "*/.github/*" ! -path "*/phpMyAdmin/*" ! -path "*/google-api-php-client/*" ! -path "*/archive/*" -printf '%T@\n' | sort -n | tail -1 | cut -f1- -d" ")
	PREV=$(cat "/temp/timestampgithub.txt")
	echo $LAST>"/temp/timestampgithub.txt"
	if [ "$LAST" != "$PREV" ]
	then
		cd /var/www/html/
		/usr/bin/nice -n20 git add .
		/usr/bin/nice -n20 git commit -am "Update"
		/usr/bin/nice -n20 git push origin master
	fi
fi