<?php
$d=fetchdata();
if ($d['Weg']['s']<3) {
	dag();
	$user='cron17  ';
	$ctx=stream_context_create(array('http'=>array('timeout' =>1)));
	foreach(array(102=>35,103=>18,104=>35,106=>35,107=>30) as $ip=>$vol) {
		$status=@file_get_contents("http://192.168.2.$ip:8090/now_playing", false, $ctx);
		$status=json_decode(json_encode(simplexml_load_string($status)), true);
		if (isset($status['@attributes']['source'])) {
			if ($d['bose'.$ip]['icon']!='Online') storeicon('bose'.$ip, 'Online', basename(__FILE__).':'.__LINE__);
			if (isset($status['@attributes']['source'])&&$status['@attributes']['source']=='STANDBY'&&$d['bose101']['m']==1) {
				if ($ip==103) {
					if ($d['bose101']['s']=='On') bosezone($ip, true);
					elseif ($d['bose103']['m']==0&&$time>strtotime('20:00')) {
						bosekey('PRESET_5', 0, $ip);
//						storemode('bose101', 0);
						bosevolume($vol, $ip);
					}
					
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
			//if ($ip==103&&$d['bose101']['m']==0&&$time<strtotime('8:30')) storemode('bose101', 1);
		}
		unset($status);
	}
	foreach(array(101,105) as $ip) {
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
	$loadedprofile=@json_decode(@file_get_contents($kodiurl.'/jsonrpc?request={"jsonrpc":"2.0","id":"1","method":"Profiles.GetCurrentProfile","id":1}', false, $ctx), true);
	if (isset($loadedprofile['result']['label'])&&$d['nas']['s']=='Off') shell_exec('/var/www/html/secure/wakenas.sh &');
	
	
	exec('/var/www/html/secure/lgtv.py '.$lgtvip, $output, $return_var);
	if ($output[0]=='TV is on.'&&$d['lgtv']['s']=='Off') sw('lgtv', 'On', basename(__FILE__).':'.__LINE__);
	elseif ($output[0]!='TV is on.'&&$d['lgtv']['s']=='On') sw('lgtv', 'Off', basename(__FILE__).':'.__LINE__);
}
//if (past('wind')>86&&past('buiten_temp')>86&&past('buien')>86) require('_weather.php');
