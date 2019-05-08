#!/bin/bash

: '
crontab -e
# m h  dom mon dow   command
* * * * * /usr/bin/nice -n20 /var/www/html/secure/cron.sh >/dev/null 2>&1
*/2 * * * * /usr/bin/nice -n20 curl -s "http://127.0.0.1/secure/cron.php?cron120" >/dev/null 2>&1
0 * * * * /usr/bin/nice -n20 curl -s "http://127.0.0.1/secure/cron.php?cron3600" >/dev/null 2>&1
0 0 * * * /usr/bin/nice -n20 curl -s "http://127.0.0.1/secure/cleandomoticzdb.php" >/dev/null 2>&1
1 0 * * * /usr/bin/nice -n20 /var/www/_SQLBackup/sqldaily.sh >dev/null 2>&1
'

DOMOTICZ=`curl -s --connect-timeout 2 --max-time 5 "http://127.0.0.1:8080/json.htm?type=devices&rid=1"`
STATUS=`echo $DOMOTICZ | jq -r '.status'`
if [ "$STATUS" == "OK" ] ; then
	echo OK
	#0
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/cron.php?rolluiken&cron60&cron10" >/dev/null 2>&1 &
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/smappeepower.php" >/dev/null 2>&1 &
	sleep 3.609
	#5
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/smappeepower.php" >/dev/null 2>&1 &
	sleep 4.999
	#10
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/cron.php?cron10" >/dev/null 2>&1 &
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/smappeepower.php" >/dev/null 2>&1 &
	sleep 4.998
	#15
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/smappeepower.php" >/dev/null 2>&1 &
	sleep 4.999
	#20
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/cron.php?cron10" >/dev/null 2>&1 &
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/smappeepower.php" >/dev/null 2>&1 &
	sleep 4.998
	#25
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/smappeepower.php" >/dev/null 2>&1 &
	sleep 4.999
	#30
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/cron.php?cron10&verwarming" >/dev/null 2>&1 &
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/smappeepower.php" >/dev/null 2>&1 &
	sleep 4.998
	#35
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/smappeepower.php" >/dev/null 2>&1 &
	sleep 4.999
	#40
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/cron.php?cron10" >/dev/null 2>&1 &
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/smappeepower.php" >/dev/null 2>&1 &
	sleep 4.998
	#45
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/smappeepower.php" >/dev/null 2>&1 &
	sleep 4.999
	#50
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/cron.php?cron10" >/dev/null 2>&1 &
	curl -s --connect-timeout 2 --max-time 30 "http://127.0.0.1/secure/smappeepower.php" >/dev/null 2>&1 &
	sleep 4.998
	#55
	curl -s --connect-timeout 2 "http://127.0.0.1/secure/smappeepower.php" >/dev/null 2>&1 &
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
echo			shutdown -r now
		fi
	fi
fi
