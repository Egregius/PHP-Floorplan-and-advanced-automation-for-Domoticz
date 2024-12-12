<?php
//lg('CRON-3600');
if (!isset($db)) $db=dbconnect();
$d=fetchdata();
$time=time();
$user='cron3600';

if (date('G')==0) {
	mset('alwayson',9999);
}


$since=date("Y-m-d G:i:s", $time-86400);
foreach (array('01','02','03','04','06','07','08','09',11,12,13,14,16,17,18,19,21,22,23,24,26,27,28,29,31,32,33,34,36,37,38,39,41,42,43,44,46,47,48,49,51,52,53,54,56,57,58,59) as $x) {
	$query="DELETE FROM `temp` WHERE `stamp` LIKE '%:$x:00' AND `stamp` < '$since'";
	echo $query.PHP_EOL;
	$db->query($query);
}
	
$data=json_decode(file_get_contents('http://127.0.0.1:8080/json.htm?type=command&param=getdevices&rid=1'));
if (isset($data->CivTwilightStart)) {
	$CivTwilightStart=strtotime($data->CivTwilightStart);
	$CivTwilightEnd=strtotime($data->CivTwilightEnd);
	$Sunrise=strtotime($data->Sunrise);
	$Sunset=strtotime($data->Sunset);
	if ($time>=$CivTwilightStart&&$time<=$CivTwilightEnd) {
		$dag=1;
		if ($time>=$Sunrise&&$time<=$Sunset) {
			if ($time>=$Sunrise+900&&$time<=$Sunset-900) $dag=4;
			else $dag=3;
		} else {
			$zonop=($CivTwilightStart+$Sunrise)/2;
			$zononder=($CivTwilightEnd+$Sunset)/2;
			if ($time>=$zonop&&$time<=$zononder) $dag=2;
		}
	}
	mset('dag',$dag);
	mset('CivTwilightStart', date('G:i', $CivTwilightStart));
} else lg('Error fetching CivTwilightStart from domoticz');


/* Clean old database records */

$remove=date('Y-m-d H:i:s', $time-(86400*100));
$stmt=$db->query("delete from temp where stamp < '$remove'");
