#!/bin/bash
ps cax | grep nginx
if [ $? -ne 0 ] ; then
	/usr/sbin/service nginx stop
	/usr/sbin/service nginx start
fi
PHP=$(ps -C php-fpm8.4 | wc -l)
if [ $PHP -le 1 ] || [ $PHP -ge 50 ] ; then
	/usr/sbin/service php8.4-fpm stop
	/usr/sbin/service php8.4-fpm start
fi

# Remove these lines as they only upload my files to gitbub.
MIN=$(date +%-M)
if [ $((MIN % 30)) -eq 0 ]; then
	LAST=$(find /var/www/html -type f ! -name '_*' ! -path "*/stills/*" ! -path "*/sounds/*" ! -path "*/.git/*" ! -path "*/.github/*" ! -path "*/pass2php/*" ! -path "*/phpMyAdmin/*" ! -path "*/google-api-php-client/*" ! -path "*/archive/*" -printf '%T@\n' | sort -n | tail -1 | cut -f1- -d" ")
	PREV=$(cat "/temp/timestampappcache.txt")
	echo $LAST>"/temp/timestampappcache.txt"
	if [ "$LAST" != "$PREV" ]
	then
#		cd /var/www/html/scripts/
#		gzip -k -d floorplanjs.js.gz -f
		cd /var/www/html/
		/usr/bin/nice -n 10 git add .
		/usr/bin/nice -n 10 git commit -am "Update"
		/usr/bin/nice -n 10 git push origin master --force
	fi
fi
# END Github
#exit

#systemctl enable --now domo-php@mqtt_sensor_switch