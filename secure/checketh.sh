#!/bin/bash
sudo ping -c1 192.168.2.1 > /dev/null
if [ $? != 0 ]
then
  echo "No network connection, restarting wlan0"
  sudo /sbin/ifdown 'enxb827eb2899fe'
  sleep 2
  sudo /sbin/ifup --force 'enxb827eb2899fe'
  sleep 2
  curl -s --data-urlencode "text=PiCam3 ETH Restored" --data "silent=true" http://192.168.2.2/secure/telegram.php
fi
