#!/usr/bin/php
<?php
require '/var/www/html/secure/functions.php';

$x=1;
$sleep='	start';
while (1){
	$start = microtime(true);
	
	// Homewizard Energy
	$data=curl('http://192.168.2.4/api/v1/data');
	$data=json_decode($data);
	if (isset($data->total_power_import_kwh)) {
		mset('net',$data->active_power_w);
		mset('avg',$data->active_power_average_w);
		if ($data->active_power_w>8500) alert('Power', 'Power usage: '.$data->active_power_w.' W!', 600, false);
		if ($data->active_power_average_w>2500) alert('Kwartierpiek', 'Kwartierpiek: '.$data->active_power_average_w.' Wh!', 300, false);

		// Homewizard kWh meter
		$dag=mget('dag');
		if ($dag>2) {
			$zon=curl('http://192.168.2.9/api/v1/data');
			$zon=json_decode($zon);
			if (isset($zon->active_power_w)) {
				$prevzon=mget('zon');
				$newzon=round($zon->active_power_w);
				if ($prevzon!=$newzon) mset('zon',$newzon);
				
			}
		} else {
			$prevzon=mget('zon');
			$newzon=0;
			if ($prevzon!=$newzon) mset('zon',$newzon);
		}
		
		// Combined
		$power=$data->active_power_w-$newzon;
		$alwayson=mget('alwayson');
		if ($power<$alwayson||empty($alwayson)) {
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
		
		// Updating verbruik database
		$sec=date('s');
		$min=date('i');
		$uur=date('G');
		if ($uur>=6&&(($uur<23&&$min%10==0&&$sec==0)||($uur==23&&$min==59&&$sec==55))) {
			lg('--- Updating energy data ---');
			$elec=$data->total_power_import_kwh;
			$injectie=$data->total_power_export_kwh;
			foreach ($data->external as $i) {
				if ($i->type=='gas_meter') $gas=$i->value;
				elseif ($i->type=='water_meter') $water=$i->value;
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
			$verbruik=round($verbruik, 1);
			if ($water!=$d['watervandaag']['s']) $dbdomoticz->query("UPDATE devices SET s=$water,t=$time WHERE n='watervandaag';");
			if ($gas!=$d['gasvandaag']['s']) $dbdomoticz->query("UPDATE devices SET s=$gas,t=$time WHERE n='gasvandaag';");
			if ($zonvandaag!=$d['zonvandaag']['s']) $dbdomoticz->query("UPDATE devices SET s=$zonvandaag,t=$time WHERE n='zonvandaag';");
			if ($verbruik!=$d['elvandaag']['s']) $dbdomoticz->query("UPDATE devices SET s=$verbruik,t=$time WHERE n='elvandaag';");
		}
	}
	if ($x==10) exit;
	$x++;
	$time_elapsed_secs=microtime(true)-$start;
	$sleep=1-$time_elapsed_secs;
	if ($sleep<0) $sleep=0;
	$sleep=round($sleep*1000000);
	usleep($sleep);
}