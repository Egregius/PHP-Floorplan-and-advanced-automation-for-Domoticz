#!/bin/sh
action=$1
curl -s "http://192.168.2.11/telegram.php?action=$action" > /dev/null 2>/dev/null &
curl -s "http://192.168.2.13/telegram.php?action=$action" > /dev/null 2>/dev/null &
curl -s "http://192.168.2.11/fifo_command.php?cmd=record%20on%2015%2055" > /dev/null 2>/dev/null &
curl -s "http://192.168.2.13/fifo_command.php?cmd=record%20on%2015%2055" > /dev/null 2>/dev/null &