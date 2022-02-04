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
$user='cron10  ';
if ($d['auto']['s']=='On') {
	if ($d['pirgarage']['s']=='Off'&&past('pirgarage')>120&&past('poort')>90&&past('deurgarage')>90&&past('garageled')>120&&$d['garageled']['s']=='On') sw('garageled', 'Off', basename(__FILE__).':'.__LINE__);
	elseif ($d['pirgarage']['s']=='On'&&$d['garageled']['s']=='Off'&&$d['garage']['s']=='Off'&&$d['zon']['s']==0) sw('garageled', 'On', basename(__FILE__).':'.__LINE__);
	if ($d['pirgarage']['s']=='Off'&&past('pirgarage')>120&&past('poort')>90&&past('deurgarage')>90&&past('garage')>240&&$d['garage']['s']=='On') sw('garage', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['pirzolder']['s']=='Off'&&$d['zolderg']['s']=='On'&&past('pirzolder')>120&&past('zolderg')>120) sw('zolderg', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['pirinkom']['s']=='Off'&&$d['inkom']['s']>0&&past('inkom')>12&&past('pirinkom')>30&&past('deurwc')>30&&past('deurinkom')>30&&past('deurvoordeur')>30) {
		foreach (array(30,28,26,0) as $i) {
			if ($d['inkom']['s']>$i) {
				sl('inkom', $i, basename(__FILE__).':'.__LINE__);
				break;
			}
		}
	} else {
		if ($d['pirinkom']['s']=='On'&&$d['zon']['s']==0) finkom();
	}
	if ($d['pirhall']['s']=='Off'&&$d['hall']['s']>0&&past('hall')>12&&past('pirhall')>30&&past('deurbadkamer')>30&&past('deurkamer')>30&&past('deurspeelkamer')>30&&past('deuralex')>30) {
		foreach (array(30,28,26,0) as $i) {
			if ($d['hall']['s']>$i) {
				sl('hall', $i, basename(__FILE__).':'.__LINE__);
				break;
			}
		}
	} else {
		if ($d['pirhall']['s']=='On'&&$d['zon']['s']==0) fhall();
	}
	if (past('pirkeuken')>40&&past('wasbak')>40&&past('keuken')>40&&$d['pirkeuken']['s']=='Off') {
		if ($d['wasbak']['m']==0&&$d['keuken']['s']=='On') sw('keuken', 'Off', basename(__FILE__).':'.__LINE__);
		if ($d['wasbak']['m']>0) sl('wasbak', $d['wasbak']['m'], basename(__FILE__).':'.__LINE__);
		if ($d['bose101']['m']==1&&$d['bose102']['s']=='On'&&$d['wasbak']['s']==0&&$d['keuken']['s']=='Off'&&$d['kookplaatpower']['s']=='Off'&&past('bose102')>90&&past('pirkeuken')>10800) {
			sw('bose102', 'Off', basename(__FILE__).':'.__LINE__);
			bosekey('POWER', 0, 102);
		}
	}
	if ($d['sirene']['s']=='On'&&past('sirene')>110) sw('sirene', 'Off', basename(__FILE__).':'.__LINE__);
}
if ($d['deurvoordeur']['s']=='Closed'&&$d['voordeur']['s']=='On'&&past('deurvoordeur')>60&&past('voordeur')>55) sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__);

if ($d['tv']['s']=='On') {
	if (pingport('192.168.2.6', 3000)==1) {
		if ($d['lgtv']['s']!='On') sw('lgtv', 'On', basename(__FILE__).':'.__LINE__);
		apcu_store('lgtv-offline', 0);
		if ($d['nvidia']['s']!='On'&&past('nvidia')>30	&&$d['Weg']['s']==0) {
			sw('nvidia', 'On', basename(__FILE__).':'.__LINE__);
			if ($d['denon']['s']!='On'&&past('denon')>30&&$d['Weg']['s']==0) sw('denon', 'On', basename(__FILE__).':'.__LINE__);
			if ($d['kenwood']['s']!='On'&&past('kenwood')>30&&$d['Weg']['s']==0) sw('kenwood', 'On', basename(__FILE__).':'.__LINE__);
		}
		$zonop=($d['civil_twilight']['s']+$d['Sun']['s'])/2;
		$zononder=($d['civil_twilight']['m']+$d['Sun']['m'])/2;

		if ($d['auto']['s']=='On'&&$d['kristal']['s']=='Off'&&$d['zon']['s']==0&&(TIME<$zonop||TIME>$zononder)&&past('kristal')>3600) sw('kristal', 'On', basename(__FILE__).':'.__LINE__);
	} else {
		if ($d['lgtv']['s']=='On') apcu_inc('lgtv-offline');
	}
	if (apcu_fetch('lgtv-offline')>=3) {
		if ($d['lgtv']['s']!='Off'&&past('lgtv')>900) {
			sw('lgtv', 'Off', basename(__FILE__).':'.__LINE__);
			if ($d['auto']['s']=='On'&&$d['jbl']['s']=='Off'&&$d['zon']['s']==0) sw('jbl', 'On', basename(__FILE__).':'.__LINE__);
		}
		if ($d['nvidia']['s']!='Off'&&past('lgtv')>900&&past('nvidia')>900) {
			sw('nvidia', 'Off', basename(__FILE__).':'.__LINE__);
			if ($d['denon']['s']!='Off'&&past('denon')>900) sw('denon', 'Off', basename(__FILE__).':'.__LINE__);
			if ($d['kenwood']['s']!='Off'&&past('kenwood')>900) sw('kenwood', 'Off', basename(__FILE__).':'.__LINE__);
		}
		if ($d['kristal']['s']!='Off'&&past('lgtv')>900&&past('kristal')>900) 	sw('kristal', 'Off', basename(__FILE__).':'.__LINE__);
		apcu_store('lgtv-offline', 0);
	}
}

