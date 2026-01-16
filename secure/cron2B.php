<?php
foreach ($devices as $ip => $vol) {
    $status = @file_get_contents("http://192.168.2.$ip:8090/now_playing", false, $ctx);
//    lg($boses[$ip].' = '.print_r($status,true));
    if (isset($status)) {
		$status = json_decode(json_encode(simplexml_load_string($status)), true);
		if (is_array($status)) {
			if ($ip==101) {
//				lg($boses[$ip].' = '.print_r($status,true));
				if (isset($status['@attributes']['source'])&&$status['@attributes']['source']=='SPOTIFY') {
//					lg(basename(__FILE__).':'.__LINE__);
					if (isset($status['ContentItem']['@attributes']['type'])&&$status['ContentItem']['@attributes']['type']=='DO_NOT_RESUME') {
						lg(basename(__FILE__).':'.__LINE__);
						bosepreset(boseplaylist(), 101);
					}
				}
				if ($status['@attributes']['source'] == 'INVALID_SOURCE') {
					$invalidcounter++;
					if ($invalidcounter > 10) {
						lg('invalidcounter = '.$invalidcounter);
						bosekey("POWER", 0, 101);
						if ($d['bose'.$ip]->s == 'On') sw('bose'.$ip, 'Off', basename(__FILE__).':'.__LINE__);
						if ($d['boseliving']->s != 'Off') {
							sw('boseliving', 'Off');
							sleep(5);
							sw('boseliving', 'On');
							$invalidcounter = 0;
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
//					if ($ip>101&&$d['boseliving']->s=='Off'&&$d['time']<strtotime('18:00')) sw('boseliving', 'On');
				}
				if ($status['@attributes']['source'] == 'STANDBY') {
//					lg(basename(__FILE__).':'.__LINE__);
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
	&&($d['weg']->s>0||($d['lgtv']->s=='On'&&$d['eettafel']->s==0))
	&&past('bose101')>300
	&&past('boseliving')>1800
//	&&past('bose102')>30
//	&&past('bose103')>30
//	&&past('bose104')>30
//	&&past('bose105')>30
//	&&past('bose106')>30
//	&&past('bose107')>30
) {
	$status=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.101:8090/now_playing"))),true);
	if (!empty($status)) {
		if (isset($status['@attributes']['source'])) {
			if ($status['@attributes']['source']!='STANDBY') {
				bosekey("POWER", 0, 101);
				if ($d['bose101']->s!='Off') store('bose101', 'Off');
				if ($d['bose102']->s!='Off') store('bose102', 'Off');
				if ($d['bose103']->s!='Off') store('bose103', 'Off');
				if ($d['bose104']->s!='Off') store('bose104', 'Off');
				if ($d['bose105']->s!='Off') store('bose105', 'Off');
				if ($d['bose106']->s!='Off') store('bose106', 'Off');
				if ($d['bose107']->s!='Off') store('bose107', 'Off');
				if ($d['boseliving']->s!='Off') sw('boseliving', 'Off');
			}
		}
	}
}
/*if ($d['media']->s=='On') {
	if ($d['lgtv']->s=='Off'&&past('media')>1800&&past('lgtv')>900) {
		if (ping('192.168.2.6')===false&&ping('192.168.2.7')===false&&ping('192.168.2.6')===false&&ping('192.168.2.28')===false) sw('media', 'Off', basename(__FILE__).':'.__LINE__);
	}
}	*/
if ($d['weg']->s==0&&$d['auto']->s=='On') {
	if ($d['nas']->s=='Off') {
		$kodi=substr($d['kodi_last_action']->s,0,5);
		if ($d['lgtv']->s=='On'||in_array($kodi,['GUI.O','windo'])) {
//			lg(basename(__FILE__).':'.__LINE__);
			$kodi=@json_decode(@file_get_contents($kodiurl.'/jsonrpc?request={"jsonrpc":"2.0","id":"1","method":"JSONRPC.Ping","id":1}', false, $ctx), true);
			if (isset($kodi['result'])) {
//				lg(basename(__FILE__).':'.__LINE__);
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
	/*if ($d['media']->s=='On') {
		if ($d['lgtv']->s=='Off'&&past('media')>1800&&past('lgtv')>900) {
			if (ping('192.168.2.6')===false&&ping('192.168.2.7')===false&&ping('192.168.2.28')===false) sw('media', 'Off', basename(__FILE__).':'.__LINE__);
		}
		if ($d['shieldpower']->s=='Off'&&past('lgtv')>30) sw('shieldpower', 'On','',true);
	}	*/
}
