<?php
//lg('CRON-3600');
$user=basename(__FILE__);

if (date('G')==0) {
	mset('alwayson',9999);
	store('gasvandaag', 0, basename(__FILE__).':'.__LINE__);
	store('watervandaag', 0, basename(__FILE__).':'.__LINE__);
}


$since=date("Y-m-d G:i:s", $time-86400);
foreach (array('01','02','03','04','06','07','08','09',11,12,13,14,16,17,18,19,21,22,23,24,26,27,28,29,31,32,33,34,36,37,38,39,41,42,43,44,46,47,48,49,51,52,53,54,56,57,58,59) as $x) {
	$query="DELETE FROM `temp` WHERE `stamp` LIKE '%:$x:00' AND `stamp` < '$since'";
	echo $query.PHP_EOL;
	$db->query($query);
}

/* Clean old database records */

$remove=date('Y-m-d H:i:s', $time-(86400*100));
$stmt=$db->query("delete from temp where stamp < '$remove'");




@$data=json_decode(file_get_contents('http://127.0.0.1:8080/json.htm?type=command&param=getdevices&rid=1'));
if (isset($data->CivTwilightStart)) {
	$CivTwilightStart=strtotime($data->CivTwilightStart);
	$CivTwilightEnd=strtotime($data->CivTwilightEnd);
	$Sunrise=strtotime($data->Sunrise);
	$Sunset=strtotime($data->Sunset);
	mset('sunrise', array(
		'CivTwilightStart'=>date('G:i', $CivTwilightStart),
		'CivTwilightEnd'=>date('G:i', $CivTwilightEnd),
		'Sunrise'=>date('G:i', $Sunrise),
		'Sunset'=>date('G:i', $Sunset),
	));
}// else lg('Error fetching CivTwilightStart from domoticz');
