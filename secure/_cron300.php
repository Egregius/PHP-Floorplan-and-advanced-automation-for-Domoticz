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
$user='cron300';
if (TIME<=strtotime('9:00')) {
	if ($d['nas']['s']!='On') {
		if (file_get_contents($urlnas)>0) {
			$k=file_get_contents($urlnas2);
			if ($k<4000000000) {
				shell_exec('./wakenas.sh');
				lg('Wake NAS');
			}
		}
	}
}
$db=new PDO("mysql:host=localhost;dbname=$dbname;", $dbuser, $dbpass);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt=$db->query("SELECT SUM(`buien`) AS buien FROM regen;");
while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
	$rainpast=$row['buien'];
}
if ($rainpast==0) $rainpast=1;
if ($d['minmaxtemp']['m'] > -3) {
	if ($rainpast>25000) $pomppauze=590;
	elseif ($rainpast>22000) $pomppauze=890;
	elseif ($rainpast>19000) $pomppauze=1190;
	elseif ($rainpast>16000) $pomppauze=1490;
	elseif ($rainpast>13000) $pomppauze=1790;
	elseif ($rainpast>10000) $pomppauze=2090;
	elseif ($rainpast>7000) $pomppauze=2390;
	elseif ($rainpast>3000) $pomppauze=2990;
	elseif ($rainpast>1000) $pomppauze=7990;
	else $pomppauze=10790;

	lg('Pomp pauze = '.$pomppauze.', maxtemp = '.$d['minmaxtemp']['m'].'°C, rainpast = '.$rainpast);
	if ($d['regenpomp']['s']=='Off'&&past('regenpomp')>=$pomppauze) {
		sw('regenpomp', 'On', basename(__FILE__).':'.__LINE__);
	}
} 

// Eerste blok voor zwembad
/*if ($d['zwembadfilter']['s']=='On') {
	if (past('zwembadfilter')>10700
		&&TIME>strtotime("16:00")
		&&$d['zwembadwarmte']['s']=='Off'
		&&$d['buiten_temp']['s']<27
	) {
		sw('zwembadfilter','Off', basename(__FILE__).':'.__LINE__);
	}
}else{
	if (
			(past('zwembadfilter')>10700&&TIME>strtotime("12:59")&&TIME<strtotime("15:59"))
			||
			(past('zwembadfilter')>10700&&$d['buiten_temp']['s']>27)
	   ) {
	   	sw('zwembadfilter','On', basename(__FILE__).':'.__LINE__);
	}
}
if ($d['zwembadwarmte']['s']=='On') {
	if (past('zwembadwarmte')>86398) {
		sw('zwembadwarmte','Off', basename(__FILE__).':'.__LINE__);
	}
	if ($d['zwembadfilter']['s']=='Off') {
		sw('zwembadfilter','On', basename(__FILE__).':'.__LINE__);
	}
}*/

// Tweede blok indien geen zwembad
if ($d['achterdeur']['s']=='Open') {
	if ($d['zwembadfilter']['s']=='Off') sw('zwembadfilter','On', basename(__FILE__).':'.__LINE__);
	if ($d['zwembadwarmte']['s']=='Off') sw('zwembadwarmte','On', basename(__FILE__).':'.__LINE__);
} else {
	if ($d['zwembadfilter']['s']=='On') sw('zwembadfilter','Off', basename(__FILE__).':'.__LINE__);
	if ($d['zwembadwarmte']['s']=='On') sw('zwembadwarmte','Off', basename(__FILE__).':'.__LINE__);
}
$items=array('buiten_temp', 'living_temp', 'badkamer_temp', 'kamer_temp', 'tobi_temp', 'alex_temp', 'zolder_temp');
foreach ($items as $i) {
    if (past($i)>900) {
        storeicon($i, '', basename(__FILE__).':'.__LINE__);
    }
}
foreach ($items as $i) {
	if ($d[$i]['m']==1&&past($i)>21600) {
		storemode($i, 0, basename(__FILE__).':'.__LINE__);
	}
}
if ($d['auto']['s']!='On') {
	if (past('auto')>10795) {
		sw('auto', 'On', basename(__FILE__).':'.__LINE__);
	}
}
if (past('Weg')>14400
	&& $d['Weg']['s']==0
	&& past('pirliving')>14400
	&& past('pirkeuken')>14400
	&& past('pirinkom')>14400
	&& past('pirhall')>14400
	&& past('pirgarage')>14400
) {
	store('Weg', 1, basename(__FILE__).':'.__LINE__);
	telegram('Slapen ingeschakeld na 4 uur geen beweging', false, 2);
} elseif (past('Weg')>36000
	&& $d['Weg']['s']==1
	&& past('pirliving')>36000
	&& past('pirkeuken')>36000
	&& past('pirinkom')>36000
	&& past('pirhall')>36000
	&& past('pirgarage')>36000
) {
	store('Weg', 2, basename(__FILE__).':'.__LINE__);
	telegram('Weg ingeschakeld na 10 uur geen beweging', false, 2);
}
if ($d['zolderg']['s']=='On'&&past('zolderg')>7200&&past('pirgarage')>7200) sw('zolderg', 'Off', basename(__FILE__).':'.__LINE__);
$items=array('Rliving', 'Rbureel', 'RkeukenL', 'RkeukenR');
foreach ($items as $i) {
	if (past($i)>10800&&$d[$i]['m']!=0) {
		storemode($i, 0, basename(__FILE__).':'.__LINE__);
	}
}
if ($d['bose103']['s']=='On'&&$d['Weg']['s']==1) {
    $nowplaying=json_decode(
        json_encode(
            simplexml_load_string(
                file_get_contents('http://192.168.2.103:8090/now_playing')
            )
        ),
        true
    );
    if (!empty($nowplaying)) {
        if (isset($nowplaying['@attributes']['source'])) {
            if ($nowplaying['@attributes']['source']!='STANDBY') {
                $volume=json_decode(
                    json_encode(
                        simplexml_load_string(
                            file_get_contents("http://192.168.2.103:8090/volume")
                        )
                    ),
                    true
                );
                $cv=$volume['actualvolume']-1;
                if ($cv<=5) {
                    bosekey("POWER", 0, 103);
                    sw('bose103', 'Off', basename(__FILE__).':'.__LINE__);
                } else {
                    bosevolume($cv, 103);
                }
            } else {
                sw('bose103', 'Off', basename(__FILE__).':'.__LINE__);
            }
        }
    }
}
if ($d['picam2plug']['s']=='On'&&past('picam2plug')>7200) {
    file_get_contents('http://192.168.2.12/fifo_command.php?cmd=halt');
    sleep(1);
    file_get_contents('http://192.168.2.12/fifo_command.php?cmd=halt');
    sleep(10);
    sw('picam2plug', 'Off', basename(__FILE__).':'.__LINE__);
}