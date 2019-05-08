#!/bin/bash

DIRECTORY="/var/www/html/picam1/archive"
CAPACITY=80
while [[ $(df $DIRECTORY | awk 'NR==2 && gsub("%","") {print$5}') -ge $CAPACITY ]];do
        rm -rf $(find $DIRECTORY -mindepth 1 -printf '%T+ %p\n' | sort | awk 'NR==1 {print$2}')
done
