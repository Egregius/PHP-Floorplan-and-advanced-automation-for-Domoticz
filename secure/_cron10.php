<?php
$d=fetchdata();
$user='cron10  ';
if ($d['auto']['s']=='On') {
	$i=39;
	if ($d['garageled']['s']=='On'&&$d['pirgarage']['s']=='Off'&&past('pirgarage')>$i&&past('poort')>$i&&past('deurgarage')>$i&&past('garageled')>$i) sw('garageled', 'Off', basename(__FILE__).':'.__LINE__);
	$i=119;
	if ($d['garage']['s']=='On'&&$d['pirgarage']['s']=='Off'&&past('pirgarage')>$i&&past('poort')>$i&&past('deurgarage')>$i&&past('garage')>$i) sw('garage', 'Off', basename(__FILE__).':'.__LINE__);
	$i=119;
	if ($d['pirzolder']['s']=='Off'&&$d['zolderg']['s']=='On'&&past('pirzolder')>$i&&past('zolderg')>$i) sw('zolderg', 'Off', basename(__FILE__).':'.__LINE__);
	$i=5;
	if ($d['pirinkom']['s']=='Off'&&$d['deurvoordeur']['s']=='Closed'&&$d['inkom']['s']>0&&past('inkom')>$i&&past('pirinkom')>$i&&past('deurwc')>12&&past('deurinkom')>12&&past('deurbadkamer')>25&&past('deurvoordeur')>45) {
		foreach (array(29,28,0) as $i) {
			if ($d['inkom']['s']>$i) {
				sl('inkom', $i, basename(__FILE__).':'.__LINE__);
				break;
			}
		}
	}
	$i=5;
	if ($d['pirhall']['s']=='Off'&&$d['hall']['s']>0&&past('hall')>$i&&past('pirhall')>$i&&past('deurbadkamer')>$i&&past('deurkamer')>$i&&past('deurwaskamer')>$i&&past('deuralex')>$i) {
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
}
if ($d['deurvoordeur']['s']=='Closed'&&$d['voordeur']['s']=='On'&&past('voordeur')>55) sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__);
if ($d['GroheRed']['s']=='On'&&$d['el']['s']>7500) sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__.' Meer dan 7500W verbruik');
elseif ($d['GroheRed']['s']=='On'&&past('GroheRed')>1800) sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__.' Langer dan 30min aan');
if ($d['powermeter']['s']=='On'&&$d['el']['s']>6500) {
	sw('powermeter', 'Off', basename(__FILE__).':'.__LINE__.' Te veel verbruik');
	telegram ('Auto laden uit, te veel verbruik');
} elseif ($d['powermeter']['s']=='On'&&past('powermeter')>9895) {
	sw('powermeter', 'Off', basename(__FILE__).':'.__LINE__.' Langer dan 2u45 bezig');
	telegram ('Auto laden uit, langer dan 2u45 bezig');
}
if ($d['water']['s']=='On'&&past('water')>=$d['water']['m']) sw('water', 'Off');
if ($d['regenpomp']['s']=='On'&&past('regenpomp')>50) sw('regenpomp', 'Off', basename(__FILE__).':'.__LINE__);
elseif ($d['regenpomp']['s']=='Off'&&past('regenpomp')>1798) sw('regenpomp', 'On', basename(__FILE__).':'.__LINE__);