if ($d['nvidia']['s']=='On') {
	if (pingport($shieldip, 9080)==1) {
		if ($d['nvidia']['m']=='Off') 	storemode('nvidia', 'On', basename(__FILE__).':'.__LINE__);
	} else {
		if ($d['nvidia']['m']=='On') 	storemode('nvidia', 'Off', basename(__FILE__).':'.__LINE__);
	}
}
/*
// BOSE BUITEN
if (pingport('192.168.2.105', 80)==1) {
	sleep(1);
	if (pingport('192.168.2.105', 80)==1) {
		if ($d['bose101']['m']==1&&$d['achterdeur']['s']=='Open') {
			$status=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.105:8090/now_playing"))), true);
			if (!empty($status)) {
				if ($d['bose105']['m']!='Online') {
					storemode('bose105', 'Online', basename(__FILE__).':'.__LINE__, 10);
					bosekey('SHUFFLE_ON', 0, 101);
				}
				if (isset($status['@attributes']['source'])) {
					if ($status['@attributes']['source']=='STANDBY') {
						bosezone(105);
						sw('bose105', 'On', basename(__FILE__).':'.__LINE__);
						bosekey('SHUFFLE_ON', 0, 101);
					}
				}
			}
		}
	}
} else {
	sleep(1);
	if (pingport('192.168.2.105', 80)!=1&&$d['bose105']['m']!='Offline') {
		storemode('bose105', 'Offline', basename(__FILE__).':'.__LINE__, 10);
		sw('bose105', 'Off', basename(__FILE__).':'.__LINE__);
	}
}
// END BOSE BUITEN
*/
// BOSE Badkamer
if (pingport('192.168.2.105', 80)==1) {
	sleep(1);
	if (pingport('192.168.2.105', 80)==1) {
		if ($d['bose101']['m']==1&&$d['achterdeur']['s']=='Open') {
			$status=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.105:8090/now_playing"))), true);
			if (!empty($status)) {
				if ($d['bose105']['m']!='Online') {
					storemode('bose105', 'Online', basename(__FILE__).':'.__LINE__, 10);
				}
				if (isset($status['@attributes']['source'])) {
					if ($status['@attributes']['source']=='STANDBY') {
						bosekey('PRESET_4', 0, 105);
						sw('bose105', 'On', basename(__FILE__).':'.__LINE__);
					}
				}
			}
		}
	}
} else {
	sleep(1);
	if (pingport('192.168.2.105', 80)!=1&&$d['bose105']['m']!='Offline') {
		storemode('bose105', 'Offline', basename(__FILE__).':'.__LINE__, 10);
		sw('bose105', 'Off', basename(__FILE__).':'.__LINE__);
	}
}
// END BOSE Badkamer
/*if ($d['daikin']['s']=='On') {
	if ($d['daikinliving']['m']==3||$d['daikinkamer']['m']==3||$d['daikinalex']['m']==3) {$rgb=230;$mode=3;}
	elseif ($d['daikinliving']['m']==4||$d['daikinkamer']['m']==4||$d['daikinalex']['m']==4) {$rgb=1;$mode=4;}
	elseif ($d['daikinliving']['m']==2||$d['daikinkamer']['m']==2||$d['daikinalex']['m']==2) {$rgb=56;$mode=2;}
	else $rgb=false;
	if ($rgb!=false) {
		$level=explode(';', $d['daikin_kWh']['s']);
		$level=$level[0];
		$level=round($level/25);
		if ($level>100) $level=80;
		elseif ($level<1) $level=1;
		if ($d['Xlight']['s']!=$level) {
			if ($d['Weg']['s']==0) {
				rgb('Xlight', $rgb, $level);
				sl('Xlight', $level, basename(__FILE__).':'.__LINE__);
			}
		}
	} else {
		if ($d['Xlight']['s']>0) sw('Xlight', 'Off', basename(__FILE__).':'.__LINE__);
	}
}*/
if (past('wind')>86&&past('buiten_temp')>86&&past('buien')>86) require('_weather.php');
