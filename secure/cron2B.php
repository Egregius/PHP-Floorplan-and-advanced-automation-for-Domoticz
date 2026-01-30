<?php
foreach ($devices as $ip => $vol) {
    $status = @file_get_contents("http://192.168.2.$ip:8090/now_playing", false, $ctx);
    if (isset($status)) {
		$status = json_decode(json_encode(simplexml_load_string($status)), true);
		if (is_array($status)) {
			if ($ip==101) {
				if (isset($status['@attributes']['source'])&&$status['@attributes']['source']=='SPOTIFY') {
					if (isset($status['ContentItem']['@attributes']['type'])&&$status['ContentItem']['@attributes']['type']=='DO_NOT_RESUME') {
						lg(basename(__FILE__).':'.__LINE__);
						bosepreset(boseplaylist(), 101);
					}
				}
				if ($status['@attributes']['source'] == 'INVALID_SOURCE') {
					$invalidcounter++;
					if ($invalidcounter > 10) {
						lg('Bose living $invalidcounter = '.$invalidcounter.' Toggling Bose');
						bosekey("POWER", 0, 101, basename(__FILE__).':'.__LINE__);
						if ($d['bose'.$ip]->s == 'On') sw('bose'.$ip, 'Off', basename(__FILE__).':'.__LINE__);
						if ($d['boseliving']->s != 'Off') {
							sw('boseliving', 'Off', basename(__FILE__).':'.__LINE__);
							sleep(5);
							sw('boseliving', 'On', basename(__FILE__).':'.__LINE__);
							$invalidcounter = 0;
						}
					} else lg('Bose living $invalidcounter = '.$invalidcounter);
				}
				if(isset($status['playStatus']) && $status['playStatus'] == 'PLAY_STATE'&&$d['media']->s=='On'&&($d['eettafel']->s==0&&($d['lgtv']->s=='On'||$d['nvidia']->s!='Unavailable'))) {
					 $vol = @file_get_contents("http://192.168.2.101:8090/volume", false, $ctx);
					 if (isset($vol)) {
						$vol = json_decode(json_encode(simplexml_load_string($vol)), true);
						if (is_array($vol)) {
							if($vol['actualvolume']>0) bosevolume(0,101, 'TV aan');
						}
					}
				}
			}
			if (isset($status['@attributes']['source'])) {
				if (/*$d['bose'.$ip]->m != 'Online' && */$d['boseliving']->s != 'On'&&($d['lgtv']->s=='Off'||($d['lgtv']->s=='On'&&$d['time']<strtotime('8:00')))) {
//					lg(basename(__FILE__).':'.__LINE__);
	//				sw('boseliving', 'On');
				} elseif ($d['bose'.$ip]->m != 1) {
					storemode('bose'.$ip, 1,basename(__FILE__).':'.__LINE__);
					$d['bose'.$ip]->m=1;
				}
				if ($status['@attributes']['source'] == 'STANDBY') {
					if ($ip==101) bosepreset(boseplaylist());
					elseif ($ip==105&&$d['time']>=strtotime('6:00')&&$d['time']<strtotime('18:00')) bosezone($ip,$vol);
					elseif ($ip!=105&&$d['time']<strtotime('20:00')) bosezone($ip,$vol);
				}
				if (isset($status['playStatus']) && $status['playStatus'] == 'PLAY_STATE') {
					if ($d['bose'.$ip]->s == 'Off') store('bose'.$ip, 'On');
					if ($invalidcounter > 0) $invalidcounter = 0;
				}
			} else {
				if ($d['bose'.$ip]->s == 'On' || $d['bose'.$ip]->m != 0) storesm('bose'.$ip, 'Off', 0,basename(__FILE__).':'.__LINE__);
			}
		} else {
			if ($d['bose'.$ip]->s == 'On' || $d['bose'.$ip]->m != 0) storesm('bose'.$ip, 'Off', 0,basename(__FILE__).':'.__LINE__);
		}
		unset($status);
	} else {
		if ($d['bose'.$ip]->s == 'On' || $d['bose'.$ip]->m != 0) storesm('bose'.$ip, 'Off', 0,basename(__FILE__).':'.__LINE__);
	}
}
if($d['boseliving']->s!='On'&&$d['boseliving']->s!='Playing'&&$d['boseliving']->s!='Unavailable') {
	if ($d['bose101']->s == 'On' || $d['bose101']->m != 0) storesm('bose101', 'Off', 0,basename(__FILE__).':'.__LINE__);
}
if ($d['bose101']->s=='On'
	&&$d['bose102']->s=='Off'
	&&$d['bose103']->s=='Off'
	&&$d['bose104']->s=='Off'
	&&$d['bose105']->s=='Off'
	&&$d['bose106']->s=='Off'
	&&$d['bose107']->s=='Off'
	&&($d['weg']->s>0||($d['eettafel']->s==0&&($d['lgtv']->s=='On'||$d['nvidia']->s=='On')))
	&&past('bose101')>300
	&&past('boseliving')>1800
) {
	$status=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.101:8090/now_playing"))),true);
	if (!empty($status)) {
		if (isset($status['@attributes']['source'])) {
			if ($status['@attributes']['source']!='STANDBY') {
				bosekey("POWER", 0, 101,basename(__FILE__).':'.__LINE__);
				if ($d['bose101']->s!='Off') store('bose101', 'Off',basename(__FILE__).':'.__LINE__);
				if ($d['bose102']->s!='Off') store('bose102', 'Off',basename(__FILE__).':'.__LINE__);
				if ($d['bose103']->s!='Off') store('bose103', 'Off',basename(__FILE__).':'.__LINE__);
				if ($d['bose104']->s!='Off') store('bose104', 'Off',basename(__FILE__).':'.__LINE__);
				if ($d['bose105']->s!='Off') store('bose105', 'Off',basename(__FILE__).':'.__LINE__);
				if ($d['bose106']->s!='Off') store('bose106', 'Off',basename(__FILE__).':'.__LINE__);
				if ($d['bose107']->s!='Off') store('bose107', 'Off',basename(__FILE__).':'.__LINE__);
				if ($d['boseliving']->s!='Off') sw('boseliving', 'Off',basename(__FILE__).':'.__LINE__);
			}
		}
	}
}
if ($d['weg']->s==0&&$d['auto']->s=='On') {
	if ($d['nas']->s=='Off') {
		$kodi_last_action=explode('-',$d['kodi_last_action']->s);
		if ($d['lgtv']->s=='On'||in_array($kodi_last_action[0],['GUI.OnScreensaverDeactivated','window_Beginscherm'])) {
			$kodi=@json_decode(@file_get_contents($kodiurl.'/jsonrpc?request={"jsonrpc":"2.0","id":"1","method":"JSONRPC.Ping"}', false, $ctx), true);
			if (isset($kodi['result'])) {
				lg('Waking NAS for Kodi...');
				shell_exec('/var/www/html/secure/wakenas.sh &');
				unset($kodi);
			}
			if (past('lgtv')>=20&&past('lgtv')<=30) hassinput('media_player','select_source','media_player.lgtv','HDMI 4');
		}
		if (past('pirhall')<300) {
			$kodi=@json_decode(@file_get_contents($kodiurl2.'/jsonrpc?request={"jsonrpc":"2.0","id":"1","method":"JSONRPC.Ping"}', false, $ctx), true);
			if (isset($kodi['result'])) {
				lg('Waking NAS for Kodi 2...');
				shell_exec('/var/www/html/secure/wakenas.sh &');
				unset($kodi);
			}
		}
	} elseif ($d['nas']->s=='On'&&$d['nvidia']->m=='Kodi'&&past('nas')>90) {
		$kodi_last_action=explode('-',$d['kodi_last_action']->s);
		if (!isset($lastlibraryupdate)||$lastlibraryupdate<$time-72000) {
			if(in_array($kodi_last_action[0],['GUI.OnScreensaverDeactivated','GUI.OnScreensaverActivated','window_Beginscherm'])) {
				$lastlibraryupdate=$time;
				kodi('{"jsonrpc":"2.0","id":1,"method":"Input.Back"}');
				kodi('{"jsonrpc":"2.0","id":1,"method": "VideoLibrary.Scan","params": {"showdialogs": true}}');
			}
		}
		if (
			(!isset($lastlibraryclean) || $lastlibraryclean < $time - 72000)
			&& isset($lastlibraryupdate)
			&& $lastlibraryupdate < $time - 60
			&& ($d['kodi_last_action']->t < $lastlibraryupdate || $kodi_last_action[0]=='GUI.OnScreensaverActivated')
		) {
			$lastlibraryclean=$time;
			kodi('{"jsonrpc":"2.0","id":1,"method":"Input.Back"}');
			kodi('{"jsonrpc":"2.0","id":1,"method": "VideoLibrary.Clean","params": {"showdialogs": true}}');
		}
	}
}
