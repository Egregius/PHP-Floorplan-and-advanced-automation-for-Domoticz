<?php
$user='cron10  ';
if ($d['auto']['s']=='On') {
	$i=40;
	if ($d['pirgarage']['s']=='Off'&&past('pirgarage')>$i&&past('poort')>$i&&past('deurgarage')>$i&&past('garageled')>$i&&$d['garageled']['s']=='On') sw('garageled', 'Off', basename(__FILE__).':'.__LINE__);
	elseif ($d['pirgarage']['s']=='On'&&$d['garageled']['s']=='Off'&&$d['garage']['s']=='Off'&&$d['zon']['s']==0) sw('garageled', 'On', basename(__FILE__).':'.__LINE__);
	$i=40;
	if ($d['pirgarage']['s']=='Off'&&past('pirgarage')>$i&&past('poort')>$i&&past('deurgarage')>$i&&past('garage')>$i&&$d['garage']['s']=='On') sw('garage', 'Off', basename(__FILE__).':'.__LINE__);
	$i=120;
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
	if ($d['lgtv']=='On'&&TIME>strtotime('19:00')) $i=5;
	else $i=35;
	if ($d['pirkeuken']['s']=='Off'&&$d['snijplank']['s']==0&&$d['wasbak']['s']<=25&&past('wasbak')>$i) {
		foreach (array(5,0) as $i) {
			if ($d['wasbak']['s']>$i) {
				sl('wasbak', $i, basename(__FILE__).':'.__LINE__);
				sleep(1);
				sl('wasbak', $i, basename(__FILE__).':'.__LINE__);
				break;
			}
		}
	}
	if ($d['GroheRed']['s']=='Off'&&$d['Weg']['s']==0&&TIME>=strtotime('10:00')&&TIME<=strtotime('19:00')) {
		if ($d['zon']['s']-$d['el']['s']>2200&&past('GroheRed')>175) {
			sw('GroheRed', 'On', basename(__FILE__).':'.__LINE__);
			storemode('GroheRed', 'Zon', basename(__FILE__).':'.__LINE__);
		}
	} elseif ($d['GroheRed']['s']=='On'&&past('GroheRed')>175) {
		if ($d['GroheRed']['m']=='Zon'&&$d['zon']['s']-$d['el']['s']<-200) {
			sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
			storemode('GroheRed', '', basename(__FILE__).':'.__LINE__);
		}
	}
	if ($d['sirene']['s']=='On'&&past('sirene')>110) sw('sirene', 'Off', basename(__FILE__).':'.__LINE__);
}
$i=50;
if ($d['deurvoordeur']['s']=='Closed'&&$d['voordeur']['s']=='On'&&past('deurvoordeur')>$i&&past('voordeur')>$i) sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__);

