#!/bin/bash
rsync -aP -e "ssh -i /root/.ssh/home -p 1598" --stats --remove-source-files "root@mail.egregius.be:/btsync/nzb/" "/tmp"
#sudo /var/packages/sabnzbd-testing/scripts/start-stop-status start
sleep 1
TIME=$(date +"%s")

curl -s "http://127.0.0.1:1601/tapi?mode=watched_now&output=json&apikey=ead6d0482e96d6d677a7c842d47590f2&_=$TIME"