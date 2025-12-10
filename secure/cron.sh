#!/bin/bash
cd /var/www/html/secure

SCRIPTS=(
  mqtt_binary_sensor.php
  mqtt_cover.php
  mqtt_event.php
  mqtt_light.php
  mqtt_media_player.php
  mqtt_sensor.php
  mqtt_switch.php
  mqtt_zigbee2mqtt.php
#  mqtt_zwave2mqtt.php
  cron.php
  cron2.php
  energy.php
  homewizard_tmpfs.py
)

i=1
while [ $i -lt 6 ]; do
	echo $i
	for s in "${SCRIPTS[@]}"; do
		if ! pgrep -f "[${s:0:1}]${s:1}" >/dev/null; then
		echo "$(date '+%F %T') starting $s"
		case "$s" in
			*.php) /var/www/html/secure/$s >/dev/null 2>&1 & ;;
			*.py)  /usr/bin/python3 "$s" >/dev/null 2>&1 & ;;
		esac
	fi
	done
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
#ps cax | grep mariadbd
#if [ $? -ne 0 ] ; then
#	/usr/sbin/service mysql stop
#	/usr/sbin/service mysql start
#fi
MIN=$(date +%M)



# Remove these lines as they only upload my files to gitbub.
if [ "$MIN" -eq 0 ] ; then
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