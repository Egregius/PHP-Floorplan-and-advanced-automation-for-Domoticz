<?php
$uniq = substr(microtime(), 2, 6);
exec(
  "mosquitto_pub -h 192.168.2.22 -u mqtt -P mqtt ".
  "-t 'kodi/last_action' ".
  "-m '{$_GET['action']}-$uniq'"
);
echo 'ok';

/*
telegram('Kodi '.$_GET['action']);
function telegram($msg,$silent=true,$to=1) {
	if ($silent==true) $silent='true';
	else $silent='false';
	shell_exec('/var/www/html/secure/telegram.sh "'.$msg.'" "'.$silent.'" "'.$to.'" > /dev/null 2>/dev/null &');
}*/