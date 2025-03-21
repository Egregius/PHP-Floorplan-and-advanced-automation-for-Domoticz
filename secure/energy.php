#!/usr/bin/php
<?php
require '/var/www/vendor/autoload.php';
require '/var/www/html/secure/functions.php';
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
$mqtt=new MqttClient('127.0.0.1',1883,'php_mqtt_ws_'.rand());
$connectionSettings=(new ConnectionSettings())
	->setKeepAliveInterval(60)
	->setUseTls(false);
$mqtt->connect($connectionSettings, true);

$prevtotal=0;
$prevavg=0;
$dbverbruik=new mysqli('192.168.2.20','home','H0m€','verbruik');
if($dbverbruik->connect_errno>0){die('Unable to connect to database ['.$dbverbruik->connect_error.']');}

$query="SELECT MAX(wH) AS wH FROM `kwartierpiek` where date like '".date('Y-m')."-%';";
if(!$result=$dbverbruik->query($query))echo('There was an error running the query "'.$query.'" '.$dbverbruik->error);
while($row=$result->fetch_assoc())$kwartierpiek=$row['wH'];$result->free();
if (!isset($kwartierpiek)) $kwartierpiek=2500;
echo 'Kwartierpiek = '.$kwartierpiek.PHP_EOL;
$x=1;
while (1){
	$start = microtime(true);
	$dag=mget('dag');
	$en=mget('en');
	$data=curl('http://192.168.2.4/api/v1/data');
	if ($dag>2) {
		$zon=curl('http://192.168.2.9/api/v1/data');
		$zon=json_decode($zon);
		if (isset($zon->active_power_w)&&$zon->active_power_w<0) {
//			$prevzon=mget('zon');
			$newzon=-round($zon->active_power_w);
		}
	} else {
//		$prevzon=mget('zon');
		$newzon=0;
	}
	$data=json_decode($data);
	if (isset($data->total_power_import_kwh)) {
		$en=array(
			'net'=>$data->active_power_w,
			'avg'=>$data->active_power_average_w,
			'zon'=>$newzon
		);
		mset('en',$en);
		$mqtt->publish('i/en', json_encode($en), 0, true);
		$total=(int)(($data->total_power_import_kwh*100)+($data->total_power_export_kwh*100)+($data->total_gas_m3*1000));
		if ($data->active_power_w>8500) alert('Power', 'Power usage: '.$data->active_power_w.' W!', 600, false);

		if ($newzon==0) {
			$power=$data->active_power_w;
			$alwayson=mget('alwayson');
			if ($power>=50&&($power<$alwayson||empty($alwayson))) {
				if (!isset($db)) $db=dbconnect(basename(__FILE__).':'.__LINE__);
				mset('alwayson',$power);
				$time=time();
				$db->query("UPDATE devices SET icon=$power,t=$time WHERE n='elvandaag';");
				lg('New alwayson '.$power.' W');
				$vandaag=date("Y-m-d",$time);
				if (!isset($dbverbruik)) {
					$dbverbruik=new mysqli('192.168.2.20','home','H0m€','verbruik');
					if($dbverbruik->connect_errno>0){die('Unable to connect to database ['.$dbverbruik->connect_error.']');}
				}
				$query="INSERT INTO `alwayson` (`date`,`w`) VALUES ('$vandaag','$alwayson') ON DUPLICATE KEY UPDATE `w`='$alwayson'";
				if(!$result=$dbverbruik->query($query)){echo('There was an error running the query "'.$query.'" - '.$dbverbruik->error);}
			}
		}
		$sec=date('s');
		$min=date('i');
		$uur=date('G');
		$newavg=$data->active_power_average_w;
		if ($prevavg>2300) {
			if ($newavg>$kwartierpiek-200) alert('Kwartierpiek', 'Kwartierpiek momenteel al '.$newavg.' Wh!'.PHP_EOL.PHP_EOL.'Piek deze maand = '.$kwartierpiek.' wH', 120, false);
			echo $x.'	'.date('Y-m-d H:i:s').' prev='.$prevavg.' new='.$newavg.PHP_EOL;
			if ($newavg<$prevavg&&$prevavg>2500) { // Nieuw kwartier
				echo $x.'	'.__LINE__.PHP_EOL;
				if (!isset($dbverbruik)) {
					echo $x.'	'.__LINE__.PHP_EOL;
					$dbverbruik=new mysqli('192.168.2.20','home','H0m€','verbruik');
					if($dbverbruik->connect_errno>0){
						echo $x.'	'.__LINE__.PHP_EOL;
						die('Unable to connect to database ['.$dbverbruik->connect_error.']');
					}
				}
				echo $x.'	'.__LINE__.PHP_EOL;
				$query="INSERT INTO `kwartierpiek` (`date`,`wh`) VALUES ('".date('Y-m-d H:i:s')."','".$prevavg."')";
				if(!$result=$dbverbruik->query($query))echo('There was an error running the query "'.$query.'" - '.$dbverbruik->error);
				echo $x.'	'.__LINE__.PHP_EOL;
				if ($prevavg>$kwartierpiek-200) {
					alert('KartierpiekB', 'Kwartierpiek = '.$prevavg.' Wh'.PHP_EOL.PHP_EOL.'Piek deze maand = '.$kwartierpiek.' wH', 30, false);
					$kwartierpiek=$prevavg;
				}
			}
		}
//		if ($newavg<$prevavg) telegram (date('Y-m-d H:i:s').PHP_EOL.'prev='.$prevavg.PHP_EOL.'new='.$newavg);
		$prevavg=$newavg;
		// Updating verbruik database
		if ($total!=$prevtotal) {
			$elec=$data->total_power_import_kwh;
			$injectie=$data->total_power_export_kwh;
			foreach ($data->external as $i) {
				if ($i->type=='gas_meter') $gas=$i->value;
				elseif ($i->type=='water_meter'){
					$prevwater=mget('water_meter');
					$water=$i->value;
					if ($prevwater!=$water&&mget('Weg')>2) {
						mset('water_meter',$water);
						alert('water_meter', 'Water verbruik gededecteerd!', 300, true);
						lg('Waterteller:	prev='.$prevwater.', nu='.$water);
					}
				}
			}
			
			$time=time();
			$vandaag=date("Y-m-d",$time);
			$gisteren=date("Y-m-d",$time-86400);
			if (!isset($dbzonphp)) {
				$dbzonphp=new mysqli('192.168.2.20','home','H0m€','egregius_zonphp');
				if($dbzonphp->connect_errno>0){die('Unable to connect to database ['.$dbzonphp->connect_error.']');}
			}		
			$query="SELECT Geg_Maand FROM `tgeg_maand` WHERE `Datum_Maand` = '$vandaag  0:00:00';";
			if(!$result=$dbzonphp->query($query))echo('There was an error running the query "'.$query.'" '.$dbzonphp->error);
			while($row=$result->fetch_assoc())$zonvandaag=$row['Geg_Maand'];$result->free();
			if(!isset($zonvandaag)) $zonvandaag=0;
		
			$query="SELECT SUM(Geg_Maand) AS Geg_Maand FROM `tgeg_maand`;";
			if(!$result=$dbzonphp->query($query))echo('There was an error running the query "'.$query.'" '.$dbzonphp->error);
			while($row=$result->fetch_assoc())$zontotaal=$row['Geg_Maand'];$result->free();
			if (!isset($dbverbruik)) {
				$dbverbruik=new mysqli('192.168.2.20','home','H0m€','verbruik');
				if($dbverbruik->connect_errno>0){die('Unable to connect to database ['.$dbverbruik->connect_error.']');}
			}		
			$query="INSERT INTO `Guy` (`date`,`gas`,`elec`,`injectie`,`zon`,`water`) VALUES ('$vandaag','$gas','$elec','$injectie','$zontotaal','$water') ON DUPLICATE KEY UPDATE `gas`='$gas',`elec`='$elec',`injectie`='$injectie',`zon`='$zontotaal',`water`='$water'";
			if(!$result=$dbverbruik->query($query)){echo('There was an error running the query "'.$query.'" - '.$dbverbruik->error);}
		
			$query="SELECT `date`,`gas`,`elec`,`injectie`,`water` FROM `Guy` ORDER BY `date` DESC LIMIT 1,1";
			if(!$result=$dbverbruik->query($query))echo('There was an error running the query "'.$query.'" '.$dbverbruik->error);
			while($row=$result->fetch_assoc())$gisteren=$row;$result->free();
		
			$gas=round($gas-$gisteren['gas'],3);
			$elec=$elec-$gisteren['elec'];
			$water=round($water-$gisteren['water'],3);
			$injectie=round($injectie-$gisteren['injectie'],3);
			$verbruik=round($zonvandaag-$injectie+$elec,3);
			$query="INSERT INTO `Guydag` (`date`,`gas`,`elec`,`verbruik`,`zon`,`water`) VALUES ('$vandaag','$gas','$elec','$verbruik','$zonvandaag','$water') ON DUPLICATE KEY UPDATE `gas`='$gas',`elec`='$elec',`verbruik`='$verbruik',`zon`='$zonvandaag',`water`='$water'";
			if(!$result=$dbverbruik->query($query)){echo('There was an error running the query "'.$query.'" - '.$dbverbruik->error);}
			
			if (!isset($dbdomoticz)) {
				$dbdomoticz=new PDO("mysql:host=127.0.0.1;dbname=$dbname;",$dbuser,$dbpass);
			}
			$stmt=$dbdomoticz->query("select n,s,m from devices WHERE n IN ('watervandaag','gasvandaag','zonvandaag','elvandaag');");
			while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
				$d[$row['n']]['s'] = $row['s'];
				$d[$row['n']]['m'] = $row['m'];
			}
			$water=$water*1000;
			if($verbruik>=10) $verbruik=round($verbruik, 1);
			elseif($verbruik>=2) $verbruik=round($verbruik, 2);
			else $verbruik=round($verbruik, 3);
			if($zonvandaag>=10) $zonvandaag=round($zonvandaag, 1);
			elseif($zonvandaag>=2) $zonvandaag=round($zonvandaag, 2);
			else $zonvandaag=round($zonvandaag, 3);
			if ($water!=$d['watervandaag']['s']) {
				$dbdomoticz->query("UPDATE devices SET s=$water,t=$time WHERE n='watervandaag';");
				$d['watervandaag']['s']=$water;
				$mqtt->publish('i/watervandaag', json_encode($d['watervandaag']), 0, true);
			}
			if ($gas!=$d['gasvandaag']['s']) {
				$dbdomoticz->query("UPDATE devices SET s=$gas,t=$time WHERE n='gasvandaag';");
				$d['gasvandaag']['s']=$gas;
				$mqtt->publish('i/gasvandaag', json_encode($d['gasvandaag']), 0, true);
			}
			if ($zonvandaag!=$d['zonvandaag']['s']) {
				$dbdomoticz->query("UPDATE devices SET s=$zonvandaag,t=$time WHERE n='zonvandaag';");
				$d['zonvandaag']['s']=$zonvandaag;
				$mqtt->publish('i/zonvandaag', json_encode($d['zonvandaag']), 0, true);
			}
			if ($verbruik!=$d['elvandaag']['s']) {
				$dbdomoticz->query("UPDATE devices SET s=$verbruik,t=$time WHERE n='elvandaag';");
				$d['elvandaag']['s']=$verbruik;
				$mqtt->publish('i/elvandaag', json_encode($d['elvandaag']), 0, true);
			}
			$prevtotal=$total;
		}
	}
	if ($uur==0&&$min==1&&$sec==0) {
		if (!isset($dbverbruik)) {
			$dbverbruik=new mysqli('192.168.2.20','home','H0m€','verbruik');
			if($dbverbruik->connect_errno>0){die('Unable to connect to database ['.$dbverbruik->connect_error.']');}
		}
		$since=date("Y-m-d",$time-(86400*30));
		$query="SELECT AVG(gas) AS gas, AVG(elec) AS elec, AVG(water)*1000 AS water FROM `Guydag` WHERE date>'$since'";
		echo $query.PHP_EOL;
		if(!$result=$dbverbruik->query($query))echo('There was an error running the query "'.$query.'" '.$dbverbruik->error);
		while($row=$result->fetch_assoc())$avg=$row;$result->free();
		if (isset($avg)) {
			if (!isset($dbdomoticz)) {
				$dbdomoticz=new PDO("mysql:host=127.0.0.1;dbname=$dbname;",$dbuser,$dbpass);
			}
			$dbdomoticz->query("UPDATE devices SET m=".round($avg['water'],3)." WHERE n='watervandaag';");
			$d['watervandaag']['m']=round($avg['water'],3);
			$mqtt->publish($topic, json_encode($d['watervandaag']), 0, true);
			$dbdomoticz->query("UPDATE devices SET m=".round($avg['gas'],3)." WHERE n='gasvandaag';");
			$dbdomoticz->query("UPDATE devices SET m=".round($avg['elec'],3)." WHERE n='elvandaag';");
		}
		if (!isset($dbzonphp)) {
			$dbzonphp=new mysqli('192.168.2.20','home','H0m€','egregius_zonphp');
			if($dbzonphp->connect_errno>0){die('Unable to connect to database ['.$dbzonphp->connect_error.']');}
		}
		$maand=date('m');
		$query="SELECT Dag_Refer FROM `tgeg_refer` WHERE Datum_Refer='2009-".$maand."-01 00:00:00'";
		if(!$result=$dbzonphp->query($query))echo('There was an error running the query "'.$query.'" '.$dbzonphp->error);
		while($row=$result->fetch_assoc())$zonref=$row['Dag_Refer'];$result->free();
		
		$query="SELECT AVG(Geg_Dag) AS AVG FROM `tgeg_dag` WHERE Datum_Dag like '%-".$maand."-%' and Geg_Dag > (SELECT MAX(Geg_Dag)/2 FROM tgeg_dag WHERE Datum_Dag like '%-".$maand."-%')";
		if(!$result=$dbzonphp->query($query))echo('There was an error running the query "'.$query.'" '.$dbzonphp->error);
		while($row=$result->fetch_assoc())$zonavg=$row['AVG'];$result->free();
		
		
		if (isset($zonref,$zonavg))	$dbdomoticz->query("UPDATE devices SET m=".$zonref.", icon=".$zonavg." WHERE n='zonvandaag';");
	}
	$x++;
	$time_elapsed_secs=microtime(true)-$start;
	$sleep=1-$time_elapsed_secs;
	if ($sleep>0) {
		$sleep=round($sleep*1000000);
		usleep($sleep);
	}
}