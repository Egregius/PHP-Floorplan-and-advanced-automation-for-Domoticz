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
	$uit=40;
	if ($d['pirgarage']['s']=='Off'&&past('pirgarage')>$uit&&past('poort')>$uit&&past('deurgarage')>$uit&&past('garageled')>$uit&&$d['garageled']['s']=='On') sw('garageled', 'Off', basename(__FILE__).':'.__LINE__);
	elseif ($d['pirgarage']['s']=='On'&&$d['garageled']['s']=='Off'&&$d['garage']['s']=='Off'&&$d['zon']['s']==0) sw('garageled', 'On', basename(__FILE__).':'.__LINE__);
	$uit=40;
	if ($d['pirgarage']['s']=='Off'&&past('pirgarage')>$uit&&past('poort')>$uit&&past('deurgarage')>$uit&&past('garage')>$uit&&$d['garage']['s']=='On') sw('garage', 'Off', basename(__FILE__).':'.__LINE__);
	$uit=120;
	if ($d['pirzolder']['s']=='Off'&&$d['zolderg']['s']=='On'&&past('pirzolder')>$uit&&past('zolderg')>$uit) sw('zolderg', 'Off', basename(__FILE__).':'.__LINE__);
	$uit=15;
	if ($d['pirinkom']['s']=='Off'&&$d['inkom']['s']>0&&past('inkom')>$uit&&past('pirinkom')>$uit&&past('deurwc')>$uit&&past('deurinkom')>$uit&&past('deurvoordeur')>$uit) {
		foreach (array(28,26,0) as $i) {
			if ($d['inkom']['s']>$i) {
				sl('inkom', $i, basename(__FILE__).':'.__LINE__);
				break;
			}
		}
	} else {
		if ($d['pirinkom']['s']=='On'&&$d['zon']['s']==0) finkom();
	}
	if ($d['pirhall']['s']=='Off'&&$d['hall']['s']>0&&past('hall')>$uit&&past('pirhall')>$uit&&past('deurbadkamer')>$uit&&past('deurkamer')>$uit&&past('deurspeelkamer')>$uit&&past('deuralex')>$uit) {
		foreach (array(28,26,0) as $i) {
			if ($d['hall']['s']>$i) {
				sl('hall', $i, basename(__FILE__).':'.__LINE__);
				break;
			}
		}
	} else {
		if ($d['pirhall']['s']=='On'&&$d['zon']['s']==0) fhall();
	}
	if ($d['pirkeuken']['s']=='Off'&&$d['wasbak']['s']>0&&past('wasbak')>8) {
		foreach (array(5,4,3,2,1,0) as $i) {
			if ($d['wasbak']['s']>$i) {
				sl('wasbak', $i, basename(__FILE__).':'.__LINE__);
				break;
			}
		}
	}
	if ($d['sirene']['s']=='On'&&past('sirene')>110) sw('sirene', 'Off', basename(__FILE__).':'.__LINE__);
}
$uit=50;
if ($d['deurvoordeur']['s']=='Closed'&&$d['voordeur']['s']=='On'&&past('deurvoordeur')>$uit&&past('voordeur')>$uit) sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__);

