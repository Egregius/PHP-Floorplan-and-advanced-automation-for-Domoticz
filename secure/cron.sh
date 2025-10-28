#!/bin/sh
PASS4MQTT=true
ENERGY=true
HOMEWIZARD=true
CRON=true
CRON2=true

i=1
while [ $i -lt 6 ]; do
	echo $i
	if [ $PASS4MQTT = true ] ;then
		ps cax | grep pass4mqtt.php
		if [ $? -ne 0 ] ; then
			/var/www/html/secure/pass4mqtt.php >/dev/null 2>&1 &
		fi
	fi
	if [ $ENERGY = true ] ;then
		ps cax | grep energy.php
		if [ $? -ne 0 ] ; then
			/var/www/html/secure/energy.php >/dev/null 2>&1 &
		fi
	fi	
	if [ $HOMEWIZARD = true ] ; then
		pgrep -f homewizard_mqtt.py >/dev/null
		if [ $? -ne 0 ]; then
			/var/www/html/secure/homewizard_mqtt.py >/dev/null 2>&1 &
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