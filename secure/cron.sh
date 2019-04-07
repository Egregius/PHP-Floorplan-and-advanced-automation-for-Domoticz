#!/bin/bash
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
			shutdown -r now
		fi
	fi
fi
