#!/bin/bash
wakeonlan -i 192.168.2.10 -p 9 00:11:32:2c:b7:21
wakeonlan -i 192.168.2.10 -p 9 00:11:32:2c:b7:22
wakeonlan -i 192.168.2.10 -p 9 00:11:32:2c:b7:23
wakeonlan -i 192.168.2.10 -p 1599 00:11:32:2c:b7:21
wakeonlan -i 192.168.2.10 -p 1599 00:11:32:2c:b7:22
wakeonlan -i 192.168.2.10 -p 1599 00:11:32:2c:b7:23
wakeonlan -i 192.168.2.255 -p 9 00:11:32:2c:b7:21
wakeonlan -i 192.168.2.255 -p 9 00:11:32:2c:b7:22
wakeonlan -i 192.168.2.255 -p 9 00:11:32:2c:b7:23
wakeonlan -i 192.168.2.255 -p 1599 00:11:32:2c:b7:21
wakeonlan -i 192.168.2.255 -p 1599 00:11:32:2c:b7:22
wakeonlan -i 192.168.2.255 -p 1599 00:11:32:2c:b7:23
echo 'wakeonlan' | tee -a /temp/domoticz.log