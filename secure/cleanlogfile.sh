#!/bin/bash
find /var/log -type f -mtime +5 -print -delete
exit 0
