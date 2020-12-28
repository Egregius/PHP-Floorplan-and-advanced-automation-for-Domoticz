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
	if ($d['pirgarage']['s']=='Off'
		&&past('pirgarage')>120
		&&past('poort')>90
		&&past('deurgarage')>90
		&&past('garageled')>120
		&&$d['garageled']['s']=='On'
	) {
		sw('garageled', 'Off', basename(__FILE__).':'.__LINE__);
	} elseif ($d['pirgarage']['s']=='On'
		&&$d['garageled']['s']=='Off'
		&&$d['garage']['s']=='Off'
		&&$d['zon']['s']<300
	) {
		sw('garageled', 'On', basename(__FILE__).':'.__LINE__);
	}

	if ($d['pirgarage']['s']=='Off'
		&&past('pirgarage')>120
		&&past('poort')>90
		&&past('deurgarage')>90
		&&past('garage')>240
		&&$d['garage']['s']=='On'
	) {
		sw('garage', 'Off', basename(__FILE__).':'.__LINE__);
	}
	if ($d['pirzolder']['s']=='Off'
		&&$d['zolderg']['s']=='On'
		&&past('pirzolder')>120
		&&past('zolderg')>120
	) {
		sw('zolderg', 'Off', basename(__FILE__).':'.__LINE__);
	}
	$items=array(32,31,30,29,28,27,26,25,24,0);
	if ($d['pirinkom']['s']=='Off'&&$d['inkom']['s']>0&&past('pirinkom')>30&&past('deurwc')>30&&past('deurinkom')>30&&past('deurvoordeur')>30) {
		foreach ($items as $i) {
			if ($d['inkom']['s']>$i) {
				sl('inkom', $i, basename(__FILE__).':'.__LINE__);
				break;
			}
		}
	} else {
		if ($d['pirinkom']['s']=='On'&&$d['zon']['s']==0) {
			finkom();
		}
	}
	if ($d['pirhall']['s']=='Off'&&$d['hall']['s']>0&&past('pirhall')>30&&past('deurbadkamer')>30&&past('deurkamer')>30&&past('deurtobi')>30&&past('deuralex')>30) {
		foreach ($items as $i) {
			if ($d['hall']['s']>$i) {
				sl('hall', $i, basename(__FILE__).':'.__LINE__);
				break;
			}
		}
	} else {
		if ($d['pirhall']['s']=='On'&&$d['zon']['s']==0) {
			fhall();
		}
	}
	if (past('pirkeuken')>40
		&&past('keuken')>40
		&&$d['pirkeuken']['s']=='Off'
		&&$d['wasbak']['s']=='Off'
		&&$d['keuken']['s']=='On'
		&&$d['kookplaat']['s']=='Off'
		&&$d['werkblad1']['s']=='Off'
	) {
		sw('keuken', 'Off', basename(__FILE__).':'.__LINE__);
	}
	if ($d['GroheRed']['s']=='Off'&&past('GroheRed')>120
		&&(
		($d['wasbak']['s']=='On'&&past('wasbak')>10)
		||($d['kookplaat']['s']=='On'&&past('kookplaat')>10))
	) {
		sw('GroheRed', 'On', basename(__FILE__).':'.__LINE__);
	}

	if ($d['sirene']['s']=='On'&&past('sirene')>110) sw('sirene', 'Off', basename(__FILE__).':'.__LINE__);
}
if ($d['tv']['s']=='On') {
	if (pingport('192.168.2.27', 3000)==1) {
		if ($d['lgtv']['s']!='On') {
			sw('lgtv', 'On', basename(__FILE__).':'.__LINE__);
			apcu_store('lgtv-offline', 0);
		}
		/*if ($d['denon']['s']!='On'
			&&past('denon')>30
			&&$d['Weg']['s']==0
		) {
			sw('denon', 'On', basename(__FILE__).':'.__LINE__);
			storemode('denon', 'TV', basename(__FILE__).':'.__LINE__);
		}*/
		if ($d['nvidia']['s']!='On'&&past('nvidia')>30	&&$d['Weg']['s']==0) {
			sw('nvidia', 'On', basename(__FILE__).':'.__LINE__);
		}
	} else {
		if ($d['lgtv']['s']=='On') apcu_inc('lgtv-offline');
	}
}

if ($d['nvidia']['s']=='On') {
	if (pingport($shieldip, 9080)==1) {
		if ($d['nvidia']['m']=='Off') {
			storemode('nvidia', 'On', basename(__FILE__).':'.__LINE__);
		}
	} else {
		if ($d['nvidia']['m']=='On') {
			storemode('nvidia', 'Off', basename(__FILE__).':'.__LINE__);
		}
	}
}
if (pingport('192.168.2.105', 80)==1) {
	sleep(1);
	if (pingport('192.168.2.105', 80)==1) {
		if ($d['achterdeur']['s']=='Open') {
			$status=json_decode(
				json_encode(
					simplexml_load_string(
						@file_get_contents(
							"http://192.168.2.105:8090/now_playing"
						)
					)
				),
				true
			);
			if (!empty($status)) {
				if ($d['bose105']['m']!='Online') {
					storemode('bose105', 'Online', basename(__FILE__).':'.__LINE__, 10);
					bosekey('SHUFFLE_ON', 0, '192.168.2.101');
				}
				if (isset($status['@attributes']['source'])) {
					if ($status['@attributes']['source']=='STANDBY') {
						bosezone(105);
						sw('bose105', 'On', basename(__FILE__).':'.__LINE__);
						bosekey('SHUFFLE_ON', 0, '192.168.2.101');
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


if ($d['daikinliving']['m']==3||$d['daikinkamer']['m']==3||$d['daikinalex']['m']==3) {$rgb=230;$mode=3;}
elseif ($d['daikinliving']['m']==4||$d['daikinkamer']['m']==4||$d['daikinalex']['m']==4) {$rgb=1;$mode=4;}
elseif ($d['daikinliving']['m']==2||$d['daikinkamer']['m']==2||$d['daikinalex']['m']==2) {$rgb=56;$mode=2;}
else $rgb=false;
if ($rgb!=false) {
	$data=file_get_contents('http://192.168.2.112/aircon/get_sensor_info');
	if($data === FALSE) {
		return FALSE;
	}else{
		$array=explode(",",$data);
		$control_info= array();
		foreach($array as $value) {
			$pair= explode("=",$value);
			$control_info[$pair[0]]=$pair[1];
		}
		$level=$control_info['cmpfreq'];
		if ($level>100)$level=100;
		$Xlight=round($level/3);
		if ($d['Xlight']['s']!=$Xlight) {
			if ($d['Weg']['s']==0) {
				rgb('Xlight', $rgb, $Xlight);
				sl('Xlight', $Xlight, basename(__FILE__).':'.__LINE__);
			}
			if ($mode==3)storemode('Xlight', -$level, basename(__FILE__).':'.__LINE__);
			elseif ($mode==4)storemode('Xlight', $level, basename(__FILE__).':'.__LINE__);
			elseif ($mode==2)storemode('Xlight', -$level, basename(__FILE__).':'.__LINE__);
		}
	}
} else {
	if ($d['Xlight']['s']>0) sw('Xlight', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['Xlight']['m']!=0) storemode('Xlight', 0, basename(__FILE__).':'.__LINE__);
}
if ($d['Weg']['s']>0) {
	if ($d['Xlight']['s']>0) {
		sw('Xlight', 'Off', basename(__FILE__).':'.__LINE__);
	}
}
if (past('wind')>86) {
	require('_weather.php');
}
