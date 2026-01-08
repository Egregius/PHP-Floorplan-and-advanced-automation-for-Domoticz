<?php
$user='cron10';
//lg($user);
if ($d['auto']['s']=='On') {
	$i=39;
	if ($d['pirgarage']['s']=='Off'&&$d['pirgarage2']['s']=='Off'&&past('pirgarage')>$i&&past('pirgarage2')>$i&&past('deurgarage')>$i&&past('garageled')>$i) {
		if ($d['garageled']['s']=='On') sw('garageled', 'Off');
		if ($d['garageled']['m']!=0) {
			storemode('garageled',0);
			setBatterijLedBrightness(0);
		}
	}
	$i=119;
	if ($d['garage']['s']=='On'&&$d['pirgarage']['s']=='Off'&&$d['pirgarage2']['s']=='Off'&&past('pirgarage')>$i&&past('pirgarage2')>$i&&past('deurgarage')>$i&&past('garage')>$i) sw('garage', 'Off');
	$i=119;
	if ($d['pirzolderg']['s']=='Off'&&$d['zolderg']['s']=='On'&&past('pirzolderg')>$i&&past('zolderg')>$i) sw('zolderg', 'Off');
	$i=5;
	if ($d['weg']['s']==0&&$d['pirinkom']['s']=='Off'&&$d['deurvoordeur']['s']=='Closed'&&$d['inkom']['s']>0&&past('inkom')>$i&&past('pirinkom')>$i&&past('deurwc')>12&&past('deurinkom')>12&&past('deurbadkamer')>25&&past('deurvoordeur')>45) {
		foreach (array(29,27,0) as $i) {
			if ($d['inkom']['s']>$i) {
				sl('inkom', $i);
				break;
			}
		}
	}
	$i=5;
	if ($d['weg']['s']==0&&$d['pirhall']['s']=='Off'&&$d['hall']['s']>0&&past('hall')>$i&&past('pirhall')>$i&&past('deurbadkamer')>$i&&past('deurkamer')>$i&&past('deurwaskamer')>$i&&past('deuralex')>$i) {
		foreach (array(29,27,0) as $i) {
			if ($d['hall']['s']>$i) {
					sl('hall', $i);
				break;
			}
		}
	}
	if (1==2) { // Lichten aan laten bij feestjes
		$i=29;
		if ($d['pirkeuken']['s']=='Off'&&$d['wasbak']['s']>0&&$d['wasbak']['s']<=25&&past('wasbak')>$i) {
			foreach (array(8,7,6,5,4,3,2,1) as $i) {
				if ($d['wasbak']['s']>$i) {
					sl('wasbak', $i);
					break;
				}
			}
		}
		$i=29;
		if ($d['pirkeuken']['s']=='Off'&&$d['snijplank']['s']>0&&$d['snijplank']['s']<=25&&past('snijplank')>$i) {
			foreach (array(8,7,6,5,4,3,2,1) as $i) {
				if ($d['snijplank']['s']>$i) {
					sl('snijplank', $i);
					break;
				}
			}
		}
	} else {
		$i=25;
		if ($d['lgtv']['s']=='On'&&$d['nvidia']['s']=='Playing'&&$time>strtotime('20:30')&&!in_array($d['nvidia']['m'],['De.ozerov.fully']))  $i=5;

//		lg($d['wasbak']['s']);
		if ($d['wasbak']['s']>25) {
			if ($d['pirkeuken']['s']=='On') $i=300;
			elseif ($d['pirkeuken']['s']=='Off') $i=150;
		}
		if ($d['pirkeuken']['s']=='Off'&&$d['snijplank']['s']==0&&$d['wasbak']['s']>0&&past('wasbak')>$i) {
			foreach (array(5,0) as $y) {
//				lg(basename(__FILE__).':'.__LINE__.'	'.$d['wasbak']['s'].' '.$i);
				if ($d['wasbak']['s']>$y) {
					sl('wasbak', $y);
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
	if ($d['sirene']['s']=='On'&&past('sirene')>110) sw('sirene', 'Off');
//	if ($d['ralex']['s']==0&&$d['z']>100&&$d['alex']['s']==1) sl('alex', 0);
//	elseif ($d['ralex']['s']==100&&$d['weg']['s']==1&&$d['alex']['s']==1&&$d['deuralex']['s']=='Closed'&&past('alex')>590) sl('alex', 0);

// KERSTMIS
	if($d['weg']['s']==0&&$d['dag']['s']<1) {
		if($d['rliving']['s']==0&&$d['tuintafel']['s']=='Off') sw('tuintafel','On');
//		if($d['lampkast']['s']=='Off'&&$d['media']['s']=='Off'&&$time>=strtotime('7:00')&&$time<=strtotime('20:00')) sw('lampkast','On');
	} elseif($d['dag']['s']>1&&$d['tuintafel']['s']=='On') sw('tuintafel','Off');

/*	if ($d['kookplaat']['s']=='On'&&$d['wasbak']['s']==0&&$d['snijplank']['s']==0) {
		if ($d['kookplaat_power']['s']<125&&past('kookplaat_power')>200&&past('kookplaat')>200) sw('kookplaat', 'Off', basename(__FILE__).':'.__LINE__);
	}*/
}
    if ($d['deurvoordeur']['s']=='Closed'&&$d['voordeur']['s']=='On') {
    	$past=past('voordeur');
    	$pastweg=past('weg');
    	if ($d['weg']['s']==0&&$d['dag']['s']>0&&$past>5&&$pastweg>5) sw('voordeur', 'Off');
    	elseif ($d['weg']['s']==0&&$past>55&&$pastweg>180) sw('voordeur', 'Off');
		elseif ($d['weg']['s']>0&&$past>55&&$pastweg>120) sw('voordeur', 'Off');
	}
if ($d['weg']['s']<2&&$d['n']<-1200&&$d['b']>0&&$d['grohered']['s']=='Off') sw('grohered', 'On', ' n='.$d['n'].'W', true);
elseif ($d['grohered']['s']=='On'&&past('8keuken_8')>1800&&$d['n']>100) sw('grohered', 'Off', ' n='.$d['n'].'W',true);

if ($d['regenpomp']['s']=='On'&&past('regenpomp')>50) sw('regenpomp', 'Off');

if ($d['water']['s']=='On'&&past('water')>=$d['water']['m']) sw('water', 'Off');