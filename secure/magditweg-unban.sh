#!/bin/bash
for x in floorplan recidive; do
	echo fail2ban-client set $x unbanip $1
	fail2ban-client set $x unbanip $1
done

