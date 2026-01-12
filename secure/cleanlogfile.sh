#!/bin/bash
tail -n 5000 /temp/domoticz.log > /temp/domoticz.tmp && mv /temp/domoticz.tmp /temp/domoticz.log
chmod 766 /temp/domoticz.log
exit 0
tail -n 500 /temp/floorplan-access.log > /temp/floorplan-access.tmp && mv /temp/floorplan-access.tmp /temp/floorplan-access.log
chmod 766 /temp/floorplan-access.log