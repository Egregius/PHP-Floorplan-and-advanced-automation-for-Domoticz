#!/usr/bin/php
<?php
$force = in_array('--force', $argv);

require '/var/www/html/secure/functions.php';
date_default_timezone_set('Europe/Brussels');

$dbverbruik = new mysqli('192.168.2.20','home','H0m€','verbruik');
if($dbverbruik->connect_errno > 0) die('DB verbruik fail: '.$dbverbruik->connect_error);

$kwartierpiek = 2500;
$q = "SELECT MAX(wH) AS wH FROM `kwartierpiek` WHERE date LIKE '".date('Y-m')."-%'";
if ($r = $dbverbruik->query($q)) {
    if ($row = $r->fetch_assoc()) $kwartierpiek = $row['wH'] ?? 2500;
    $r->free();
}

$en = json_decode(getCache('en'));
$teller = json_decode(getCache('teller'));
if (!isset($teller->import)) exit;
$newData = [
    'energy_import' => $teller->import,
    'energy_export' => $teller->export,
    'gas' => $teller->gas,
    'water' => $teller->water
];
$zon=$en->z;
$gas=$teller->gas;
$elec=$teller->import;
$injectie=$teller->export;
$water=$teller->water;
$alwayson = (int)getCache('alwayson');
$newavg = $en->a;
$prevavg   = getCache('energy_prevavg');

if ($zon == 0) {
    if ($en->b<0) $power = $en->n-$en->b;
    else $power = $en->n;
    if ($power>=30 && $power<=300 && ($power<$alwayson || empty($alwayson))) {
        setCache('alwayson',$power);
        $alwayson=$power;
        $force=true;
        $time=time();
        lg('New alwayson '.$power.' W');
        $vandaag=date("Y-m-d",$time);
        $dbverbruik->query("INSERT INTO `alwayson` (`date`,`w`) VALUES ('$vandaag','$alwayson') ON DUPLICATE KEY UPDATE `w`='$alwayson'");
    }
}

if ($prevavg > 2500) {
    if ($newavg > $kwartierpiek - 200) alert('Kwartierpiek', 'Kwartierpiek momenteel al '.$newavg.' Wh!'.PHP_EOL.'Piek deze maand = '.$kwartierpiek.' Wh', 120, false);
    if ($newavg < $prevavg) {
        $dbverbruik->query("INSERT INTO `kwartierpiek` (`date`,`wh`) VALUES ('".date('Y-m-d H:i:s')."','".$prevavg."')");
        if ($prevavg > $kwartierpiek - 200) {
            alert('KwartierpiekB', 'Kwartierpiek = '.$prevavg.' Wh'.PHP_EOL.'Piek deze maand = '.$kwartierpiek.' Wh', 30, false);
            $kwartierpiek = $prevavg;
        }
    }
}

