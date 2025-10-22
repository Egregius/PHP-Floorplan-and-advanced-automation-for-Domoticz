<?php
//$action = ucfirst(strtolower($_GET['action']));
$uniq = substr(microtime(), 2,6);
exec("mosquitto_pub -h 192.168.2.26 -u mqtt -P mqtt -t 'kodi_last_action' -m '{$_GET['action']}-$uniq'");
echo 'ok';
//exit;
lg("'{$_GET['action']}'");
function lg($msg,$level=0) {
	$fp = fopen('/temp/kodi_activity.log', "a+");
	fwrite($fp, sprintf("%s\n", $msg));
	fclose($fp);
}
?>