if ($d['tv']['s']=='On') {
	if (ping('192.168.2.6')==true) {
		if ($d['lgtv']['s']=='Off') sw('lgtv', 'On', basename(__FILE__).':'.__LINE__);
		apcu_store('lgtv-offline', 0);
		if ($d['nvidia']['s']!='On'&&past('nvidia')>30	&&$d['Weg']['s']==0) {
			sw('nvidia', 'On', basename(__FILE__).':'.__LINE__);
			if ($d['sony']['s']!='On'&&past('sony')>30) sw('sony', 'On', basename(__FILE__).':'.__LINE__);
		}
		$zonop=($d['civil_twilight']['s']+$d['Sun']['s'])/2;
		$zononder=($d['civil_twilight']['m']+$d['Sun']['m'])/2;
		if ($d['auto']['s']=='On'&&$d['kristal']['s']=='Off'&&$d['zon']['s']==0&&(TIME<$zonop||TIME>$zononder)&&past('kristal')>3600) sw('kristal', 'On', basename(__FILE__).':'.__LINE__);
		if ($d['nas']['s']=='Off') shell_exec('/var/www/html/secure/wakenas.sh &');
	} else {
		if ($d['lgtv']['s']=='On') apcu_inc('lgtv-offline');
	}
	if (apcu_fetch('lgtv-offline')>=30) {
		if ($d['lgtv']['s']!='Off'&&past('lgtv')>900) {
			sw('lgtv', 'Off', basename(__FILE__).':'.__LINE__);
			if ($d['auto']['s']=='On'&&$d['lamp kast']['s']=='Off'&&$d['zon']['s']==0&&$d['Weg']['s']==0) sw('lamp kast', 'On', basename(__FILE__).':'.__LINE__);
		}
		if ($d['nvidia']['s']!='Off'&&past('lgtv')>900&&past('nvidia')>900) {
			sw('nvidia', 'Off', basename(__FILE__).':'.__LINE__);
			if ($d['sony']['s']!='Off'&&past('sony')>900) sw('sony', 'Off', basename(__FILE__).':'.__LINE__);
		}
		if ($d['kristal']['s']!='Off'&&past('lgtv')>900&&past('kristal')>900) 	sw('kristal', 'Off', basename(__FILE__).':'.__LINE__);
		apcu_store('lgtv-offline', 0);
	}
}
if ($d['GroheRed']['s']=='On'&&$d['el']['s']>7200) sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
$ctx=stream_context_create(array('http'=>array('timeout' =>1)));
foreach(array(102=>35,103=>18,104=>35,105=>35,106=>35,107=>30) as $ip=>$vol) {
	$status=@file_get_contents("http://192.168.2.$ip:8090/now_playing", false, $ctx);
	$status=json_decode(json_encode(simplexml_load_string($status)), true);
	if (isset($status['@attributes']['source'])) {
		if ($d['bose'.$ip]['icon']!='Online') storeicon('bose'.$ip, 'Online', basename(__FILE__).':'.__LINE__);
		if (isset($status['@attributes']['source'])&&$status['@attributes']['source']=='STANDBY'&&$d['bose101']['m']==1) {
			if ($ip==103) {
				if ($d['bose101']['s']=='On') bosezone($ip, true);
				elseif ($d['bose103']['m']==0&&TIME>strtotime('20:00')) {
					bosekey('PRESET_5', 0, $ip);
					storemode('bose101', 0);
				}
				bosevolume($vol, $ip);
			} else {
				bosezone($ip);
				bosevolume($vol, $ip);
			}
		}
		if (isset($status['playStatus'])&&$status['playStatus']=='PLAY_STATE') {
			if ($d['bose'.$ip]['s']=='Off') sw('bose'.$ip, 'On', basename(__FILE__).':'.__LINE__);
		} elseif (isset($status['@attributes']['source'])&&$status['@attributes']['source']=='STANDBY') {
			if ($d['bose'.$ip]['s']=='On') sw('bose'.$ip, 'Off', basename(__FILE__).':'.__LINE__);
		}
	} else {
		if ($d['bose'.$ip]['icon']!='Offline') storeicon('bose'.$ip, 'Offline', basename(__FILE__).':'.__LINE__);
		if ($d['bose'.$ip]['s']=='On') sw('bose'.$ip, 'Off', basename(__FILE__).':'.__LINE__);
		if ($ip==103&&$d['bose101']['m']==0&&TIME<strtotime('8:30')) storemode('bose101', 1);
	}
	unset($status);
}
foreach(array(101/*,105*/) as $ip) {
	$status=@file_get_contents("http://192.168.2.$ip:8090/now_playing", false, $ctx);
	if ($ip==101&&$status=='<?xml version="1.0" encoding="UTF-8" ?><nowPlaying deviceID="587A6260C5B2" source="INVALID_SOURCE"><ContentItem source="INVALID_SOURCE" isPresetable="true" /></nowPlaying>') bosekey('PRESET_5', 0, $ip);
	$status=json_decode(json_encode(simplexml_load_string($status)), true);
	if (isset($status['@attributes']['source'])) {
		if ($d['bose'.$ip]['icon']!='Online') storeicon('bose'.$ip, 'Online', basename(__FILE__).':'.__LINE__);
		if ($ip==101&&isset($status['@attributes']['source'],$status['shuffleSetting'])&&$status['@attributes']['source']=='SPOTIFY'&&$status['shuffleSetting']!='SHUFFLE_ON') {
			bosekey('SHUFFLE_ON', 0, $ip);
		}
		if (isset($status['playStatus'])&&$status['playStatus']=='PLAY_STATE') {
			if ($d['bose'.$ip]['s']=='Off') sw('bose'.$ip, 'On', basename(__FILE__).':'.__LINE__);
		} elseif (isset($status['@attributes']['source'])&&$status['@attributes']['source']=='STANDBY') {
			if ($d['bose'.$ip]['s']=='On') sw('bose'.$ip, 'Off', basename(__FILE__).':'.__LINE__);
		}
	} else {
		if ($d['bose'.$ip]['icon']!='Offline') storeicon('bose'.$ip, 'Offline', basename(__FILE__).':'.__LINE__);
		if ($d['bose'.$ip]['s']=='On') sw('bose'.$ip, 'Off', basename(__FILE__).':'.__LINE__);
	}
	unset($status);
}

if (past('wind')>86&&past('buiten_temp')>86&&past('buien')>86) require('_weather.php');
