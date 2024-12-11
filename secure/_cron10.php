<?php
$d=fetchdata();
$user='cron10  ';
if ($d['auto']['s']=='On') {
	$i=39;
	if ($d['garageled']['s']=='On'&&$d['pirgarage']['s']=='Off'&&past('pirgarage')>$i&&past('deurgarage')>$i&&past('garageled')>$i) sw('garageled', 'Off', basename(__FILE__).':'.__LINE__);
	$i=119;
	if ($d['garage']['s']=='On'&&$d['pirgarage']['s']=='Off'&&past('pirgarage')>$i&&past('deurgarage')>$i&&past('garage')>$i) sw('garage', 'Off', basename(__FILE__).':'.__LINE__);
	$i=119;
	if ($d['pirzolder']['s']=='Off'&&$d['zolderg']['s']=='On'&&past('pirzolder')>$i&&past('zolderg')>$i) sw('zolderg', 'Off', basename(__FILE__).':'.__LINE__);
	$i=5;
	if ($d['Weg']['s']==0&&$d['pirinkom']['s']=='Off'&&$d['deurvoordeur']['s']=='Closed'&&$d['inkom']['s']>0&&past('inkom')>$i&&past('pirinkom')>$i&&past('deurwc')>12&&past('deurinkom')>12&&past('deurbadkamer')>25&&past('deurvoordeur')>45) {
		foreach (array(29,28,0) as $i) {
			if ($d['inkom']['s']>$i) {
				sl('inkom', $i, basename(__FILE__).':'.__LINE__);
				break;
			}
		}
	}
	$i=5;
	if ($d['Weg']['s']==0&&$d['pirhall']['s']=='Off'&&$d['hall']['s']>0&&past('hall')>$i&&past('pirhall')>$i&&past('deurbadkamer')>$i&&past('deurkamer')>$i&&past('deurwaskamer')>$i&&past('deuralex')>$i) {
		foreach (array(29,28,0) as $i) {
			if ($d['hall']['s']>$i) {
				sl('hall', $i, basename(__FILE__).':'.__LINE__);
				break;
			}
		}
	}
	if (1==2) { // Lichten aan laten bij feestjes
		$i=29;
		if ($d['pirkeuken']['s']=='Off'&&$d['wasbak']['s']>0&&$d['wasbak']['s']<=25&&past('wasbak')>$i) {
			foreach (array(8,7,6,5,4,3,2,1) as $i) {
				if ($d['wasbak']['s']>$i) {
					sl('wasbak', $i, basename(__FILE__).':'.__LINE__);
					break;
				}
			}
		}
		$i=39;
		if ($d['pirkeuken']['s']=='Off'&&$d['snijplank']['s']>0&&$d['snijplank']['s']<=25&&past('snijplank')>$i) {
			foreach (array(8,7,6,5,4,3,2,1) as $i) {
				if ($d['snijplank']['s']>$i) {
					sl('snijplank', $i, basename(__FILE__).':'.__LINE__);
					break;
				}
			}
		}
	} else {
		if ($d['Media']=='On'&&$time>strtotime('19:00')) $i=5;
		else $i=35;
		if ($d['pirkeuken']['s']=='Off'&&$d['snijplank']['s']==0&&$d['wasbak']['s']>0&&$d['wasbak']['s']<=25&&past('wasbak')>$i) {
			foreach (array(5,0) as $i) {
				if ($d['wasbak']['s']>$i) {
					sl('wasbak', $i, basename(__FILE__).':'.__LINE__);
					if ($i==0) {
						sl('wasbak', 0, basename(__FILE__).':'.__LINE__);
						sleep(2);
						sl('wasbak', 0, basename(__FILE__).':'.__LINE__);
					}
					break;
				}
			}
		}
	}
	if ($d['sirene']['s']=='On'&&past('sirene')>110) sw('sirene', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['Ralex']['s']==0&&$d['zon']>100&&$d['alex']['s']==1) sl('alex', 0, basename(__FILE__).':'.__LINE__);
	elseif ($d['Ralex']['s']==100&&$d['Weg']['s']==1&&$d['alex']['s']==1&&$d['deuralex']['s']=='Closed'&&past('alex')>590) sl('alex', 0, basename(__FILE__).':'.__LINE__);
}
if ($d['deurvoordeur']['s']=='Closed'&&$d['voordeur']['s']=='On'&&$d['Weg']['s']==0&&past('voordeur')>55&&past('Weg')>300) sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__);
elseif ($d['deurvoordeur']['s']=='Closed'&&$d['voordeur']['s']=='On'&&$d['Weg']['s']>0&&past('voordeur')>55) sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__);
if ($d['GroheRed']['s']=='On'&&$d['avg']>$d['GroheRed']['m']) {
	sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__.' Meer dan '.$d['GroheRed']['m'].'W verbruik');
	storemode('GroheRed', 0, basename(__FILE__).':'.__LINE__);
} elseif ($d['GroheRed']['s']=='On'&&past('GroheRed')>3600) {
	sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__.' Langer dan 60min aan');
	storemode('GroheRed', 0, basename(__FILE__).':'.__LINE__);
}
//elseif ($d['GroheRed']['s']=='On'&&past('GroheRed')>600&&past('watervandaag')>600) sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__.' Langer dan 10min geen water genomen');
if ($d['powermeter']['s']=='On'&&$d['avg']>$d['powermeter']['m']) {
	sw('powermeter', 'Off', basename(__FILE__).':'.__LINE__.' Te veel verbruik');
	storemode('powermeter', 0, basename(__FILE__).':'.__LINE__);
	telegram ('Auto laden uit, te veel verbruik');
} 
if ($d['water']['s']=='On'&&past('water')>=$d['water']['m']) sw('water', 'Off');


/*
if ($d['regenpomp']['s']=='On'&&past('regenpomp')>50) sw('regenpomp', 'Off', basename(__FILE__).':'.__LINE__);
elseif ($d['regenpomp']['s']=='Off'&&past('regenpomp')>1798) sw('regenpomp', 'On', basename(__FILE__).':'.__LINE__);
*/



if ($d['Weg']['m']==2) {
	lg('Stopping CRON Loop...');
	$db->query("UPDATE devices SET m=0 WHERE n ='Weg';");
	exit('Stop');
	die('Stop');
}