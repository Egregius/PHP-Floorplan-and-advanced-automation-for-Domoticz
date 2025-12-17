<?php
$uniq = substr(microtime(), 2,6);
exec("mosquitto_pub -h 192.168.2.22 -u mqtt -P mqtt -t 'kodi_last_action' -m '{$_GET['action']}-$uniq'");
echo 'ok';
