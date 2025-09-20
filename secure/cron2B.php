<?php
$user='cron2B';
$boses=array(
	101=>'Living',
	102=>'102',
	103=>'Boven',
	104=>'Garage',
	105=>'10-Wit',
	106=>'Buiten20',
	107=>'Keuken',
);

foreach(array(101=>14,102=>22,103=>32,104=>32,105=>32,106=>22,107=>32) as $ip=>$vol) {
	$ch = curl_init("http://192.168.2.$ip:8090/now_playing");
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1500);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$status = curl_exec($ch);
	curl_close($ch);
	$status=json_decode(json_encode(simplexml_load_string($status)), true);
	if (is_array($status)) {
		if (isset($status['@attributes']['source'])) {
			if ($d['bose'.$ip]['icon']!='Online'&&$d['boseliving']['s']!='On') {
				sw('boseliving', 'On', basename(__FILE__).':'.__LINE__);
			} elseif ($d['bose'.$ip]['icon']!='Online') {
				storeicon('bose'.$ip, 'Online', basename(__FILE__).':'.__LINE__, true);
			}
			if ($status['@attributes']['source']=='STANDBY'&&$d['bose101']['m']==1) {
				bosezone($ip);
				if ($ip>101) {
					usleep(500000);
					bosevolume($vol, $ip);
				}
			} elseif ($status['@attributes']['source']=='STANDBY') {
				if ($d['bose'.$ip]['s']=='On') sw('bose'.$ip, 'Off', basename(__FILE__).':'.__LINE__);
			} elseif ($status['@attributes']['source']=='INVALID_SOURCE') {
				$invalidcounter++;
				if ($invalidcounter>10) {
					lg('invalidcounter='.$invalidcounter);
					bosekey("POWER", 0, 101);
					if ($d['bose'.$ip]['s']=='On') sw('bose'.$ip, 'Off', basename(__FILE__).':'.__LINE__);
					if ($d['boseliving']['s']!='Off') {
						sw('boseliving', 'Off', basename(__FILE__).':'.__LINE__);
						sleep(5);
						sw('boseliving', 'On', basename(__FILE__).':'.__LINE__);
						$invalidcounter=0;
					}
					
				}
			}
			if (isset($status['playStatus'])&&$status['playStatus']=='PLAY_STATE') {
				if ($d['bose'.$ip]['s']=='Off') sw('bose'.$ip, 'On', basename(__FILE__).':'.__LINE__);
				if ($invalidcounter>0) $invalidcounter=0;
			}
		} else {
			if ($d['bose'.$ip]['icon']!='Offline') storeicon('bose'.$ip, 'Offline', basename(__FILE__).':'.__LINE__, true);
			if ($d['bose'.$ip]['s']=='On') sw('bose'.$ip, 'Off', basename(__FILE__).':'.__LINE__);
		}
		unset($status);
	}
}
if ($d['media']['s']=='On') {
	if ($d['nas']['s']=='Off') {
		$loadedprofile=@json_decode(@file_get_contents($kodiurl.'/jsonrpc?request={"jsonrpc":"2.0","id":"1","method":"Profiles.GetCurrentProfile","id":1}', false, $ctx), true);
		if (isset($loadedprofile['result']['label'])) {
			lg('Waking NAS...');
			shell_exec('/var/www/html/secure/wakenas.sh &');
		}
	}
	if ($d['lgtv']['s']=='Off'&&past('media')>1800&&past('lgtv')>900) {
		if (ping('192.168.2.6')!=1) sw('media', 'Off', basename(__FILE__).':'.__LINE__);
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
			if (past('lgtv')>=20&&past('lgtv')<=30) hassinput('media_player','select_source','media_player.lgtv','HDMI 4');
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
	if ($d['media']['s']=='On') {
		if ($d['lgtv']['s']=='Off'&&past('media')>1800&&past('lgtv')>900) {
			if (ping('192.168.2.6')===false&&ping('192.168.2.28')===false) sw('media', 'Off', basename(__FILE__).':'.__LINE__);
		}
//		if (past('media')>45&&past('media')<60) sw('shieldpower', 'On','',true);
	}	
}
if ($d['bose101']['m']==1
	&&$d['bose101']['s']=='On'
	&&$d['bose102']['s']=='Off'
	&&$d['bose103']['s']=='Off'
	&&$d['bose104']['s']=='Off'
	&&$d['bose105']['s']=='Off'
	&&$d['bose106']['s']=='Off'
	&&$d['bose107']['s']=='Off'
	&&past('bose101')>90
	&&past('bose102')>90
	&&past('bose103')>90
	&&past('bose104')>90
	&&past('bose105')>90
	&&past('bose106')>90
	&&past('bose107')>90
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
				if ($d['boseliving']['s']!='Off') sw('boseliving', 'Off', basename(__FILE__).':'.__LINE__);
			}
		}
	}
}
/*if ($d['kodi']['s']=='Idle'||$d['kodi']['s']=='Paused') {
	$past=past('kodi');
	if ($past>=20&&$past<=30)	kodi('{"jsonrpc": "2.0","method": "GUI.ActivateScreensaver","id": 1}');
}*/