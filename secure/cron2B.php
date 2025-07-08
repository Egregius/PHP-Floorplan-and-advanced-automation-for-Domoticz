<?php
$user=basename(__FILE__);
$ctx=stream_context_create(array('http'=>array('timeout' =>1)));
if ($d['weg']['s']==0&&($d['boseliving']['s']=='On'||$d['boseliving']['s']=='Playing')) {
	$week=date('W');
	foreach(array(101=>8,102=>32,103=>32,104=>32,105=>32,106=>32,107=>32) as $ip=>$vol) {
		$status=@file_get_contents("http://192.168.2.$ip:8090/now_playing", false, $ctx);
		$status=json_decode(json_encode(simplexml_load_string($status)), true);
		if (isset($status['@attributes']['source'])) {
//			lg(basename(__FILE__).':'.__LINE__);
			if ($d['bose'.$ip]['icon']!='Online'&&$d['boseliving']['s']!='On') {
//				lg(basename(__FILE__).':'.__LINE__);
				sw('boseliving', 'On', basename(__FILE__).':'.__LINE__);
			} elseif ($d['bose'.$ip]['icon']!='Online') {
//				lg(basename(__FILE__).':'.__LINE__);
				storeicon('bose'.$ip, 'Online', basename(__FILE__).':'.__LINE__, true);
			}

			if (isset($status['@attributes']['source'])&&$status['@attributes']['source']=='STANDBY'&&$d['bose101']['m']==1) {
				bosezone($ip);
				if ($ip>101) {
					usleep(500000);
					bosevolume($vol, $ip);
				}
			}
			if (isset($status['playStatus'])&&$status['playStatus']=='PLAY_STATE') {
				if ($d['bose'.$ip]['s']=='Off') sw('bose'.$ip, 'On', basename(__FILE__).':'.__LINE__);
			} elseif (isset($status['@attributes']['source'])&&$status['@attributes']['source']=='STANDBY') {
				if ($d['bose'.$ip]['s']=='On') sw('bose'.$ip, 'Off', basename(__FILE__).':'.__LINE__);
			}
		} else {
			if ($d['bose'.$ip]['icon']!='Offline') storeicon('bose'.$ip, 'Offline', basename(__FILE__).':'.__LINE__, true);
			if ($d['bose'.$ip]['s']=='On') sw('bose'.$ip, 'Off', basename(__FILE__).':'.__LINE__);
		}
		unset($status);
	}
/*	foreach(array(101) as $ip) {
		$status=@file_get_contents("http://192.168.2.$ip:8090/now_playing", false, $ctx);
		if (isset($status)) {
			if ($status=='<?xml version="1.0" encoding="UTF-8" ?><nowPlaying deviceID="587A6260C5B2" source="INVALID_SOURCE"><ContentItem source="INVALID_SOURCE" isPresetable="true" /></nowPlaying>') {
				$playlist=boseplaylist();
					if ($playlist=='EDM-1') $preset='PRESET_1';
				elseif ($playlist=='EDM-2') $preset='PRESET_2';
				elseif ($playlist=='EDM-3') $preset='PRESET_3';
				elseif ($playlist=='MIX-1') $preset='PRESET_4';
				elseif ($playlist=='MIX-2') $preset='PRESET_5';
				elseif ($playlist=='MIX-3') $preset='PRESET_6';
				if (isset(${'lasttrybose'.$ip}) && ${'lasttrybose'.$ip}<$time-30) {
					lg('INVALID SOURCE');
					bosekey($preset, 0, $ip, basename(__FILE__).':'.__LINE__);
					${'lasttrybose'.$ip}=$time;
				} else ${'lasttrybose'.$ip}=0;
			}
			$status=json_decode(json_encode(simplexml_load_string($status)), true);
			if (isset($status['@attributes']['source'])) {
				if ($d['bose'.$ip]['icon']!='Online') {
					storeicon('bose'.$ip, 'Online', basename(__FILE__).':'.__LINE__, true);
					bosezone(101);
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
	}*/
	if ($d['media']['s']=='On'&&$d['nas']['s']=='Off') {
		$loadedprofile=@json_decode(@file_get_contents($kodiurl.'/jsonrpc?request={"jsonrpc":"2.0","id":"1","method":"Profiles.GetCurrentProfile","id":1}', false, $ctx), true);
		if (isset($loadedprofile['result']['label'])) {
			lg('Waking NAS...');
			shell_exec('/var/www/html/secure/wakenas.sh &');
		}
	}	
}
if($d['boseliving']['s']!='On'&&$d['boseliving']['s']!='Playing') {
	if ($d['bose101']['icon']!='Offline') storeicon('bose101', 'Offline', basename(__FILE__).':'.__LINE__, true);
}

if ($d['weg']['s']==0&&$d['auto']['s']=='On') {
	if ($d['nas']['s']=='Off') {
		if ($d['lgtv']['s']=='On') {
			$kodi=@json_decode(@file_get_contents($kodiurl.'/jsonrpc?request={"jsonrpc":"2.0","id":"1","method":"JSONRPC.Ping","id":1}', false, $ctx), true);
			if (isset($kodi['result'])) {
				lg('Waking NAS for Kodi...');
				shell_exec('/var/www/html/secure/wakenas.sh &');
				unset($kodi);
			}
			if (past('lgtv')<35) hassinput('media_player','select_source','media_player.lgtv','HDMI 4');
//			if ($d['heating']['s']>0&&$d['Rliving']['s']<100) sl('Rliving', 100, basename(__FILE__).':'.__LINE__);
//			elseif ($d['Rliving']['s']<25) sl('Rliving', 25, basename(__FILE__).':'.__LINE__);
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
	&&($d['weg']['s']>0||($d['lgtv']['s']=='On'&&$d['eettafel']['s']==0))
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
				sleep(2);
				if ($d['boseliving']['s']!='Off') sw('boseliving', 'Off', basename(__FILE__).':'.__LINE__);
			}
		}
	}
}
if ($d['kodi']['s']=='Idle'||$d['kodi']['s']=='Paused') {
	kodi('{"jsonrpc": "2.0","method": "GUI.ActivateScreensaver","id": 1}');
}