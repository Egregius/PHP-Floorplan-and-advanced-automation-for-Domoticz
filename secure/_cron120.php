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
    if ($rainpast>64000) $pomppauze=43200;
    elseif ($rainpast>32000) $pomppauze=86400;
    elseif ($rainpast>16000) $pomppauze=86400*2;
    else $pomppauze=86400*28;

    if ($d['regenpomp']['s']=='On'&&past('regenpomp')>57) {
        sw('regenpomp', 'Off');
    } elseif ($d['regenpomp']['s']=='Off'&&past('regenpomp')>$pomppauze) {
        sw('regenpomp', 'On');
        telegram('Regenpomp aan, rainpast='.$rainpast);
    }
    if (TIME>=strtotime('21:30')
        &&$d['zon']['s']==0
        &&$d['achterdeur']['s']=='Closed'
        &&past('zon')>1800
        &&past('water')>72000
    ) {
        $msg="Regen check:
            __Laatste 48u:$rainpast
            __Volgende 48u: $maxrain
            __Automatisch tuin water geven gestart.";
        if ($rainpast<1000&&$maxrain<1) {
            sw('water', 'On');
            storemode('water', 300);
            telegram($msg, 2);
        }
    }
    $x=0;
    foreach ($windhist as $y) {
        $x=$y+$x;
        $windhist=round($x/4, 2);
    }
    if ($d['heating']['s']==0&&$d['buien']['s']<10) { //Neutral
        if ($d['wind']['s']>=30) $luifel=0;
        elseif ($d['wind']['s']>=25) $luifel=25;
        elseif ($d['wind']['s']>=20) $luifel=30;
        elseif ($d['wind']['s']>=15) $luifel=35;
        elseif ($d['wind']['s']>=10) $luifel=40;
        else $luifel=40;
    } elseif ($d['heating']['s']==1&&$d['buien']['s']<10) { //Cooling
        if ($d['wind']['s']>=30) $luifel=0;
        elseif ($d['wind']['s']>=25) $luifel=30;
        elseif ($d['wind']['s']>=20) $luifel=40;
        elseif ($d['wind']['s']>=15) $luifel=50;
        elseif ($d['wind']['s']>=10) $luifel=60;
        else $luifel=70;
    } else {
        $luifel=0;
    }
    echo $luifel;
    if ($d['luifel']['m']==1) {
        if (past('luifel')>3600&&$luifel<30) {
            storemode('luifel', 0);
            $d['luifel']['m']=1;
        } elseif (past('luifel')>28800) {
            storemode('luifel', 0);
            $d['luifel']['m']=1;
        }
    }
    if ($d['luifel']['s']!=$luifel&&$d['luifel']['m']==0) {
        sl('luifel', $luifel);
    } 
}
$items=array('buiten_temp', 'living_temp', 'badkamer_temp', 'kamer_temp', 'tobi_temp', 'alex_temp', 'zolder_temp');
foreach ($items as $i) {
    if (past($i)>1800) {
        storeicon($i, '');
    }
}