<?php
//lg('CRON-3600');
if (!isset($db)) $db=dbconnect();
$d=fetchdata();
$time=time();
$user='cron3600';

if (date('G')==0) {
	if ($d['winst']['s']!=0) store ('winst', 0);
}


$since=date("Y-m-d G:i:s", $time-86400);
foreach (array('01','02','03','04','06','07','08','09',11,12,13,14,16,17,18,19,21,22,23,24,26,27,28,29,31,32,33,34,36,37,38,39,41,42,43,44,46,47,48,49,51,52,53,54,56,57,58,59) as $x) {
	$query="DELETE FROM `temp` WHERE `stamp` LIKE '%:$x:00' AND `stamp` < '$since'";
	echo $query.PHP_EOL;
	$db->query($query);
}
	
$data=json_decode(file_get_contents('http://127.0.0.1:8080/json.htm?type=command&param=getdevices&rid=1'), true);
if (isset($data['CivTwilightStart'])) {
	$name='civil_twilight';
	$status=strtotime($data['CivTwilightStart']);
	$mode=strtotime($data['CivTwilightEnd']);
	$db->query("INSERT INTO devices (n,s,m,t) VALUES ('$name','$status','$mode','$time') ON DUPLICATE KEY UPDATE s='$status', m='$mode', t='$time';");
	$name='Sun';
	$status=strtotime($data['Sunrise']);
	$mode=strtotime($data['Sunset']);
	$icon=strtotime($data['SunAtSouth']);
	$db->query("INSERT INTO devices (n,s,m,icon,t) VALUES ('$name', '$status', '$mode', '$icon', '$time') ON DUPLICATE KEY UPDATE s='$status', m='$mode', icon='$icon', t='$time';");
} else lg('Error fetching CivTwilightStart from domoticz');

/* Clean old database records */

$remove=date('Y-m-d H:i:s', $time-(86400*100));
$stmt=$db->query("delete from temp where stamp < '$remove'");
