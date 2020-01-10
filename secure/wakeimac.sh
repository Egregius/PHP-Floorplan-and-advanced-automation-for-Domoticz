#!/bin/sh
wakeonlan -i 192.168.2.50 -p 9 3c:07:54:22:34:17 | tee -a '/var/log/domoticz.log'
wakeonlan -i 192.168.2.51 -p 9 04:54:53:02:0a:34 | tee -a '/var/log/domoticz.log'
wakeonlan -i 192.168.2.255 -p 9 3c:07:54:22:34:17 | tee -a '/var/log/domoticz.log'
wakeonlan -i 192.168.2.255 -p 9 04:54:53:02:0a:34 | tee -a '/var/log/domoticz.log'

