#!/usr/bin/php
<?php
require '/var/www/html/secure/functions.php';
date_default_timezone_set('Europe/Brussels');

/** Databases */
$dbverbruik = new mysqli('192.168.2.20','home','H0m€','verbruik');
if($dbverbruik->connect_errno > 0) die('DB verbruik fail: '.$dbverbruik->connect_error);

/** Kwartierpiek deze maand */
$kwartierpiek = 2500;
$q = "SELECT MAX(wH) AS wH FROM `kwartierpiek` WHERE date LIKE '".date('Y-m')."-%'";
if ($r = $dbverbruik->query($q)) {
    if ($row = $r->fetch_assoc()) $kwartierpiek = $row['wH'] ?? 2500;
    $r->free();
}

/** Inladen huidige meterdata */
$en = json_decode(getCache('en'));
$teller = json_decode(getCache('teller'));
if (!isset($teller->import)) exit; // niets binnen → stoppen

$newzon=$en->z;
$gas=$teller->gas;
$elec=$teller->import;
$injectie=$teller->export;
$water=$teller->water;

/** Variabelen uit meter */
$total = (int)(($teller->import*100)+($teller->export*100)+($teller->gas*1000));
$newavg = $en->a;

/** Vorige waarden */
$prevavg   = getCache('energy_prevavg', 0);
$prevtotal = getCache('energy_prevtotal', 0);

/** Always-on detectie */
if ($newzon == 0) {
    $power = $en->n;
    $alwayson = (int)getCache('alwayson');
    if ($power>=50 && ($power<$alwayson || empty($alwayson))) {
        setCache('alwayson',$power);
        $time=time();
        lg('New alwayson '.$power.' W');
        $vandaag=date("Y-m-d",$time);
        $dbverbruik->query("INSERT INTO `alwayson` (`date`,`w`) VALUES ('$vandaag','$alwayson') ON DUPLICATE KEY UPDATE `w`='$alwayson'");
    }
}

/** Kwartierpiek detectie */
if ($prevavg > 2500) {
    if ($newavg > $kwartierpiek - 200) alert('Kwartierpiek', 'Kwartierpiek momenteel al '.$newavg.' Wh!'.PHP_EOL.'Piek deze maand = '.$kwartierpiek.' Wh', 120, false);
    if ($newavg < $prevavg) { // nieuw kwartier
        $dbverbruik->query("INSERT INTO `kwartierpiek` (`date`,`wh`) VALUES ('".date('Y-m-d H:i:s')."','".$prevavg."')");
        if ($prevavg > $kwartierpiek - 200) {
            alert('KwartierpiekB', 'Kwartierpiek = '.$prevavg.' Wh'.PHP_EOL.'Piek deze maand = '.$kwartierpiek.' Wh', 30, false);
            $kwartierpiek = $prevavg;
        }
    }
}

/** Verbruik updates als total gewijzigd */
if ($total != $prevtotal) {
	
	$prevwater=getCache('water_meter');
	if ($prevwater!=$water && getCache('weg')>2) {
		setCache('water_meter',$water);
		alert('water_meter', 'Water verbruik gedetecteerd!', 300, true);
		lg("Waterteller: prev=$prevwater, nu=$water");
	}
       

    $time=time();
    $vandaag=date("Y-m-d",$time);

    /** Zon totalen */
    $dbzonphp = new mysqli('192.168.2.20','home','H0m€','egregius_zonphp');
    $zonvandaag=0; $zontotaal=0;

    $q="SELECT Geg_Maand FROM `tgeg_maand` WHERE `Datum_Maand` = '$vandaag  0:00:00'";
    if ($r=$dbzonphp->query($q)) { if ($row=$r->fetch_assoc()) $zonvandaag=$row['Geg_Maand']; $r->free(); }

    $q="SELECT SUM(Geg_Maand) AS Geg_Maand FROM `tgeg_maand`";
    if ($r=$dbzonphp->query($q)) { if ($row=$r->fetch_assoc()) $zontotaal=$row['Geg_Maand']; $r->free(); }

    $dbverbruik->query("INSERT INTO `Guy` (`date`,`gas`,`elec`,`injectie`,`zon`,`water`) VALUES ('$vandaag','$gas','$elec','$injectie','$zontotaal','$water')
        ON DUPLICATE KEY UPDATE gas='$gas',elec='$elec',injectie='$injectie',zon='$zontotaal',water='$water'");

    /** Dagwaarden */
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
	echo $query.PHP_EOL;
	if(!$result=$dbverbruik->query($query))echo('There was an error running the query "'.$query.'" '.$dbverbruik->error);
	while($row=$result->fetch_assoc())$avg=$row;$result->free();
		
	
    $data=json_encode(['gas'=>$gas,'gasavg'=>round($avg['gas'],3),'elec'=>$elec,'elecavg'=>round($avg['elec'],3),'verbruik'=>$verbruik,'zon'=>$zonvandaag,'alwayson'=>$alwayson]);
    lg('Updating teller database:'.$data);
    setCache('energy_vandaag',$data);
}

/** Opslaan nieuwe referenties */
setCache('energy_prevavg', $newavg);
setCache('energy_prevtotal', $total);
