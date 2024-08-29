<?php
$db = new SQLite3('/domoticz/domoticz.db');
$time=time();
$clean = date("Y-m-d H:i:s", $time-3600*24*7);
$tables = array( 'LightingLog',
	'MultiMeter',
	'MultiMeter_Calendar',
	'Meter',
	'Meter_Calendar',
	'Percentage',
	'Percentage_Calendar',
	'Rain',
	'Rain_Calendar',
	'Temperature',
	'Temperature_Calendar',
	'UV',
	'UV_Calendar',
	'Wind',
	'Wind_Calendar');
echo PHP_EOL;
foreach ($tables as $table) {
	$query=$db->exec("DELETE FROM $table WHERE DeviceRowID not in (select ID from DeviceStatus where Used = 1)");
	if ($query) {
		$rows = $db->changes();
		if ($rows>0) {
			echo $rows." rows removed from $table".PHP_EOL;
			sleep(1);
		}
	}
/*
	$query=$db->exec("DELETE FROM $table WHERE Date < '$clean'");
	if ($query) {
		$rows = $db->changes();
		if ($rows>0) {
			echo $rows." rows removed from $table".PHP_EOL;
			sleep(1);
		}
	}
*/
}

$query=$db->exec("DELETE FROM LightingLog WHERE DeviceRowID in (6,24,170,171,172,173,174,175,176,177,295,296,297,298,299,300,301,302)");
if ($query) {
	$rows = $db->changes();
	if ($rows>0) {
		echo $rows." rows removed from LightingLog".PHP_EOL;
		sleep(1);
	}
}

$query=$db->exec("DELETE FROM LightingLog WHERE Date < '$clean'");
if ($query) {
	$rows = $db->changes();
	if ($rows>0) {
		echo $rows." old rows removed from LightingLog".PHP_EOL;
		sleep(1);
	}
}
$sql = 'VACUUM;';
if(!$result = $db->exec($sql))
die('There was an error running the query [' . $db->error . ']');
?>
