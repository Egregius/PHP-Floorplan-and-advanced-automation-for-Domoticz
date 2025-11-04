<?php
$user='cron10';
//lg($user);
if ($d['auto']['s']=='On') {
	$i=39;
	if ($d['pirgarage']['s']=='Off'&&$d['pirgarage2']['s']=='Off'&&past('pirgarage')>$i&&past('pirgarage2')>$i&&past('deurgarage')>$i&&past('garageled')>$i) {
		if ($d['garageled']['s']=='On') sw('garageled', 'Off', basename(__FILE__).':'.__LINE__);
		if ($d['garageled']['m']!=0) {
			storemode('garageled',0);
			setBatterijLedBrightness(0);
		}
	}
	$i=119;
	if ($d['garage']['s']=='On'&&$d['pirgarage']['s']=='Off'&&$d['pirgarage2']['s']=='Off'&&past('pirgarage')>$i&&past('pirgarage2')>$i&&past('deurgarage')>$i&&past('garage')>$i) sw('garage', 'Off', basename(__FILE__).':'.__LINE__);
	$i=119;
	if ($d['pirzolderg']['s']=='Off'&&$d['zolderg']['s']=='On'&&past('pirzolderg')>$i&&past('zolderg')>$i) sw('zolderg', 'Off', basename(__FILE__).':'.__LINE__);
	$i=5;
	if ($d['weg']['s']==0&&$d['pirinkom']['s']=='Off'&&$d['deurvoordeur']['s']=='Closed'&&$d['inkom']['s']>0&&past('inkom')>$i&&past('pirinkom')>$i&&past('deurwc')>12&&past('deurinkom')>12&&past('deurbadkamer')>25&&past('deurvoordeur')>45) {
		foreach (array(29,27,0) as $i) {
			if ($d['inkom']['s']>$i) {
				sl('inkom', $i, basename(__FILE__).':'.__LINE__);
				break;
			}
		}
	}
	$i=5;
	if ($d['weg']['s']==0&&$d['pirhall']['s']=='Off'&&$d['hall']['s']>0&&past('hall')>$i&&past('pirhall')>$i&&past('deurbadkamer')>$i&&past('deurkamer')>$i&&past('deurwaskamer')>$i&&past('deuralex')>$i) {
		foreach (array(29,27,0) as $i) {
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
		if ($d['lgtv']['s']=='On') $i=5;
		else $i=25;
		if ($d['wasbak']['s']>25) {
			if ($d['pirkeuken']['s']=='On') $i=300;
			elseif ($d['pirkeuken']['s']=='Off'&&past('pirkeuken')<300) $i=150;
		}
		if ($d['pirkeuken']['s']=='Off'&&$d['snijplank']['s']==0&&$d['wasbak']['s']>0&&past('wasbak')>$i) {
			foreach (array(5,0) as $i) {
//				lg(basename(__FILE__).':'.__LINE__.'	'.$d['wasbak']['s'].' '.$i);
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
	if ($d['ralex']['s']==0&&$d['z']>100&&$d['alex']['s']==1) sl('alex', 0, basename(__FILE__).':'.__LINE__);
	elseif ($d['ralex']['s']==100&&$d['weg']['s']==1&&$d['alex']['s']==1&&$d['deuralex']['s']=='Closed'&&past('alex')>590) sl('alex', 0, basename(__FILE__).':'.__LINE__);
	
/*	if ($d['kookplaat']['s']=='On'&&$d['wasbak']['s']==0&&$d['snijplank']['s']==0) {
		if ($d['kookplaat_power']['s']<125&&past('kookplaat_power')>200&&past('kookplaat')>200) sw('kookplaat', 'Off', basename(__FILE__).':'.__LINE__);
	}*/
}
    if ($d['deurvoordeur']['s']=='Closed'&&$d['voordeur']['s']=='On'&&$d['weg']['s']==0&&past('voordeur')>55&&past('weg')>300) sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__);
elseif ($d['deurvoordeur']['s']=='Closed'&&$d['voordeur']['s']=='On'&&$d['weg']['s']>0&&past('voordeur')>55) sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__);

if ($d['weg']['s']<2&&$d['n']<-1200&&$d['b']>0&&$d['grohered']['s']=='Off') sw('grohered', 'On', basename(__FILE__).':'.__LINE__.' net='.$d['net'].'W', true);
elseif ($d['grohered']['s']=='On'&&past('8keuken_8')>1800&&$d['n']>100) sw('grohered', 'Off', basename(__FILE__).':'.__LINE__.' net='.$d['net'].'W',true);

if ($d['regenpomp']['s']=='On'&&past('regenpomp')>50) sw('regenpomp', 'Off', basename(__FILE__).':'.__LINE__);

if ($d['water']['s']=='On'&&past('water')>=$d['water']['m']) sw('water', 'Off');