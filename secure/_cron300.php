<?php
/**
 * Pass2PHP
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
//lg(__FILE__.':'.$s);
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
	if ($rainpast>25000) $pomppauze=3600;
	elseif ($rainpast>22000) $pomppauze=5400;
	elseif ($rainpast>19000) $pomppauze=7200;
	elseif ($rainpast>16000) $pomppauze=10800;
	elseif ($rainpast>13000) $pomppauze=21600;
	elseif ($rainpast>10000) $pomppauze=43200;
	elseif ($rainpast>7000) $pomppauze=86400;
	elseif ($rainpast>3000) $pomppauze=129600;
	elseif ($rainpast>1000) $pomppauze=259200;
	else $pomppauze=2592000;
	if ($d['regenpomp']['s']=='Off'&&past('regenpomp')>=$pomppauze) {
		sw('regenpomp', 'On', basename(__FILE__).':'.__LINE__.' '.'Pomp pauze = '.$pomppauze.', maxtemp = '.$d['minmaxtemp']['m'].'°C, rainpast = '.$rainpast);
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

if (past('diepvries_temp')>7200) {
	alert(
		'diepvriestemp',
		'Diepvries temp not updated since '.
		strftime("%k:%M:%S", $d['diepvries_temp']['t']),
		7200
	);
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

if ($d['GroheRed']['m']>0&&$d['GroheRed']['s']=='On'&&past('GroheRed')>3600&&past('pirkeuken')>3600) {
	sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
	storemode('GroheRed', 0, basename(__FILE__).':'.__LINE__);
}
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
				if ($cv<=8) {
					bosekey("POWER", 0, 103);
					sw('bose103', 'Off', basename(__FILE__).':'.__LINE__);
				} else {
					bosevolume($cv, 103, basename(__FILE__).':'.__LINE__);
				}
			} else {
				sw('bose103', 'Off', basename(__FILE__).':'.__LINE__);
			}
		}
	}
}
$battery=apcu_fetch('ring-battery');

if ((TIME>=strtotime('11:00')&&TIME<strtotime('19:30'))||$battery<50) {
	if (($d['ringdoorbell']['s']=='Off'&&past('ringdoorbell')>28800)||($d['ringdoorbell']['s']=='Off'&&$battery<50)) sw('ringdoorbell', 'On', basename(__FILE__).':'.__LINE__.' battery='.$battery);
} elseif ((TIME<strtotime('6:00')||TIME>=strtotime('22:00')||$battery>=80)&&$battery>60) {
	if ($d['ringdoorbell']['s']=='On'&&past('ringdoorbell')>28800) sw('ringdoorbell', 'Off', basename(__FILE__).':'.__LINE__.' battery='.$battery);
}