if ($d['tv']['s']=='On') {
	if (pingport('192.168.2.6', 3000)==1) {
		if ($d['lgtv']['s']=='Off') sw('lgtv', 'On', basename(__FILE__).':'.__LINE__);
		if ($d['TV']['s']=='Off') sw('TV', 'On', basename(__FILE__).':'.__LINE__);
		apcu_store('lgtv-offline', 0);
		if ($d['nvidia']['s']!='On'&&past('nvidia')>30	&&$d['Weg']['s']==0) {
			sw('nvidia', 'On', basename(__FILE__).':'.__LINE__);
			if ($d['denon']['s']!='On'&&past('denon')>30&&$d['Weg']['s']==0) sw('denon', 'On', basename(__FILE__).':'.__LINE__);
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
			if ($d['TV']['s']=='On') sw('TV', 'Off', basename(__FILE__).':'.__LINE__);
			if ($d['auto']['s']=='On'&&$d['lamp kast']['s']=='Off'&&$d['zon']['s']==0&&$d['Weg']['s']==0) sw('lamp kast', 'On', basename(__FILE__).':'.__LINE__);
		}
		if ($d['nvidia']['s']!='Off'&&past('lgtv')>900&&past('nvidia')>900) {
			sw('nvidia', 'Off', basename(__FILE__).':'.__LINE__);
			if ($d['denon']['s']!='Off'&&past('denon')>900) sw('denon', 'Off', basename(__FILE__).':'.__LINE__);
			if ($d['DENON']['s']!='Off'&&past('DENON')>900) sw('DENON', 'Off', basename(__FILE__).':'.__LINE__);
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
if (past('wind')>86&&past('buiten_temp')>86&&past('buien')>86) require('_weather.php');
$user='cron10  ';
$el=$d['el']['s']-$d['zon']['s'];
//lg($el);

// BOSE Living
if (pingport('192.168.2.101', 80)==1) {
	$status=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.101:8090/now_playing"))), true);
	if (!empty($status)) {
		if ($d['bose101']['icon']!='Online') {
			storeicon('bose101', 'Online', basename(__FILE__).':'.__LINE__);
		}
	}
} else {
	if (pingport('192.168.2.101', 80)!=1&&$d['bose101']['icon']!='Offline') {
		storeicon('bose101', 'Offline', basename(__FILE__).':'.__LINE__);
		sw('bose101', 'Off', basename(__FILE__).':'.__LINE__);
	}
}
// END BOSE Living

// BOSE Keuken
if (pingport('192.168.2.105', 80)==1) {
	$status=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.105:8090/now_playing"))), true);
	if (!empty($status)) {
		if ($d['bose105']['icon']!='Online') {
			storeicon('bose105', 'Online', basename(__FILE__).':'.__LINE__);
		}
	}
} else {
	if (pingport('192.168.2.105', 80)!=1&&$d['bose105']['icon']!='Offline') {
		storeicon('bose105', 'Offline', basename(__FILE__).':'.__LINE__);
		sw('bose105', 'Off', basename(__FILE__).':'.__LINE__);
		sw('Bose Keuken', 'Off', basename(__FILE__).':'.__LINE__);
	}
}
// END BOSE Keuken

// BOSE Kamer
if (pingport('192.168.2.103', 80)==1) {
	if (TIME>strtotime('10:00')) {
		$status=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.103:8090/now_playing"))), true);
		if (!empty($status)) {
			if ($d['bose103']['icon']!='Online') {
				storeicon('bose103', 'Online', basename(__FILE__).':'.__LINE__);
				if (isset($status['@attributes']['source'])) {
					if ($status['@attributes']['source']=='STANDBY'&&$d['bose101']['m']==1) {
						sleep(1);
						if ($d['bose101']['s']=='On') bosezone(103, true);
						else bosekey('PRESET_5', 0, 103);
						bosevolume(18, 103);
					}
				}
			}
		}
	}
} else {
	if (pingport('192.168.2.103', 80)!=1&&$d['bose103']['icon']!='Offline') {
		storeicon('bose103', 'Offline', basename(__FILE__).':'.__LINE__);
		sw('bose103', 'Off', basename(__FILE__).':'.__LINE__);
	}
}
// END BOSE Kamer

// BOSE Garage
if (pingport('192.168.2.104', 80)==1) {
	$status=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.104:8090/now_playing"))), true);
	if (!empty($status)) {
		if ($d['bose104']['icon']!='Online') {
			storeicon('bose104', 'Online', basename(__FILE__).':'.__LINE__);
			bosekey('SHUFFLE_ON', 0, 101);
		}
		if (isset($status['@attributes']['source'])&&$status['@attributes']['source']=='STANDBY'&&$d['bose101']['m']==1) {
			bosezone(104);
			bosevolume(38, 104);
		}
	}
} else {
	if (pingport('192.168.2.104', 80)!=1&&$d['bose104']['icon']!='Offline') {
		storeicon('bose104', 'Offline', basename(__FILE__).':'.__LINE__);
		sw('bose104', 'Off', basename(__FILE__).':'.__LINE__);
	}
}
// END BOSE Garage

// BOSE Badkamer
if (pingport('192.168.2.102', 80)==1) {
	if (TIME>strtotime('6:00')&&TIME<strtotime('21:00')) {
		$status=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.102:8090/now_playing"))), true);
		if (!empty($status)) {
			if ($d['bose102']['icon']!='Online') {
				storeicon('bose102', 'Online', basename(__FILE__).':'.__LINE__);
			}
			if (isset($status['@attributes']['source'])) {
				if ($status['@attributes']['source']=='STANDBY'&&$d['bose101']['m']==1) {
					bosezone(102, true);
					bosevolume(22, 102);
				}
			}
		}
	}
} else {
	sleep(1);
	if (pingport('192.168.2.102', 80)!=1&&$d['bose102']['icon']!='Offline') {
		storeicon('bose102', 'Offline', basename(__FILE__).':'.__LINE__);
		sw('bose102', 'Off', basename(__FILE__).':'.__LINE__);
	}
}
// END BOSE Badkamer

// BOSE BUITEN
if (pingport('192.168.2.106', 80)==1) {
	$status=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.106:8090/now_playing"))), true);
	if (!empty($status)) {
		if ($d['bose106']['icon']!='Online') {
			storeicon('bose106', 'Online', basename(__FILE__).':'.__LINE__);
		}
		if (isset($status['@attributes']['source'])&&$status['@attributes']['source']=='STANDBY'&&$d['bose101']['m']==1) {
			bosezone(106);
			bosevolume(32, 106);
		}
	}
} else {
	if (pingport('192.168.2.106', 80)!=1&&$d['bose106']['icon']!='Offline') {
		storeicon('bose106', 'Offline', basename(__FILE__).':'.__LINE__);
		sw('bose106', 'Off', basename(__FILE__).':'.__LINE__);
	}
}
if (pingport('192.168.2.107', 80)==1) {
	$status=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.107:8090/now_playing"))), true);
	if (!empty($status)) {
		if ($d['bose107']['icon']!='Online') {
			storeicon('bose107', 'Online', basename(__FILE__).':'.__LINE__);
		}
		if (isset($status['@attributes']['source'])&&$status['@attributes']['source']=='STANDBY'&&$d['bose101']['m']==1) {
			bosezone(107);
			bosevolume(38, 107);
		}
	}
} else {
	if (pingport('192.168.2.107', 80)!=1&&$d['bose107']['icon']!='Offline') {
		storeicon('bose107', 'Offline', basename(__FILE__).':'.__LINE__);
		sw('bose107', 'Off', basename(__FILE__).':'.__LINE__);
	}
}
// END BOSE BUITEN
