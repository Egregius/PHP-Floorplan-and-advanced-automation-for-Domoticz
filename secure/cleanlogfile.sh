#!/bin/bash
tail -n 5000 /temp/domoticz.log > /temp/domoticz.tmp && mv /temp/domoticz.tmp /temp/domoticz.log
chmod 766 /temp/domoticz.log