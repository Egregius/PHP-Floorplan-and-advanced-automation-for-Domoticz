#!/usr/bin/php
<?php
require '/var/www/html/secure/functions.php';

$x=1;
$sleep='	start';
$prevtotal=0;
while (1){
	$start = microtime(true);
	$dag=mget('dag');
	$en=mget('en');
	$data=curl('http://192.168.2.4/api/v1/data');
	if ($dag>2) {
		$zon=curl('http://192.168.2.9/api/v1/data');
		$zon=json_decode($zon);
		if (isset($zon->active_power_w)) {
			$prevzon=mget('zon');
			$newzon=round($zon->active_power_w);
		}
	} else {
		$prevzon=mget('zon');
		$newzon=0;
	}
	$data=json_decode($data);
	if (isset($data->total_power_import_kwh)) {
		mset('en',array(
			'net'=>$data->active_power_w,
			'avg'=>$data->active_power_average_w,
			'zon'=>$newzon,
		));
		$total=(int)(($data->total_power_import_kwh*100)+($data->total_power_export_kwh*100)+($data->total_gas_m3*1000));
		if ($data->active_power_w>8500) alert('Power', 'Power usage: '.$data->active_power_w.' W!', 600, false);
		if ($data->active_power_average_w>2300) alert('Kwartierpiek', 'Kwartierpiek: '.$data->active_power_average_w.' Wh!', 300, false);

		$power=$data->active_power_w-$newzon;
		$alwayson=mget('alwayson');
		if ($power>=50&&($power<$alwayson||empty($alwayson))) {
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
		
		$sec=date('s');
		$min=date('i');
		$uur=date('G');
		if ($data->active_power_average_w>2500&&$sec==52&&($min==14||$min==29||$min==44||$min==59)) { // Einde kwartier
			if (!isset($dbverbruik)) {
				$dbverbruik=new mysqli('192.168.2.20','home','H0m€','verbruik');
				if($dbverbruik->connect_errno>0){die('Unable to connect to database ['.$dbverbruik->connect_error.']');}
			}
			$query="INSERT INTO `kwartierpiek` (`date`,`wh`) VALUES ('".date('Y-m-d H:i:s')."','".$data->active_power_average_w."')";
			if(!$result=$dbverbruik->query($query)){echo('There was an error running the query "'.$query.'" - '.$dbverbruik->error);}
			telegram('Kwartierpiek: '.$data->active_power_average_w.' Wh');
		}

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
			if ($water!=$d['watervandaag']['s']) $dbdomoticz->query("UPDATE devices SET s=$water,t=$time WHERE n='watervandaag';");
			if ($gas!=$d['gasvandaag']['s']) $dbdomoticz->query("UPDATE devices SET s=$gas,t=$time WHERE n='gasvandaag';");
			if ($zonvandaag!=$d['zonvandaag']['s']) $dbdomoticz->query("UPDATE devices SET s=$zonvandaag,t=$time WHERE n='zonvandaag';");
			if ($verbruik!=$d['elvandaag']['s']) $dbdomoticz->query("UPDATE devices SET s=$verbruik,t=$time WHERE n='elvandaag';");
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

	if ($x==86399) exit;
	$x++;
	$time_elapsed_secs=microtime(true)-$start;
	$sleep=1-$time_elapsed_secs;
	if ($sleep<0) $sleep=0;
	$sleep=round($sleep*1000000);
	usleep($sleep);
}