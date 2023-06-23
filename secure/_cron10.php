<?php
$d=fetchdata();
dag();
$user='cron10  ';
if ($d['auto']['s']=='On') {
	$i=39;
	if ($d['garageled']['s']=='On'&&$d['pirgarage']['s']=='Off'&&past('pirgarage')>$i&&past('poort')>$i&&past('deurgarage')>$i&&past('garageled')>$i) sw('garageled', 'Off', basename(__FILE__).':'.__LINE__);
	$i=39;
	if ($d['garage']['s']=='On'&&$d['pirgarage']['s']=='Off'&&past('pirgarage')>$i&&past('poort')>$i&&past('deurgarage')>$i&&past('garage')>$i) sw('garage', 'Off', basename(__FILE__).':'.__LINE__);
	$i=119;
	if ($d['pirzolder']['s']=='Off'&&$d['zolderg']['s']=='On'&&past('pirzolder')>$i&&past('zolderg')>$i) sw('zolderg', 'Off', basename(__FILE__).':'.__LINE__);
	$i=5;
	if ($d['pirinkom']['s']=='Off'&&$d['deurvoordeur']['s']=='Closed'&&$d['inkom']['s']>0&&past('inkom')>$i&&past('pirinkom')>$i&&past('deurwc')>$i&&past('deurinkom')>$i&&past('deurbadkamer')>15&&past('deurvoordeur')>$i) {
		foreach (array(24,0) as $i) {
			if ($d['inkom']['s']>$i) {
				sl('inkom', $i, basename(__FILE__).':'.__LINE__);
				break;
			}
		}
	} else {
		if ($d['pirinkom']['s']=='On'&&$d['zon']['s']==0) finkom();
	}
	$i=5;
	if ($d['pirhall']['s']=='Off'&&$d['hall']['s']>0&&past('hall')>$i&&past('pirhall')>$i&&past('deurbadkamer')>$i&&past('deurkamer')>$i&&past('deurwaskamer')>$i&&past('deuralex')>$i) {
		foreach (array(24,0) as $i) {
			if ($d['hall']['s']>$i) {
				sl('hall', $i, basename(__FILE__).':'.__LINE__);
				break;
			}
		}
	} else {
		if ($d['pirhall']['s']=='On'&&$d['zon']['s']==0) fhall();
	}
	if ($d['lgtv']=='On'&&$time>strtotime('19:00')) $i=5;
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
/*	if ($d['GroheRed']['s']=='Off'&&$d['Weg']['s']==0&&$time>=strtotime('10:00')&&$time<=strtotime('19:00')) {
		if ($d['zon']['s']-$d['el']['s']>2200&&past('GroheRed')>175) {
			sw('GroheRed', 'On', basename(__FILE__).':'.__LINE__);
			storemode('GroheRed', 'Zon', basename(__FILE__).':'.__LINE__);
		}
	} elseif ($d['GroheRed']['s']=='On'&&past('GroheRed')>175) {
		if ($d['GroheRed']['m']=='Zon'&&$d['zon']['s']-$d['el']['s']<-200) {
			sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
			storemode('GroheRed', '', basename(__FILE__).':'.__LINE__);
		}
	}*/
	if ($d['sirene']['s']=='On'&&past('sirene')>110) sw('sirene', 'Off', basename(__FILE__).':'.__LINE__);
}
$i=59;
if ($d['deurvoordeur']['s']=='Closed'&&$d['voordeur']['s']=='On'&&past('deurvoordeur')>$i&&past('voordeur')>$i) sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__);

if ($d['tv']['s']=='On') {
	if (ping('192.168.2.6')==true) {
		if ($d['lgtv']['s']=='Off') sw('lgtv', 'On', basename(__FILE__).':'.__LINE__);
		mset('lgtv-offline', 0);
		if ($d['nvidia']['s']!='On'&&past('nvidia')>30	&&$d['Weg']['s']==0) {
			sw('nvidia', 'On', basename(__FILE__).':'.__LINE__);
			if ($d['sony']['s']!='On'&&past('sony')>30) sw('sony', 'On', basename(__FILE__).':'.__LINE__);
		}
		$zonop=($d['civil_twilight']['s']+$d['Sun']['s'])/2;
		$zononder=($d['civil_twilight']['m']+$d['Sun']['m'])/2;
		if ($d['auto']['s']=='On'&&$d['kristal']['s']=='Off'&&$d['zon']['s']==0&&($time<$zonop||$time>$zononder)&&past('kristal')>3600) sw('kristal', 'On', basename(__FILE__).':'.__LINE__);
		if ($d['nas']['s']=='Off') shell_exec('/var/www/html/secure/wakenas.sh &');
	} else {
		if ($d['lgtv']['s']=='On') {
			mset('lgtv-offline',mget('lgtv-offline')+1);
		}
	}
	if (mget('lgtv-offline')>=30) {
		if ($d['lgtv']['s']!='Off'&&past('lgtv')>900) {
			sw('lgtv', 'Off', basename(__FILE__).':'.__LINE__);
			if ($d['auto']['s']=='On'&&$d['lamp kast']['s']=='Off'&&$d['zon']['s']==0&&$d['Weg']['s']==0) sw('lamp kast', 'On', basename(__FILE__).':'.__LINE__);
		}
		if ($d['nvidia']['s']!='Off'&&past('lgtv')>900&&past('nvidia')>900) {
			sw('nvidia', 'Off', basename(__FILE__).':'.__LINE__);
			if ($d['sony']['s']!='Off'&&past('sony')>900) sw('sony', 'Off', basename(__FILE__).':'.__LINE__);
		}
		if ($d['kristal']['s']!='Off'&&past('lgtv')>900&&past('kristal')>900) 	sw('kristal', 'Off', basename(__FILE__).':'.__LINE__);
		mset('lgtv-offline', 0);
	}
}
if ($d['GroheRed']['s']=='On'&&$d['el']['s']>7200) sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
if ($d['water']['s']=='On'&&past('water')>$d['water']['m']) sw('water', 'Off');
if ($d['regenpomp']['s']=='On'&&past('regenpomp')>50) sw('regenpomp', 'Off', basename(__FILE__).':'.__LINE__);
