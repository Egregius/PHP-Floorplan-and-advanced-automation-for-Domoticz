<?php
$t=t();
$user=basename(__FILE__);
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
		$i=29;
		if ($d['pirkeuken']['s']=='Off'&&$d['snijplank']['s']>0&&$d['snijplank']['s']<=25&&past('snijplank')>$i) {
			foreach (array(8,7,6,5,4,3,2,1) as $i) {
				if ($d['snijplank']['s']>$i) {
					sl('snijplank', $i, basename(__FILE__).':'.__LINE__);
					break;
				}
			}
		}
	} else {
		if ($d['lgtv']['s']=='On'&&$time>strtotime('19:00')) $i=5;
		else $i=35;
		if ($d['pirkeuken']['s']=='Off'&&$d['snijplank']['s']==0&&$d['wasbak']['s']>0&&$d['wasbak']['s']<=25&&past('wasbak')>$i) {
			foreach (array(5,0) as $i) {
				if ($d['wasbak']['s']>$i) {
					sl('wasbak', $i, basename(__FILE__).':'.__LINE__);
/*					if ($i==0) {
						sl('wasbak', 0, basename(__FILE__).':'.__LINE__);
						sleep(2);
						sl('wasbak', 0, basename(__FILE__).':'.__LINE__);
					}*/
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
$pastgrohe=past('GroheRed');
if ($d['Weg']['s']<2&&$d['net']<-1000&&$d['GroheRed']['s']=='Off') sw('GroheRed', 'On', basename(__FILE__).':'.__LINE__.' '.$d['net'].'W Zonne-energie over', true);
elseif ($d['GroheRed']['s']=='On'&&$pastgrohe>3600&&past('$ 8keuken8')>1800&&$d['net']>0) sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);

if ($d['powermeter']['s']=='On'&&($d['avg']>$d['powermeter']['m']||$d['Weg']['s']>=2)) {
	sw('powermeter', 'Off', basename(__FILE__).':'.__LINE__.' Auto laden uit, te veel verbruik');
	storemode('powermeter', 0, basename(__FILE__).':'.__LINE__);
} elseif ($d['Weg']['s']==0&&$d['powermeter']['s']=='Off'&&$d['avg']<100&&$d['net']<-2500&&$d['GroheRed']['s']=='On') {
	mset('powermeter',$time);
	storemode('powermeter', 500, basename(__FILE__).':'.__LINE__);
	sw('powermeter', 'On', basename(__FILE__).':'.__LINE__.' Te veel verbruik');
}
if ($d['water']['s']=='On'&&past('water')>=$d['water']['m']) sw('water', 'Off');

if ($d['Weg']['m']==2) {
	lg('Stopping CRON Loop...');
	$db->query("UPDATE devices SET m=0 WHERE n ='Weg';");
	exit('Stop');
	die('Stop');
}