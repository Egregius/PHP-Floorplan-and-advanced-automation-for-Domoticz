#!/bin/sh
ssh guy@diskstation -p 1598 -i /home/www-data/.ssh/id_rsa "sudo shutdown -P now"
