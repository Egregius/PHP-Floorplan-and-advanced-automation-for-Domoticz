<?php
$mqtt->publish('p', 'c', 0, false);
$user=basename(__FILE__);
$ctx=stream_context_create(array('http'=>array('timeout' =>1)));
if ($d['Weg']['s']==0&&$d['langekast']['s']=='On'&&past('langekast')>75) {
	$week=date('W');
	foreach(array(102=>35,103=>35,104=>35,105=>35,106=>35,107=>35) as $ip=>$vol) {
		$status=@file_get_contents("http://192.168.2.$ip:8090/now_playing", false, $ctx);
		$status=json_decode(json_encode(simplexml_load_string($status)), true);
		if (isset($status['@attributes']['source'])) {
			if ($d['bose'.$ip]['icon']!='Online') {
				storeicon('bose'.$ip, 'Online', basename(__FILE__).':'.__LINE__, true);
				$d['bose'.$ip]['icon']='Online';
				$mqtt->publish('i/bose'.$ip, json_encode($d['bose'.$ip]), 0, false);
			}
			if (isset($status['@attributes']['source'])&&$status['@attributes']['source']=='STANDBY'&&$d['bose101']['m']==1) {
				bosezone($ip,true,$vol);
//				usleep(500000);
//				bosevolume($vol, $ip);
			}
			if (isset($status['playStatus'])&&$status['playStatus']=='PLAY_STATE') {
				if ($d['bose'.$ip]['s']=='Off') {
					sw('bose'.$ip, 'On', basename(__FILE__).':'.__LINE__);
					$d['bose'.$ip]['s']='On';
					$mqtt->publish('i/bose'.$ip, json_encode($d['bose'.$ip]), 0, false);
				}
			} elseif (isset($status['@attributes']['source'])&&$status['@attributes']['source']=='STANDBY') {
				if ($d['bose'.$ip]['s']=='On') {
					sw('bose'.$ip, 'Off', basename(__FILE__).':'.__LINE__);
					$d['bose'.$ip]['s']='Off';
					$mqtt->publish('i/bose'.$ip, json_encode($d['bose'.$ip]), 0, false);
				}
			}
		} else {
//			lg('bose'.$ip.' '.__LINE__.' '.$d['bose'.$ip]['icon']);
			if ($d['bose'.$ip]['icon']!='Offline') {
				storeicon('bose'.$ip, 'Offline', basename(__FILE__).':'.__LINE__, true);
				$d['bose'.$ip]['icon']='Offline';
				$mqtt->publish('i/bose'.$ip, json_encode($d['bose'.$ip]), 0, false);
			}
			if ($d['bose'.$ip]['s']=='On') sw('bose'.$ip, 'Off', basename(__FILE__).':'.__LINE__);
		}
		unset($status);
	}
	foreach(array(101) as $ip) {
		$status=@file_get_contents("http://192.168.2.$ip:8090/now_playing", false, $ctx);
		if (isset($status)) {
			if ($status=='<?xml version="1.0" encoding="UTF-8" ?><nowPlaying deviceID="587A6260C5B2" source="INVALID_SOURCE"><ContentItem source="INVALID_SOURCE" isPresetable="true" /></nowPlaying>') {
				lg('INVALID SOURCE');
				if ($weekend==true) {
					if ((int)$week % 2 == 0) $preset='PRESET_4';
					else $preset='PRESET_3';
				} else {
					if ((int)$week % 2 == 0) $preset='PRESET_2';
					else $preset='PRESET_1';
				}
				bosekey($preset, 0, $ip, basename(__FILE__).':'.__LINE__);
			}
			$status=json_decode(json_encode(simplexml_load_string($status)), true);
			if (isset($status['@attributes']['source'])) {
				if ($d['bose'.$ip]['icon']!='Online') {
					storeicon('bose'.$ip, 'Online', basename(__FILE__).':'.__LINE__, true);
					$d['bose'.$ip]['icon']='Online';
					$mqtt->publish('i/bose'.$ip, json_encode($d['bose'.$ip]), 0, false);
					if ($d['lg_webos_tv_cd9e']['s']!='On'&&$time>=strtotime('5:30')&&$time<strtotime('19:30')) {
						bosezone(101);
					}
					
				}
				if (isset($status['playStatus'])&&$status['playStatus']=='PLAY_STATE') {
					if ($d['bose'.$ip]['s']=='Off') sw('bose'.$ip, 'On', basename(__FILE__).':'.__LINE__);
				} elseif (isset($status['@attributes']['source'])&&$status['@attributes']['source']=='STANDBY') {
					if ($d['bose'.$ip]['s']=='On') sw('bose'.$ip, 'Off', basename(__FILE__).':'.__LINE__);
				}
			} else {
				if ($d['bose'.$ip]['icon']!='Offline') {
					storeicon('bose'.$ip, 'Offline', basename(__FILE__).':'.__LINE__);
					$d['bose'.$ip]['icon']='Offline';
					$mqtt->publish('i/bose'.$ip, json_encode($d['bose'.$ip]), 0, false);
				}
				if ($d['bose'.$ip]['s']=='On') sw('bose'.$ip, 'Off', basename(__FILE__).':'.__LINE__);
			}
			unset($status);
		}
	}
	if ($d['Media']['s']=='On'&&$d['nas']['s']=='Off') {
		$loadedprofile=@json_decode(@file_get_contents($kodiurl.'/jsonrpc?request={"jsonrpc":"2.0","id":"1","method":"Profiles.GetCurrentProfile","id":1}', false, $ctx), true);
		if (isset($loadedprofile['result']['label'])) {
			lg('Waking NAS...');
			shell_exec('/var/www/html/secure/wakenas.sh &');
		}
	}	
}
if($d['langekast']['s']!='On') {
	if ($d['bose101']['icon']!='Offline') {
		storeicon('bose101', 'Offline', basename(__FILE__).':'.__LINE__, true);
		$d['bose'.$ip]['icon']='Offline';
		$mqtt->publish('i/bose101', json_encode($d['bose101']), 0, false);
	}
} else {
	if (past('langekast')<15) sw('lamp kast', 'Off');
}

