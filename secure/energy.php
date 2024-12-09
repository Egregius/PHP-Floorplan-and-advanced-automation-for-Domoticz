#!/usr/bin/php
<?php
require '/var/www/html/secure/functions.php';
while (1){
	// Homewizard Energy
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
		if(!$result=$db->query($query))echo('There was an error running the query "'.$query.'" '.$db->error);
		while($row=$result->fetch_assoc())$zon=$row['Geg_Maand'];$result->free();
		if(!isset($zon)) $zon=0;
	
		$query="SELECT SUM(Geg_Maand) AS Geg_Maand FROM `tgeg_maand`;";
		if(!$result=$db->query($query))echo('There was an error running the query "'.$query.'" '.$db->error);
		while($row=$result->fetch_assoc())$zontotaal=$row['Geg_Maand'];$result->free();
		$db->close();
		
		$db=new mysqli('192.168.2.20','home','H0m€','verbruik');
		if($db->connect_errno>0){die('Unable to connect to database ['.$db->connect_error.']');}
	
		$query="INSERT INTO `Guy` (`date`,`gas`,`elec`,`injectie`,`zon`,`water`) VALUES ('$vandaag','$gas','$elec','$injectie','$zontotaal','$water') ON DUPLICATE KEY UPDATE `gas`='$gas',`elec`='$elec',`injectie`='$injectie',`zon`='$zontotaal',`water`='$water'";
		if(!$result=$db->query($query)){echo('There was an error running the query "'.$query.'" - '.$db->error);}
	
		$query="SELECT `date`,`gas`,`elec`,`injectie`,`water` FROM `Guy` ORDER BY `date` DESC LIMIT 1,1";
		if(!$result=$db->query($query))echo('There was an error running the query "'.$query.'" '.$db->error);
		while($row=$result->fetch_assoc())$gisteren=$row;$result->free();
	
		$gas=round($gas-$gisteren['gas'],3);
		$elec=$elec-$gisteren['elec'];
		$water=round($water-$gisteren['water'],3);
		$injectie=round($injectie-$gisteren['injectie'],3);
		$verbruik=round($zon-$injectie+$elec,3);
		$query="INSERT INTO `Guydag` (`date`,`gas`,`elec`,`verbruik`,`zon`,`water`) VALUES ('$vandaag','$gas','$elec','$verbruik','$zon','$water') ON DUPLICATE KEY UPDATE `gas`='$gas',`elec`='$elec',`verbruik`='$verbruik',`zon`='$zon',`water`='$water'";
		if(!$result=$db->query($query)){echo('There was an error running the query "'.$query.'" - '.$db->error);}
		$db->close();
		
		$db=new PDO("mysql:host=127.0.0.1;dbname=$dbname;",$dbuser,$dbpass);
		$stmt=$db->query("select n,s,m from devices WHERE n IN ('watervandaag','gasvandaag','zonvandaag','el','zon');");
		while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
			$d[$row['n']]['s'] = $row['s'];
			$d[$row['n']]['m'] = $row['m'];
		}
		$water=$water*1000;
		$verbruik=round($verbruik, 1);
		if ($water!=$d['watervandaag']['s']) {
			echo 'Updating watervandaag'.PHP_EOL;
			$db->query("UPDATE devices SET s=$water,t=$time WHERE n='watervandaag';");
		}
		if ($gas!=$d['gasvandaag']['s']) {
			echo 'Updating gasvandaag'.PHP_EOL;
			$db->query("UPDATE devices SET s=$gas,t=$time WHERE n='gasvandaag';");
		}
		if ($zon!=$d['zonvandaag']['s']) {
			echo 'Updating zonvandaag'.PHP_EOL;
			$db->query("UPDATE devices SET s=$zon,t=$time WHERE n='zonvandaag';");
		}
		if ($verbruik!=$d['el']['m']) {
			echo 'Updating el'.PHP_EOL;
			$db->query("UPDATE devices SET m=$verbruik,t=$time WHERE n='el';");
		}

		// Smappee
		$ctx=stream_context_create(array('http'=>array('timeout' =>5)));
		$smappee=@json_decode(@file_get_contents('http://192.168.2.15/gateway/apipublic/reportInstantaneousValues', false, $ctx), true);
		if (isset($smappee['report'])&&!empty($smappee['report'])) {
			preg_match_all("/ activePower=(\\d*.\\d*)/",$smappee['report'],$matches);
			if (!empty($matches[1][1])) {
				$newzon=round($matches[1][1], 0);
				if ($newzon<0) $newzon=0;
				if ($newzon==0 ) $db->query("UPDATE devices SET s='$newzon' WHERE n='zon';") or trigger_error($db->error);
				else $db->query("UPDATE devices SET s='$newzon',t='".$time."' WHERE n='zon';") or trigger_error($db->error);
		
				if (!empty($matches[1][2])) {
					$consumption=round($matches[1][2], 0);
					$net=$data->active_power_w;
					$avg=$data->active_power_average_w;
					$db->query("UPDATE devices SET dt='$net', s='$consumption', icon='".$avg."', t='".$time."' WHERE n='el';") or trigger_error($db->error);
					if ($net>8500) alert('Power', 'Power usage: '.$net.' W!', 600, false);
					if ($avg>2500) alert('Kwartierpiek', 'Kwartierpiek: '.$avg.' Wh!', 300, false);
				}
				
			}
		} else {
			if (shell_exec('curl -H "Content-Type: application/json" -X POST -d "" http://192.168.2.15/gateway/apipublic/logon')!='{"success":"Logon successful!","header":"Logon to the monitor portal successful..."}') {
				exit;
			}
		}
	}
	sleep(1);
}