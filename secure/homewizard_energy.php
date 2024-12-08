<?php
$data=curl('http://192.168.2.4/api/v1/data');
$data=json_decode($data);
if (isset($data->total_power_import_kwh)) {
	$elec=$data->total_power_import_kwh;
	$injectie=$data->total_power_export_kwh;
	foreach ($data->external as $i) {
		if ($i->type=='gas_meter') $gas=$i->value;
		elseif ($i->type=='water_meter') $water=$i->value;

	}
	date_default_timezone_set('Europe/Brussels');
	$time=time();
	$vandaag=date("Y-m-d",$time);
	$gisteren=date("Y-m-d",$time-86400);

	$db=new mysqli('192.168.2.20','home','H0m€','egregius_zonphp');
	if($db->connect_errno>0){die('Unable to connect to database ['.$dbz->connect_error.']');}

	$query="SELECT Geg_Maand FROM `tgeg_maand` WHERE `Datum_Maand` = '$vandaag  0:00:00';";
//	echo $query.'<br>';
	if(!$result=$db->query($query))echo('There was an error running the query "'.$query.'" '.$db->error);
	while($row=$result->fetch_assoc())$zon=$row['Geg_Maand'];$result->free();
	if(!isset($zon)) $zon=0;
	echo 'Zon :		'.$zon.'<br>';

	$query="SELECT SUM(Geg_Maand) AS Geg_Maand FROM `tgeg_maand`;";
//	echo $query.'<br>';
	if(!$result=$db->query($query))echo('There was an error running the query "'.$query.'" '.$db->error);
	while($row=$result->fetch_assoc())$zontotaal=$row['Geg_Maand'];$result->free();
	echo 'Zon totaal:	'.$zontotaal.'<br>';
	$db->close();
	
	$db=new mysqli('192.168.2.20','home','H0m€','verbruik');
	if($db->connect_errno>0){die('Unable to connect to database ['.$db->connect_error.']');}

	$query="INSERT INTO `Guy` (`date`,`gas`,`elec`,`injectie`,`zon`,`water`) VALUES ('$vandaag','$gas','$elec','$injectie','$zontotaal','$water') ON DUPLICATE KEY UPDATE `gas`='$gas',`elec`='$elec',`injectie`='$injectie',`zon`='$zontotaal',`water`='$water'";
//	echo $query.'<br>';
	if(!$result=$db->query($query)){echo('There was an error running the query "'.$query.'" - '.$db->error);}

	$query="SELECT `date`,`gas`,`elec`,`injectie`,`water` FROM `Guy` ORDER BY `date` DESC LIMIT 1,1";
	echo $query.'<br>';
	if(!$result=$db->query($query))echo('There was an error running the query "'.$query.'" '.$db->error);
	while($row=$result->fetch_assoc())$gisteren=$row;$result->free();
	echo 'Gisteren=';print_r($gisteren);

	$gas=round($gas-$gisteren['gas'],3);
	$elec=$elec-$gisteren['elec'];
	$water=round($water-$gisteren['water'],3);
	$injectie=round($injectie-$gisteren['injectie'],3);
	$verbruik=round($zon-$injectie+$elec,3);
	$query="INSERT INTO `Guydag` (`date`,`gas`,`elec`,`verbruik`,`zon`,`water`) VALUES ('$vandaag','$gas','$elec','$verbruik','$zon','$water') ON DUPLICATE KEY UPDATE `gas`='$gas',`elec`='$elec',`verbruik`='$verbruik',`zon`='$zon',`water`='$water'";
//	echo $query.'<br>';
	if(!$result=$db->query($query)){echo('There was an error running the query "'.$query.'" - '.$db->error);}
	$db->close();
	
	$dbname='domotica';
	$dbuser='domotica';
	$dbpass='0untracked-mila5-1Lumbar-3confound-bereft8-opals-Allyn8-buyer-channel-Junction-1Haggler-marbles1-Nods-Honk-2Rico';

	$db=new PDO("mysql:host=127.0.0.1;dbname=$dbname;",$dbuser,$dbpass);
	$stmt=$db->query("select n,s,m from devices WHERE n IN ('watervandaag','gasvandaag','zonvandaag','el','zon');");
	while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
		$d[$row['n']]['s'] = $row['s'];
		$d[$row['n']]['m'] = $row['m'];
	}
	$water=$water*1000;
	$verbruik=round($verbruik, 1);
	if ($water!=$d['watervandaag']['s']) {
		echo 'Updating watervandaag'.'<br>';
		$db->query("UPDATE devices SET s=$water,t=$time WHERE n='watervandaag';");
	}
	if ($gas!=$d['gasvandaag']['s']) {
		echo 'Updating gasvandaag'.'<br>';
		$db->query("UPDATE devices SET s=$gas,t=$time WHERE n='gasvandaag';");
	}
	if ($zon!=$d['zonvandaag']['s']) {
		echo 'Updating zonvandaag'.'<br>';
		$db->query("UPDATE devices SET s=$zon,t=$time WHERE n='zonvandaag';");
	}
	if ($verbruik!=$d['el']['m']) {
		echo 'Updating el'.'<br>';
		$db->query("UPDATE devices SET m=$verbruik,t=$time WHERE n='el';");
	}
	echo '<br>'.'<br>';
	
	




}




function curl($url) {
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_FRESH_CONNECT,true);
	curl_setopt($ch,CURLOPT_TIMEOUT,5);
	$data=curl_exec($ch);
	curl_close($ch);
	return $data;
}