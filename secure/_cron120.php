<?php
/**
 * Pass2PHP functions
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
    
    if ($rainpast>128000) $pomppauze=43200;
    elseif ($rainpast>64000) $pomppauze=86400;
    elseif ($rainpast>32000) $pomppauze=86400*2;
    else $pomppauze=86400*28;

    if ($d['regenpomp']['s']=='On'&&past('regenpomp')>57) {
        sw('regenpomp', 'Off');
    } elseif ($d['regenpomp']['s']=='Off'&&past('regenpomp')>$pomppauze) {
        sw('regenpomp', 'On');
        telegram('Regenpomp aan, rainpast='.$rainpast);
    }
    if ($d['achterdeur']['s']=='Closed') {
    	$stmt=$db->query("SELECT MAX(`buiten`) AS max FROM temp;");
		while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
			$watertime=$row['max']*15;
		}
		if (TIME>=strtotime('21:30')
			&&$d['zon']['s']==0
			&&past('zon')>1800
			&&past('water')>72000
		) {
			$msg="Regen check:
				__Laatste 48u:$rainpast
				__Volgende 48u: $maxrain
				__Automatisch tuin water geven gestart voor $watertime sec.";
			if ($rainpast<1000&&$maxrain<1) {
				sw('water', 'On');
				storemode('water', $watertime);
				telegram($msg, 2);
			}
		}
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
		elseif ($d['wind']['s']>=15) $luifel=35;
		elseif ($d['wind']['s']>=10) $luifel=40;
		else $luifel=40;
	} elseif ($d['heating']['s']==0	&&$d['living_temp']['s']>20&&$d['zon']['s']>2000&&TIME>=strtotime("10:00")) { //Neutral
		if ($d['wind']['s']>=30) 	 $luifel=0;
		elseif ($d['wind']['s']>=25) $luifel=25;
		elseif ($d['wind']['s']>=20) $luifel=30;
		elseif ($d['wind']['s']>=15) $luifel=35;
		elseif ($d['wind']['s']>=10) $luifel=40;
		else $luifel=40;
	} elseif ($d['heating']['s']==1	&&$d['living_temp']['s']>19&&$d['zon']['s']>200&&TIME>=strtotime("10:00")) { //Cooling
		if ($d['wind']['s']>=30) 	 $luifel=0;
		elseif ($d['wind']['s']>=25) $luifel=30;
		elseif ($d['wind']['s']>=20) $luifel=40;
		elseif ($d['wind']['s']>=15) $luifel=50;
		elseif ($d['wind']['s']>=10) $luifel=60;
		else $luifel=70;
	} else {
		$luifel=0;
	}
	//if ($luifel>$d['luifel']['s']) $luifel=$d['luifel']['s']+10;
	//elseif ($luifel<$d['luifel']['s']&&$d['achterdeur']['s']=='Closed') $luifel=$d['luifel']['s']-10;
	if ($d['wind']['s']>=40) 	 $luifel=0;
	if ($d['buien']['s']>=10) 	 $luifel=0;
	if ($luifel<10) $luifel=0;
	elseif ($luifel>100) $luifel=100;

	echo $luifel;
	
	echo past('luifel');
	if (		past('luifel')>900&&$d['luifel']['s']<$luifel&&$d['luifel']['m']==0&&$d['wind']['s']<$windhist) {
		sl('luifel', $luifel);
	} elseif (	past('luifel')>300&&$d['luifel']['s']>$luifel&&$d['luifel']['m']==0) {
		sl('luifel', $luifel);
	}
	if ($d['luifel']['m']==1) {
		if (past('luifel')>3600&&$luifel<30) {
			storemode('luifel', 0);
			$d['luifel']['m']=1;
		} elseif (past('luifel')>28800) {
			storemode('luifel', 0);
			$d['luifel']['m']=1;
		}
	}
}
