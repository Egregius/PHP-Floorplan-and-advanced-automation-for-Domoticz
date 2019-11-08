<?php
/**
 * Pass2PHP 
 * php version 7.3.4-2
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
$user='cron120';

if ($d['auto']['s']=='On') {
	$windhist=json_decode($d['wind']['m']);
    $db=new PDO("mysql:host=localhost;dbname=domotica;", 'domotica', 'domotica');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt=$db->query("SELECT SUM(`buien`) AS buien FROM regen;");
    while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
        $rainpast=$row['buien'];
    }
    if ($d['minmaxtemp']['m']>1) $base=3600*$d['minmaxtemp']['m'];
    else $base=3600;
    if ($rainpast>64000) $pomppauze=$base;
    elseif ($rainpast>32000) $pomppauze=$base*2;
    elseif ($rainpast>16000) $pomppauze=$base*4;
    else $pomppauze=$base*24;

    if ($d['regenpomp']['s']=='On'&&past('regenpomp')>57) {
        sw('regenpomp', 'Off', basename(__FILE__).':'.__LINE__);
    } elseif ($d['regenpomp']['s']=='Off'&&past('regenpomp')>$pomppauze) {
        sw('regenpomp', 'On', basename(__FILE__).':'.__LINE__);
    }
	$x=0;
	foreach ($windhist as $y) {
		$x=$y+$x;
		$windhist=round($x/4, 2);
	}
	if (	$d['heating']['s']>=2	&&$d['living_temp']['s']>22&&$d['zon']['s']>3000&&TIME>=strtotime("10:00")) { //Heating
		if ($d['wind']['s']>=30) 	 $luifel=0;
		elseif ($d['wind']['s']>=25) $luifel=25;
		elseif ($d['wind']['s']>=20) $luifel=30;
		elseif ($d['wind']['s']>=15) $luifel=30;
		elseif ($d['wind']['s']>=10) $luifel=30;
		else $luifel=40;
	} elseif ($d['heating']['s']==0	&&$d['living_temp']['s']>20&&$d['zon']['s']>2000&&TIME>=strtotime("10:00")) { //Neutral
		if ($d['wind']['s']>=30) 	 $luifel=0;
		elseif ($d['wind']['s']>=25) $luifel=25;
		elseif ($d['wind']['s']>=20) $luifel=30;
		elseif ($d['wind']['s']>=15) $luifel=30;
		elseif ($d['wind']['s']>=10) $luifel=30;
		else $luifel=40;
	} elseif ($d['heating']['s']==1	&&$d['living_temp']['s']>19&&$d['zon']['s']>200&&TIME>=strtotime("10:00")) { //Cooling
		if ($d['wind']['s']>=30) 	 $luifel=0;
		elseif ($d['wind']['s']>=25) $luifel=30;
		elseif ($d['wind']['s']>=20) $luifel=30;
		elseif ($d['wind']['s']>=15) $luifel=30;
		elseif ($d['wind']['s']>=10) $luifel=30;
		else $luifel=70;
	} else {
		$luifel=0;
	}
	$luifel=0;
	if ($luifel>$d['luifel']['s']+30) $luifel=$d['luifel']['s']+30;
	elseif ($luifel<$d['luifel']['s']-30&&$d['achterdeur']['s']=='Closed') $luifel=$d['luifel']['s']-30;
	if ($d['wind']['s']>=40) 	 $luifel=0;
	if ($d['buien']['s']>=10) 	 $luifel=0;
	if ($luifel<10) $luifel=0;
	elseif ($luifel>100) $luifel=100;


	echo 'past='.past('luifel').' $d[\'luifel\'][\'s\']='.$d['luifel']['s'].' $luifel='.$luifel.' $d[\'luifel\'][\'m\']='.$d['luifel']['m'].' wind='.$d['wind']['s'].' windhist='.$windhist;
	if (		past('luifel')>900&&$d['luifel']['s']<$luifel&&$d['luifel']['m']==0&&$d['wind']['s']<$windhist) {
		sl('luifel', $luifel, basename(__FILE__).':'.__LINE__);
	} elseif (	past('luifel')>300&&$d['luifel']['s']>$luifel&&$d['luifel']['m']==0) {
		sl('luifel', $luifel, basename(__FILE__).':'.__LINE__);
	}
	
	if ($d['luifel']['m']==1) {
		if (past('luifel')>3600&&$luifel<30&&$d['achterdeur']['s']=='Closed') {
			storemode('luifel', 0, basename(__FILE__).':'.__LINE__);
			$d['luifel']['m']=1;
		} elseif (past('luifel')>28800) {
			storemode('luifel', 0, basename(__FILE__).':'.__LINE__);
			$d['luifel']['m']=1;
		}
	}
}