if (updateVerbruikCache($newData, $force)) {	
	$prevwater=getCache('water_meter');
	if ($prevwater!=$water && getCache('weg')>2) {
		setCache('water_meter',$water);
		alert('water_meter', 'Water verbruik gedetecteerd!', 300, true);
		lg("Waterteller: prev=$prevwater, nu=$water");
	}
    $time=time();
    $vandaag=date("Y-m-d",$time);

    $dbzonphp = new mysqli('192.168.2.20','home','H0m€','egregius_zonphp');
    $zonvandaag=0; $zontotaal=0;

    $q="SELECT Geg_Maand FROM `tgeg_maand` WHERE `Datum_Maand` = '$vandaag  0:00:00'";
    if ($r=$dbzonphp->query($q)) { if ($row=$r->fetch_assoc()) $zonvandaag=$row['Geg_Maand']; $r->free(); }

    $q="SELECT SUM(Geg_Maand) AS Geg_Maand FROM `tgeg_maand`";
    if ($r=$dbzonphp->query($q)) { if ($row=$r->fetch_assoc()) $zontotaal=$row['Geg_Maand']; $r->free(); }

    $dbverbruik->query("INSERT INTO `Guy` (`date`,`gas`,`elec`,`injectie`,`zon`,`water`) VALUES ('$vandaag','$gas','$elec','$injectie','$zontotaal','$water')
        ON DUPLICATE KEY UPDATE gas='$gas',elec='$elec',injectie='$injectie',zon='$zontotaal',water='$water'");

    $q="SELECT `date`,`gas`,`elec`,`injectie`,`water` FROM `Guy` ORDER BY `date` DESC LIMIT 1,1";
    if ($r=$dbverbruik->query($q)) { $gisteren=$r->fetch_assoc(); $r->free(); }

    $gas = round($gas-$gisteren['gas'],3);
    $elec = round($elec - $gisteren['elec'],3);
    $water = round($water-$gisteren['water'],3);
    $injectie = round($injectie-$gisteren['injectie'],3);
    $verbruik = round($zonvandaag-$injectie+$elec,3);

    $dbverbruik->query("INSERT INTO `Guydag` (`date`,`gas`,`elec`,`verbruik`,`zon`,`water`) VALUES ('$vandaag','$gas','$elec','$verbruik','$zonvandaag','$water')
        ON DUPLICATE KEY UPDATE gas='$gas',elec='$elec',verbruik='$verbruik',zon='$zonvandaag',water='$water'");
        
    $since=date("Y-m-d",$time-(86400*30));
	$query="SELECT AVG(gas) AS gas, AVG(elec) AS elec FROM `Guydag` WHERE date>'$since'";

	if(!$result=$dbverbruik->query($query))echo('There was an error running the query "'.$query.'" '.$dbverbruik->error);
	while($row=$result->fetch_assoc())$avg=$row;$result->free();
	$maand=date('m');
	$query="SELECT Dag_Refer FROM `tgeg_refer` WHERE Datum_Refer='2009-".$maand."-01 00:00:00'";
	if(!$result=$dbzonphp->query($query))echo('There was an error running the query "'.$query.'" '.$dbzonphp->error);
	while($row=$result->fetch_assoc())$zonref=round($row['Dag_Refer'],1);$result->free();
	
	$query="SELECT AVG(Geg_Dag) AS AVG FROM `tgeg_dag` WHERE Datum_Dag like '%-".$maand."-%' and Geg_Dag > (SELECT MAX(Geg_Dag)/2 FROM tgeg_dag WHERE Datum_Dag like '%-".$maand."-%')";
	if(!$result=$dbzonphp->query($query))echo('There was an error running the query "'.$query.'" '.$dbzonphp->error);
	while($row=$result->fetch_assoc())$zonavg=round($row['AVG'],0);$result->free();
	
    $data=json_encode(['gas'=>$gas,'gasavg'=>round($avg['gas'],3),'elec'=>$elec,'elecavg'=>round($avg['elec'],3),'verbruik'=>$verbruik,'zon'=>$zonvandaag,'zonref'=>$zonref,'zonavg'=>$zonavg,'alwayson'=>$alwayson]);
    lg($data);
    echo $data;
    setCache('energy_vandaag',$data);
    setCache('energy_lastupdate', $time);
}

setCache('energy_prevavg', $newavg);

function updateVerbruikCache($newData, $force = false, $thresholds = ['energy_import'=>0.1,'energy_export'=>0.1,'gas'=>0.1,'water'=>0.01]) {
    $cacheFile = '/dev/shm/cache/verbruik.json';
    $cache = [];
    if (file_exists($cacheFile)) {
        $cache = json_decode(file_get_contents($cacheFile), true) ?: [];
    }
    if (!isset($cache['previous'])) {
        $cache['previous'] = $newData;
        $cache['current'] = $newData;
        file_put_contents($cacheFile, json_encode($cache));
        return true;
    }
    if ($force) {
        $cache['current'] = $newData;
        file_put_contents($cacheFile, json_encode($cache));
        return true;
    }
    $updateNeeded = false;
    foreach ($newData as $key => $value) {
        $prevValue = $cache['previous'][$key] ?? 0;
        $dif=round(abs($value - $prevValue),3);
        if ($dif >= ($thresholds[$key] ?? 0)) {
        	lg('update needed');
            $updateNeeded = true;
            break;
        }
    }
    if ($updateNeeded || $force) {
		$cache['previous'] = $newData;
		$cache['current'] = $newData;
		file_put_contents($cacheFile, json_encode($cache));
	}
    return $updateNeeded;
}