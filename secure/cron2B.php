<?php
foreach ($devices as $ip => $vol) {
    $status = @file_get_contents("http://192.168.2.$ip:8090/now_playing", false, $ctx);
//    lg($boses[$ip].' = '.print_r($status,true));
    if (isset($status)) {
		$status = json_decode(json_encode(simplexml_load_string($status)), true);
		if (is_array($status)) {
//			if ($ip==107) lg($boses[$ip].' = '.print_r($status,true));
			if (isset($status['@attributes']['source'])) {
				if ($d['bose'.$ip]['m'] != 'Online' && $d['boseliving']['s'] != 'On') {
					store('boseliving', 'On', basename(__FILE__).':'.__LINE__,1);
				} elseif ($d['bose'.$ip]['m'] != 'Online') {
					storemode('bose'.$ip, 'Online', basename(__FILE__).':'.__LINE__, true);
				}
				if ($status['@attributes']['source'] == 'STANDBY') {
					lg(basename(__FILE__).':'.__LINE__);
					bosezone($ip,$vol);
				} elseif ($status['@attributes']['source'] == 'INVALID_SOURCE') {
					$invalidcounter++;
					if ($invalidcounter > 10) {
						lg('invalidcounter = '.$invalidcounter);
						bosekey("POWER", 0, 101);
						if ($d['bose'.$ip]['s'] == 'On') sw('bose'.$ip, 'Off', basename(__FILE__).':'.__LINE__);
						if ($d['boseliving']['s'] != 'Off') {
							store('boseliving', 'Off', basename(__FILE__).':'.__LINE__,1);
							sleep(5);
							store('boseliving', 'On', basename(__FILE__).':'.__LINE__,1);
							$invalidcounter = 0;
						}
					}
				}
				if (isset($status['playStatus']) && $status['playStatus'] == 'PLAY_STATE') {
					if ($d['bose'.$ip]['s'] == 'Off') store('bose'.$ip, 'On', basename(__FILE__).':'.__LINE__,1);
					if ($invalidcounter > 0) $invalidcounter = 0;
				}
			} else {
				if ($d['bose'.$ip]['s'] == 'On' || $d['bose'.$ip]['m'] != 'Offline') storesm('bose'.$ip, 'Off', 'Offline', basename(__FILE__).':'.__LINE__, true);
			}
		} else {
			if ($d['bose'.$ip]['s'] == 'On' || $d['bose'.$ip]['m'] != 'Offline') storesm('bose'.$ip, 'Off', 'Offline', basename(__FILE__).':'.__LINE__, true);
		}
		unset($status);
	} else {
		if ($d['bose'.$ip]['s'] == 'On' || $d['bose'.$ip]['m'] != 'Offline') storesm('bose'.$ip, 'Off', 'Offline', basename(__FILE__).':'.__LINE__, true);
	}
}
if($d['boseliving']['s']!='On'&&$d['boseliving']['s']!='Playing') {
	if ($d['bose101']['s'] == 'On' || $d['bose101']['m'] != 'Offline') storesm('bose101', 'Off', 'Offline', basename(__FILE__).':'.__LINE__, true);
}
if ($d['bose101']['m']==1
	&&$d['bose101']['s']=='On'
	&&$d['bose102']['s']=='Off'
	&&$d['bose103']['s']=='Off'
	&&$d['bose104']['s']=='Off'
	&&$d['bose105']['s']=='Off'
	&&$d['bose106']['s']=='Off'
	&&$d['bose107']['s']=='Off'
	&&($d['weg']['s']>0||($d['lgtv']['s']=='On'&&$d['eettafel']['s']==0))
	&&past('bose101')>90
	&&past('bose102')>90
	&&past('bose103')>90
	&&past('bose104')>90
	&&past('bose105')>90
	&&past('bose106')>90
	&&past('bose107')>90
) {
	$status=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.101:8090/now_playing"))),true);
	if (!empty($status)) {
		if (isset($status['@attributes']['source'])) {
			if ($status['@attributes']['source']!='STANDBY') {
				bosekey("POWER", 0, 101);
				if ($d['bose101']['s']!='Off') store('bose101', 'Off', basename(__FILE__).':'.__LINE__,1);
				if ($d['bose102']['s']!='Off') store('bose102', 'Off', basename(__FILE__).':'.__LINE__,1);
				if ($d['bose103']['s']!='Off') store('bose103', 'Off', basename(__FILE__).':'.__LINE__,1);
				if ($d['bose104']['s']!='Off') store('bose104', 'Off', basename(__FILE__).':'.__LINE__,1);
				if ($d['bose105']['s']!='Off') store('bose105', 'Off', basename(__FILE__).':'.__LINE__,1);
				if ($d['bose106']['s']!='Off') store('bose106', 'Off', basename(__FILE__).':'.__LINE__,1);
				if ($d['bose107']['s']!='Off') store('bose107', 'Off', basename(__FILE__).':'.__LINE__,1);
				if ($d['boseliving']['s']!='Off') sw('boseliving', 'Off', basename(__FILE__).':'.__LINE__);
			}
		}
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
		if (past('media')>45&&past('media')<60) sw('shieldpower', 'On','',true);
	}	
}
