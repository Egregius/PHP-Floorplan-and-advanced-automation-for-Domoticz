<?php
$lastStatus101 = null;
$tvActive = ($d['media']->s == 'On' && $d['eettafel']->s == 0 && ($d['lgtv']->s == 'On' || ($d['nvidia']->s != 'Unavailable' && $d['nvidia']->s != 'Off')));
foreach ($devices as $ip => $vol) {
	$key = 'bose' . $ip;
	if (isset($powerSwitches[$ip]) && ($d[$powerSwitches[$ip]]->s ?? 'Off') != 'On') {
		if($d[$key]->s!='Off') store($key, 'Off', basename(__FILE__) . ':' . __LINE__, 'cron2');
		continue;
	}
	if ($d['guy']->s != 'thuis' || $d['carplay']->s == 'On') {
		if ($d[$key]->s == 'On') {
			$raw = @file_get_contents("http://192.168.2.$ip:8090/now_playing", false, $ctx);
			if ($raw !== false && $raw !== '') {
				$xml = @simplexml_load_string(@mb_convert_encoding($raw, 'UTF-8', 'UTF-8, ISO-8859-1') ?: $raw);
				$status = $xml !== false ? json_decode(json_encode($xml), true) : null;
				if (is_array($status) && ((isset($status['playStatus']) && $status['playStatus'] == 'PLAY_STATE') || (isset($status['@attributes']['source']) && $status['@attributes']['source'] == 'UPNP'))) {
					bosekey("POWER", 0, $ip, basename(__FILE__) . ':' . __LINE__);
				}
			}
			if($d[$key]->s!='Off') store($key, 'Off', basename(__FILE__) . ':' . __LINE__, 'cron2');
		}
		continue;
	}
	$raw = @file_get_contents("http://192.168.2.$ip:8090/now_playing", false, $ctx);
	if ($raw !== false && $raw !== '') {
		$xml = @simplexml_load_string(@mb_convert_encoding($raw, 'UTF-8', 'UTF-8, ISO-8859-1') ?: $raw);
		$status = $xml !== false ? json_decode(json_encode($xml), true) : null;
		if (is_array($status) && isset($status['@attributes']['source'])) {
			if ($ip == 101) {
				$lastStatus101 = $status;
				if ((isset($status['playStatus']) && $status['playStatus'] == 'PLAY_STATE') || (isset($status['@attributes']['source']) && $status['@attributes']['source'] == 'UPNP')) {
					if ($playlisttries > 0) $playlisttries = 0;
					if ($tvActive) {
						$volRaw = @file_get_contents("http://192.168.2.101:8090/volume", false, $ctx);
						if ($volRaw !== false && $volRaw !== '') {
							$volXml = @simplexml_load_string($volRaw);
							$actualvol = $volXml !== false ? json_decode(json_encode($volXml), true) : null;
							if (is_array($actualvol) && isset($actualvol['actualvolume']) && $actualvol['actualvolume'] > 0) {
								bosevolume(0, 101, 'TV aan');
							}
						}
					} else {
						if ($d['boseliving']->m != 'Off' && (isset($status['artist'], $status['track']) || ($status['@attributes']['source'] ?? '') == 'AUX' || ($status['@attributes']['source'] ?? '') == 'UPNP')) {
							if (($status['@attributes']['source'] ?? '') == 'AUX' || ($status['@attributes']['source'] ?? '') == 'UPNP' || (($status['artist'] ?? '') == 'wiim' && ($status['track'] ?? '') == 'dlna cast')) {
								$wiim = json_decode(Wiim('getMetaInfo'));
								if (isset($wiim->metaData->artist)) {
									$status['artist'] = $wiim->metaData->artist;
									$status['track'] = $wiim->metaData->title;
									$wiimplaying = true;
									if (!in_array($status['artist'], ['unknow', 'unknown'], true) && !in_array($status['track'], ['unknow', 'unknown'], true)) $wiimunknown = 0;
								} else {
									$wiimunknown++;
									lg('wiim data not set ' . $wiimunknown, 'cron2');
									if ($wiimunknown >= 16) {
										$wiimunknown = 0;
										Wiim('StartRebootTime:1');
									} elseif ($wiimunknown >= 12) {
										$preset = wiimplaylist();
										Wiim("MCUKeyShortClick:$preset");
									} elseif ($wiimunknown >= 8) {
										$vandaag = date("Y-m-d");
										if ($vandaag != $lastdate) {
											$preset = wiimplaylist();
											Wiim("MCUKeyShortClick:$preset");
											$lastdate = $vandaag;
										} else Wiim('setPlayerCmd:resume');
									}
									continue;
								}
							} else {
								lg(basename(__FILE__) . ':' . __LINE__, 'cron2');
								$wiimplaying = false;
							}
							if (isset($status['artist'], $status['track'])) {
								lg(basename(__FILE__) . ':' . __LINE__, 'cron2');
								$cleantitle = cleanTitle($status['artist'], $status['track']);
								if (substr($d['boseliving']->m, 0, 7) != 'NoScore' && $cleantitle && $cleantitle != $prevcleantitle && !in_array($cleantitle, ['unknowunknow', 'unknownunknown', 'unknownaturalaudio', 'unknowroomcorrectionaudio'], true)) {
									lg(basename(__FILE__) . ':' . __LINE__, 'cron2');
									$prevcleantitle = $cleantitle;
									if (isset($history[$cleantitle])) {
										lg($cleantitle . ' skipped op cleantitle', 'cron2');
										if ($wiimplaying === true) Wiim('setPlayerCmd:next');
									} else {
										lg('Adding ' . $cleantitle . ' to history', 'cron2');
										$history[$cleantitle] = ($history[$cleantitle] ?? 0) + 1;
										while (count($history) > 10) {
											reset($history);
											$oldestKey = key($history);
											unset($history[$oldestKey]);
										}
									}
								} elseif (isset($wiim)) {
									lg(basename(__FILE__) . ':' . __LINE__, 'cron2');
									lg(json_encode($wiim));
									if (in_array($wiim->metaData->artist ?? '', ['unknow', 'unknown'], true) && in_array($wiim->metaData->album ?? '', ['unknow', 'unknown'], true)) {
										$wiimunknown++;
										lg('$wiimunknown = ' . $wiimunknown, 'cron2');
										if ($wiimunknown > 10) {
											$wiimunknown = 0;
											$preset = wiimplaylist();
											Wiim("MCUKeyShortClick:$preset");
										}
									}
									unset($wiim);
								}
							}
							$artists = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $status['artist']));
							$title = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $status['track']));
							$cleanKey = cleanTitle($artists, $title);
							if ($cleanKey !== $lastCleanKey && !in_array($title, ['unknow', 'unknown'], true) && !empty($apiUrl) && !empty($secretToken)) {
								$data = [
									'artist' => $status['artist'],
									'title' => $status['track'],
									'clean_key' => $cleanKey,
									'started_at' => date('Y-m-d H:i:s')
								];
								$ch = curl_init($apiUrl);
								curl_setopt_array($ch, [
									CURLOPT_POST => true,
									CURLOPT_POSTFIELDS => json_encode($data),
									CURLOPT_HTTPHEADER => [
										'Content-Type: application/json',
										'X-Auth-Token: ' . $secretToken
									],
									CURLOPT_RETURNTRANSFER => true,
									CURLOPT_TIMEOUT => 3
								]);
								curl_exec($ch);
								curl_close($ch);
								$lastCleanKey = $cleanKey;
							}
						} else {
							lg(basename(__FILE__) . ':' . __LINE__, 'cron2');
						}
						if ($d['boseliving']->s == 'On' && $boseSetVolume == 0) {
							$volRaw = @file_get_contents("http://192.168.2.101:8090/volume", false, $ctx);
							if ($volRaw !== false && $volRaw !== '') {
								$volXml = @simplexml_load_string($volRaw);
								$actualvol = $volXml !== false ? json_decode(json_encode($volXml), true) : null;
								if (is_array($actualvol) && isset($actualvol['actualvolume']) && $actualvol['actualvolume'] < 35) {
									bosevolume(35, 101, 'Bose pas ingeschakeld, volume was ' . $actualvol['actualvolume']);
									$boseSetVolume = 1;
								}
							}
						}
					}
				} elseif (in_array(($status['@attributes']['source'] ?? ''), ['STANDBY', 'SETUP', 'BLUETOOTH', 'INVALID_SOURCE'], true) && $d['boseliving']->s == 'On' && !$tvActive) {
					lg(basename(__FILE__) . ':' . __LINE__, 'cron2');
					bosekey("AUX_INPUT", 0, 101);
					if (in_array(($status['@attributes']['source'] ?? ''), ['STANDBY', 'SETUP'], true)) {
						usleep(100000);
						bosekey("AUX_INPUT", 0, 101);
					}
				}
			}
			if ((isset($status['playStatus']) && $status['playStatus'] == 'PLAY_STATE') || (isset($status['@attributes']['source']) && $status['@attributes']['source'] == 'UPNP')) {
				if($d[$key]->s!='On') store($key, 'On', basename(__FILE__) . ':' . __LINE__, 'cron2');
			} elseif (($status['@attributes']['source'] == 'STANDBY' || (isset($status['playStatus']) && $status['playStatus'] == 'STOP_STATE')) && !$tvActive && ($d['weg']->s == 0 || ($d['weg']->s == 1 && $d['badkamerpower']->s == 'On'))) {
				bosezone($ip, $vol);
			}
		} else {
			if ($ip == 101) $lastStatus101 = null;
			if($d[$key]->s!='Off') store($key, 'Off', basename(__FILE__) . ':' . __LINE__, 'cron2');
		}
		unset($status, $xml, $raw);
	} else {
		if ($ip == 101) $lastStatus101 = null;
		if($d[$key]->s!='Off') store($key, 'Off', basename(__FILE__) . ':' . __LINE__, 'cron2');
	}
}
if ($d['boseliving']->s != 'On' && $d['boseliving']->s != 'Playing' && $d['boseliving']->s != 'Unavailable' ) {
	//store('bose101', 'Off', basename(__FILE__) . ':' . __LINE__, 'cron2');
	$boseSetVolume = 0;
}
if ($d['bose101']->s == 'On'
	&& $d['bose102']->s == 'Off'
	&& $d['bose103']->s == 'Off'
	&& $d['bose104']->s == 'Off'
	&& $d['bose105']->s == 'Off'
	&& $d['bose106']->s == 'Off'
	&& $d['bose107']->s == 'Off'
	&& $d['bose108']->s == 'Off'
	&& $d['bose109']->s == 'Off'
	&& ($d['weg']->s > 0 || ($d['eettafel']->s == 0 && ($d['lgtv']->s == 'On' || $d['nvidia']->s == 'On')))
	&& past('bose101') > 300
	&& past('boseliving') > 1800
) {
	if (!empty($lastStatus101) && isset($lastStatus101['@attributes']['source']) && $lastStatus101['@attributes']['source'] != 'STANDBY') {
		bosekey("POWER", 0, 101, basename(__FILE__) . ':' . __LINE__);
		foreach (array_keys($devices) as $devIp) {
			if($d['bose' . $devIp]->s!='Off') store('bose' . $devIp, 'Off', basename(__FILE__) . ':' . __LINE__, 'cron2');
		}
		if ($d['boseliving']->s != 'Off') sw('boseliving', 'Off', basename(__FILE__) . ':' . __LINE__, 'cron2');
		$boseSetVolume = 0;
	}
}
if ($d['weg']->s==0&&$d['auto']->s=='On') {
	if ($d['nas']->s=='Off') {
		$kodi_last_action=explode('-',$d['kodi_last_action']->s);
		if ($d['lgtv']->s=='On'||in_array($kodi_last_action[0],['GUI.OnScreensaverDeactivated','window_Beginscherm'])) {
			$kodi=@json_decode(@file_get_contents($kodiurl.'/jsonrpc?request={"jsonrpc":"2.0","id":"1","method":"JSONRPC.Ping"}', false, $ctx), true);
			if (isset($kodi['result'])) {
				lg('Waking NAS for Kodi...','cron2');
				shell_exec('/var/www/html/secure/wakenas.sh &');
				unset($kodi);
			}
			if (past('lgtv')>=20&&past('lgtv')<=30) hassinput('media_player','select_source','media_player.lgtv','HDMI 4');
		}
		if (past('pirhall')<300) {
			$kodi=@json_decode(@file_get_contents($kodiurl2.'/jsonrpc?request={"jsonrpc":"2.0","id":"1","method":"JSONRPC.Ping"}', false, $ctx), true);
			if (isset($kodi['result'])) {
				lg('Waking NAS for Kodi 2...','cron2');
				shell_exec('/var/www/html/secure/wakenas.sh &');
				unset($kodi);
			}
		}
	}
}
