#!/bin/bash
curl -d "<play_info><app_key>UJvfKvnMPgzK6oc7tTE1QpAVcOqp4BAY</app_key><url>http://192.168.2.2/sounds/$1.mp3</url><service>%1</service><reason>$1</reason><message>$1</message><volume>50</volume></play_info>" http://192.168.2.101:8090/speaker