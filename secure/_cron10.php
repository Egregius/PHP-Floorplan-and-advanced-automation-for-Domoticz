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
	if ($d['pirinkom']['s']=='Off'&&$d['deurvoordeur']['s']=='Closed'&&$d['inkom']['s']>0&&past('inkom')>$i&&past('pirinkom')>$i&&past('deurwc')>$i&&past('deurinkom')>$i&&past('deurbadkamer')>15&&past('deurvoordeur')>45) {
		foreach (array(29,28,27,26,0) as $i) {
			if ($d['inkom']['s']>$i) {
				sl('inkom', $i, basename(__FILE__).':'.__LINE__);
				break;
			}
		}
	}
	$i=5;
	if ($d['pirhall']['s']=='Off'&&$d['hall']['s']>0&&past('hall')>$i&&past('pirhall')>$i&&past('deurbadkamer')>$i&&past('deurkamer')>$i&&past('deurwaskamer')>$i&&past('deuralex')>$i) {
		foreach (array(29,28,27,26,0) as $i) {
			if ($d['hall']['s']>$i) {
				sl('hall', $i, basename(__FILE__).':'.__LINE__);
				break;
			}
		}
	}
	if ($d['Media']=='On'&&$time>strtotime('19:00')) $i=5;
	else $i=35;
	if ($d['pirkeuken']['s']=='Off'&&$d['snijplank']['s']==0&&$d['wasbak']['s']>0&&$d['wasbak']['s']<=25&&past('wasbak')>$i) {
		foreach (array(5,0) as $i) {
			if ($d['wasbak']['s']>$i) {
				sl('wasbak', $i, basename(__FILE__).':'.__LINE__);
				sleep(1);
				sl('wasbak', $i, basename(__FILE__).':'.__LINE__);
				break;
			}
		}
	}
	if ($d['sirene']['s']=='On'&&past('sirene')>110) sw('sirene', 'Off', basename(__FILE__).':'.__LINE__);
}
if ($d['deurvoordeur']['s']=='Closed'&&$d['voordeur']['s']=='On'&&past('voordeur')>59) sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__);

if ($d['GroheRed']['s']=='On'&&$d['el']['s']>7200) sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
if ($d['water']['s']=='On'&&past('water')>=$d['water']['m']) sw('water', 'Off');
if ($d['regenpomp']['s']=='On'&&past('regenpomp')>50) sw('regenpomp', 'Off', basename(__FILE__).':'.__LINE__);
elseif ($d['regenpomp']['s']=='Off'&&past('regenpomp')>1798) sw('regenpomp', 'On', basename(__FILE__).':'.__LINE__);
