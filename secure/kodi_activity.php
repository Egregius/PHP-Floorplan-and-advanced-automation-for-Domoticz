<?php
$action = isset($_GET['action']) ? $_GET['action'] : 'unknown';
exec("mosquitto_pub -h 192.168.2.26 -u mqtt -P mqtt -t 'homeassistant/media_player/kodi_last_action/state' -m '$action'");
lg("kodi ping: $action");
echo 'ok';
function lg($msg,$level=0) {
/*
Levels:
0:	Default / Undefined
1:	Loop starts
2:	
3:	
4:	Switch commands
5:	Setpoints
6:	OwnTracks
7:	
8:	Update kWh devices
9:	Update temperatures
10: Store/Storemode
99:	SQL Fetchdata
*/
	static $inLg = false;
	if ($inLg) return; // voorkomt recursie

	$inLg = true;
	global $d;
	if (isset($d['auto']['m'])) {
		$loglevel = $d['auto']['m'];
	} else $loglevel = 0;

	if ($level <= $loglevel) {
		$fp = fopen('/temp/domoticz.log', "a+");
		$time = microtime(true);
		$dFormat = "Y-m-d H:i:s";
		$mSecs = $time - floor($time);
		$mSecs = substr(number_format($mSecs, 3), 1);
		fwrite($fp, sprintf("%s%s %s\n", date($dFormat), $mSecs, $msg));
		fclose($fp);
	}

	$inLg = false;
}
?>