if ($d['Weg']['s']==0) {
	if ($d['nas']['s']=='Off') {
		if ($d['lg_webos_tv_cd9e']['s']=='On') {
			$kodi=@json_decode(@file_get_contents($kodiurl.'/jsonrpc?request={"jsonrpc":"2.0","id":"1","method":"JSONRPC.Ping","id":1}', false, $ctx), true);
			if (isset($kodi['result'])) {
				lg('Waking NAS for Kodi...');
				shell_exec('/var/www/html/secure/wakenas.sh &');
				unset($kodi);
			}
			if (past('lg_webos_tv_cd9e')<35) hassinput('media_player','select_source','media_player.lg_webos_tv_cd9e','SHIELD');
			if ($d['heating']['s']>0&&$d['Rliving']['s']<100) sl('Rliving', 100, basename(__FILE__).':'.__LINE__);
			elseif ($d['Rliving']['s']<25) sl('Rliving', 25, basename(__FILE__).':'.__LINE__);
		}
		if (past('pirhall')<300) {
			$kodi=@json_decode(@file_get_contents($kodiurl2.'/jsonrpc?request={"jsonrpc":"2.0","id":"1","method":"JSONRPC.Ping","id":1}', false, $ctx), true);
			if (isset($kodi['result'])) {
				lg('Waking NAS for Kodi 2...');
				shell_exec('/var/www/html/secure/wakenas.sh &');
				unset($kodi);
			}
		}
		
	}
}


if ($d['pirliving']['s']=='Off'
	&&$d['pirgarage']['s']=='Off'
	&&$d['bose101']['m']==1
	&&$d['bose101']['s']=='On'
	&&$d['bose102']['s']=='Off'
	&&$d['bose103']['s']=='Off'
	&&$d['bose104']['s']=='Off'
	&&$d['bose105']['s']=='Off'
	&&$d['bose106']['s']=='Off'
	&&$d['bose107']['s']=='Off'
	&&past('bose101')>30
	&&past('bose102')>30
	&&past('bose103')>30
	&&past('bose104')>30
	&&past('bose105')>30
	&&past('bose106')>30
	&&past('bose107')>30
	&&(($d['Weg']['s']>0||$d['lg_webos_tv_cd9e']['s']=='On')&&$d['eettafel']['s']==0)
) {
	$status=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.101:8090/now_playing"))),true);
	if (!empty($status)) {
		if (isset($status['@attributes']['source'])) {
			if ($status['@attributes']['source']!='STANDBY') {
				bosekey("POWER", 0, 101);
				if ($d['bose101']['s']!='Off') sw('bose101', 'Off', basename(__FILE__).':'.__LINE__);
				if ($d['bose102']['s']!='Off') sw('bose102', 'Off', basename(__FILE__).':'.__LINE__);
				if ($d['bose103']['s']!='Off') sw('bose103', 'Off', basename(__FILE__).':'.__LINE__);
				if ($d['bose104']['s']!='Off') sw('bose104', 'Off', basename(__FILE__).':'.__LINE__);
				if ($d['bose105']['s']!='Off') sw('bose105', 'Off', basename(__FILE__).':'.__LINE__);
				if ($d['bose106']['s']!='Off') sw('bose106', 'Off', basename(__FILE__).':'.__LINE__);
				if ($d['bose107']['s']!='Off') sw('bose107', 'Off', basename(__FILE__).':'.__LINE__);
			}
		}
	}
}