#!/bin/sh
PASS4MQTT=true
MQTTREPUBLISHDOMOTICZ=false
ENERGY=true
CRON=true
CRON2=true
MQTTTEST=true
i=1
while [ $i -lt 6 ]; do
	echo $i
	if [ $PASS4MQTT = true ] ;then
		ps cax | grep pass4mqtt.php
		if [ $? -ne 0 ] ; then
			/var/www/html/secure/pass4mqtt.php >/dev/null 2>&1 &
		fi
	fi
	if [ $MQTTREPUBLISHDOMOTICZ = true ] ;then
		ps cax | grep mqttrepublishdo
		if [ $? -ne 0 ] ; then
			/var/www/html/secure/mqttrepublishdomoticz.php >/dev/null 2>&1 &
		fi
	fi
	if [ $ENERGY = true ] ;then
		ps cax | grep energy.php
		if [ $? -ne 0 ] ; then
			/var/www/html/secure/energy.php >/dev/null 2>&1 &
		fi
	fi	
	if [ $CRON = true ] ;then
		ps cax | grep cron.php
		if [ $? -ne 0 ] ; then
			/var/www/html/secure/cron.php >/dev/null 2>&1 &
		fi
	fi
	if [ $CRON2 = true ] ;then
		ps cax | grep cron2.php
		if [ $? -ne 0 ] ; then
			/var/www/html/secure/cron2.php >/dev/null 2>&1 &
		fi
	fi
	if [ $MQTTTEST = true ] ;then
		ps cax | grep mqttsleep5.php
		if [ $? -ne 0 ] ; then
			/var/www/mqttsleep5.php >/dev/null 2>&1 &
		fi
		ps cax | grep mqttsleep2.php
		if [ $? -ne 0 ] ; then
			/var/www/mqttsleep2.php >/dev/null 2>&1 &
		fi
		ps cax | grep mqttsleep.php
		if [ $? -ne 0 ] ; then
			/var/www/mqttsleep.php >/dev/null 2>&1 &
		fi
		ps cax | grep mqttfalse.php
		if [ $? -ne 0 ] ; then
			/var/www/mqttfalse.php >/dev/null 2>&1 &
		fi
		ps cax | grep mqtt1.php
		if [ $? -ne 0 ] ; then
			/var/www/mqtt1.php >/dev/null 2>&1 &
		fi
		ps cax | grep mqtt10.php
		if [ $? -ne 0 ] ; then
			/var/www/mqtt10.php >/dev/null 2>&1 &
		fi
		ps cax | grep mqtt100.php
		if [ $? -ne 0 ] ; then
			/var/www/mqtt100.php >/dev/null 2>&1 &
		fi
		ps cax | grep mqtt1000.php
		if [ $? -ne 0 ] ; then
			/var/www/mqtt1000.php >/dev/null 2>&1 &
		fi
		ps cax | grep mqtt10m.php
		if [ $? -ne 0 ] ; then
			/var/www/mqtt10m.php >/dev/null 2>&1 &
		fi
		ps cax | grep mqtt100m.php
		if [ $? -ne 0 ] ; then
			/var/www/mqtt100m.php >/dev/null 2>&1 &
		fi
		ps cax | grep mqtt1000m.php
		if [ $? -ne 0 ] ; then
			/var/www/mqtt1000m.php >/dev/null 2>&1 &
		fi
	fi
	sleep 10
	i=`expr $i + 1`
done

ps cax | grep nginx
if [ $? -ne 0 ] ; then
	/usr/sbin/service nginx stop
	/usr/sbin/service nginx start
fi
PHP=$(ps -C php-fpm8.0 | wc -l)
if [ $PHP -le 1 ] || [ $PHP -ge 50 ] ; then
	/usr/sbin/service php8.0-fpm stop
	/usr/sbin/service php8.0-fpm start
fi
ps cax | grep mariadbd
if [ $? -ne 0 ] ; then
	/usr/sbin/service mysql stop
	/usr/sbin/service mysql start
fi

# Remove these lines as they only upload my files to gitbub.
MINUTE=$(date +"%M")
if [ "$MINUTE" -eq 0 ] ; then
	LAST=$(find /var/www/html -type f ! -name '_*' ! -path "*/stills/*" ! -path "*/sounds/*" ! -path "*/.git/*" ! -path "*/.github/*" ! -path "*/pass2php/*" ! -path "*/phpMyAdmin/*" ! -path "*/google-api-php-client/*" ! -path "*/archive/*" -printf '%T@\n' | sort -n | tail -1 | cut -f1- -d" ")
	PREV=$(cat "/temp/timestampappcache.txt")
	echo $LAST>"/temp/timestampappcache.txt"
	if [ "$LAST" != "$PREV" ]
	then
		cd /var/www/html/
		/usr/bin/nice -n20 git add .
		/usr/bin/nice -n20 git commit -am "Update"
		/usr/bin/nice -n20 git push origin master --force
	fi
fi
# END Github
#exit

DOMOTICZ=`curl -s --connect-timeout 2 --max-time 5 "http://127.0.0.1:8080/json.htm?type=command&param=getdevices&rid=1"`
STATUS=`echo $DOMOTICZ | jq -r '.status'`
if [ $STATUS = "OK" ] ; then
	exit
else
	sleep 20
	DOMOTICZ=`curl -s --connect-timeout 2 --max-time 5 "http://127.0.0.1:8080/json.htm?type=command&param=getdevices&rid=1"`
	STATUS2=`echo $DOMOTICZ | jq -r '.status'`
	if [ $STATUS2 = "OK" ] ; then
		exit
	else
		sleep 20
		DOMOTICZ=`curl -s --connect-timeout 2 --max-time 5 "http://127.0.0.1:8080/json.htm?type=command&param=getdevices&rid=1"`
		STATUS3=`echo $DOMOTICZ | jq -r '.status'`
		if [ $STATUS3 = "OK" ] ; then
			exit
		else
			/usr/sbin/service domoticz stop
			/usr/sbin/service domoticz start
		fi
	fi
fi
