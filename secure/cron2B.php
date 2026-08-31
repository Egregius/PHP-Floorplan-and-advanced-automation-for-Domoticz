<?php
foreach ($devices as $ip => $vol) {
//	continue;
	$status = @file_get_contents("http://192.168.2.$ip:8090/now_playing", false, $ctx);
   
	if (isset($status)) {
		$status = json_decode(json_encode(simplexml_load_string(mb_convert_encoding($status, 'UTF-8', mb_detect_encoding($status, 'UTF-8, ISO-8859-1', true)))), true);
		if (is_array($status)) {
			if ($ip==101) {
				if(isset($status['playStatus']) && $status['playStatus'] == 'PLAY_STATE') {
					if($playlisttries>0) $playlisttries=0;
					if ($d['media']->s=='On'&&$d['eettafel']->s==0&&($d['lgtv']->s=='On'||($d['nvidia']->s!='Unavailable'&&$d['nvidia']->s!='Off'))) {
						$actualvol = @file_get_contents("http://192.168.2.101:8090/volume", false, $ctx);
						if (isset($actualvol)) {
							$actualvol = json_decode(json_encode(simplexml_load_string($actualvol)), true);
							if (is_array($actualvol)) {
								if($actualvol['actualvolume']>0) bosevolume(0,101, 'TV aan');
							}
						}
					} else {
						$start = hrtime(true);
						if($d['boseliving']->m == 1 && (isset($status['artist'],$status['track'])||$status['@attributes']['source']=='AUX'||$status['@attributes']['source']=='UPNP')) {
							if($status['@attributes']['source']=='AUX'||$status['@attributes']['source']=='UPNP'||($status['artist']=='wiim'&&$status['track']=='dlna cast')) {
								$wiim=json_decode(Wiim('getMetaInfo'));
//								lg(print_r($wiim,true),'cron2');
								$status['artist']=$wiim->metaData->artist;
								$status['track']=$wiim->metaData->title;
								$wiimplaying=true;
							} else $wiimplaying=false;
							if(isset($status['artist'],$status['track'])) {
								$cleantitle=cleanTitle($status['artist'],$status['track']);
								if ($cleantitle && $cleantitle!=$prevcleantitle && !in_array($cleantitle,['unknowunknow','unknownaturalaudio','unknowroomcorrectionaudio'])) {
									$prevcleantitle=$cleantitle;
									if (isset($history[$cleantitle])&&1==1) {
										if(!in_array($cleantitle, $toplist)) {
											lg($cleantitle.' skipped op cleantitle','cron2');
											if($wiimplaying===true) Wiim('setPlayerCmd:next');
											else ma_next_track();
										}
									} else {
										lg('Adding '.$cleantitle.' to history','cron2');
										$history[$cleantitle] = ($history[$cleantitle] ?? 0) + 1;
										while (count($history) > 10) {
											reset($history);
											$oldestKey = key($history);
											unset($history[$oldestKey]);
										}
									}
/*									if ($historyruns>50) {
										$elapsed = round((hrtime(true) - $start) / 1e+6, 3);
										file_put_contents('/var/www/spotifyhistory.json', json_encode($history));
										gc_collect_cycles();
										$vars = get_defined_vars();
										$total_var_size=0;
										foreach ($vars as $name => $value) {
											if (in_array($name, [
												'GLOBALS', '_POST', '_GET', '_COOKIE', '_FILES', '_SERVER', '_ENV',
												'memory_cache', 'name', 'vars', 'value', 'size', 'oldSize', 'percent', 'usage_report'
											])) continue;
											if ($value instanceof PDO || $value instanceof PDOStatement || is_resource($value)) {
												$size = 0;
											} else {
												try {
													$size = strlen(serialize($value));
												} catch (Exception $e) {
													$size = 0;
												}
											}
											$total_var_size += $size;
											if (isset($memory_cache[$name]) && $memory_cache[$name] > 0) {
												$oldSize = $memory_cache[$name];
												if ($size > ($oldSize * 1.05)) {
													$percent = round((($size - $oldSize) / $oldSize) * 100, 1);
													lg("📈 \${$name}	+{$percent}% (" . convertbytes($oldSize) . "	-> " . convertbytes($size) . ")",'cron2');
													$memory_cache[$name] = $size;
												}
											} else $memory_cache[$name] = $size;
										}
										unset($vars, $name, $value, $size, $oldSize, $percent);
										lg('🕒 Variabelen: ' . convertbytes($total_var_size) . ' | Intern: ' . convertbytes(memory_get_usage(false)) . ' | Systeem: ' . convertbytes(memory_get_usage(true)).' | history: '.count($history).' items | '.$elapsed. ' milliseconds','cron2');
										$historyruns=0;
									}
									$historyruns++;*/
								} elseif (isset($wiim)) {
//									lg(print_r($wiim,true),'cron2');
									if($wiim->metaData->artist=='unknow'&&$wiim->metaData->album=='unknow') {
										$wiimunknown++;
										lg('$wiimunknown = '.$wiimunknown,'cron2');
										if($wiimunknown>10) {
											$wiimunknown=0;
											$preset=wiimplaylist();
											Wiim("MCUKeyShortClick:$preset");
											sleep(1);
											Wiim("setPlayerCmd:playindex:1");
										}
									}
									unset($wiim);
								}
							}
							$artists = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $status['artist']));
							$title   = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $status['track']));
							$title   = preg_replace('/\b(19|20)\d{2}\b/', '', $title);
							$arr     = array_map('trim', explode(',', $artists));
							sort($arr);
							$cleanKey = preg_replace('/[^a-z0-9]/', '', implode('', $arr) . $title);
					
							if ($cleanKey !== $lastCleanKey && $title!='unknow') {
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
						}
						if($d['boseliving']->s=='On') {
							$pastboseliving=past('boseliving');
							if($pastboseliving>60&&$pastboseliving<120) {
								$actualvol = @file_get_contents("http://192.168.2.101:8090/volume", false, $ctx);
								if (isset($actualvol)) {
									$actualvol = json_decode(json_encode(simplexml_load_string($actualvol)), true);
									if (is_array($actualvol)) {
										if($actualvol['actualvolume']<35) bosevolume(35,101, 'Bose pas ingeschakeld');
									}
								}
							}
						}
					}
				} elseif ($status['@attributes']['source']=="STANDBY"||$status['@attributes']['source']=="SETUP") {
//					streborn(101,'box/source','{"source":"AUX","sourceAccount":"AUX"}');
					bosekey("AUX_INPUT", 0, 101);
					usleep(100000);
					bosekey("AUX_INPUT", 0, 101);
				} elseif ($status['@attributes']['source']=="BLUETOOTH") {
//					streborn(101,'box/source','{"source":"AUX","sourceAccount":"AUX"}');
					bosekey("AUX_INPUT", 0, 101);
				}// else lg(print_r($status,true),'cron2');
				
			}
			if (isset($status['@attributes']['source'])) {
				if (/*$d['bose'.$ip]->m != 'Online' && */$d['boseliving']->s != 'On'&&($d['lgtv']->s=='Off'||($d['lgtv']->s=='On'&&$d['time']<strtotime('8:00')))) {
//					lg(basename(__FILE__).':'.__LINE__);
	//				sw('boseliving', 'On');
				} elseif ($d['bose'.$ip]->m != 1) {
					storemode('bose'.$ip, 1,basename(__FILE__).':'.__LINE__,'cron2');
					$d['bose'.$ip]->m=1;

				}
				if (($status['@attributes']['source'] == 'STANDBY'||(isset($status['playStatus'])&&$status['playStatus'] == 'STOP_STATE')) && ($d['weg']->s==0||($d['weg']->s==1&&$d['badkamerpower']->s=='On'))) {
					bosezone($ip,$vol);
				}
				if (isset($status['playStatus']) && $status['playStatus'] == 'PLAY_STATE') {
					if ($d['bose'.$ip]->s == 'Off') {
						store('bose'.$ip, 'On');
					}
				}
			} else {
				if ($d['bose'.$ip]->s == 'On' || $d['bose'.$ip]->m != 0) storesm('bose'.$ip, 'Off', 0,basename(__FILE__).':'.__LINE__,'cron2');
			}
		} else {
			if ($d['bose'.$ip]->s == 'On' || $d['bose'.$ip]->m != 0) {
				storesm('bose'.$ip, 'Off', 0,basename(__FILE__).':'.__LINE__,'cron2');
			}
		}
		unset($status);
	} else {
		if ($d['bose'.$ip]->s == 'On' || $d['bose'.$ip]->m != 0) storesm('bose'.$ip, 'Off', 0,basename(__FILE__).':'.__LINE__,'cron2');
	}
}
if($d['boseliving']->s!='On'&&$d['boseliving']->s!='Playing'&&$d['boseliving']->s!='Unavailable') {
	if ($d['bose101']->s == 'On' || $d['bose101']->m != 0) storesm('bose101', 'Off', 0,basename(__FILE__).':'.__LINE__,'cron2');
}
if ($d['bose101']->s=='On'
	&&$d['bose102']->s=='Off'
	&&$d['bose103']->s=='Off'
	&&$d['bose104']->s=='Off'
	&&$d['bose105']->s=='Off'
	&&$d['bose106']->s=='Off'
	&&$d['bose107']->s=='Off'
	&&$d['bose108']->s=='Off'
	&&$d['bose109']->s=='Off'
	&&($d['weg']->s>0||($d['eettafel']->s==0&&($d['lgtv']->s=='On'||$d['nvidia']->s=='On')))
	&&past('bose101')>300
	&&past('boseliving')>1800
) {
	$status=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.101:8090/now_playing"))),true);
	if (!empty($status)) {
		if (isset($status['@attributes']['source'])) {
			if ($status['@attributes']['source']!='STANDBY') {
				bosekey("POWER", 0, 101,basename(__FILE__).':'.__LINE__);
				if ($d['bose101']->s!='Off') store('bose101', 'Off',basename(__FILE__).':'.__LINE__,'cron2');
				if ($d['bose102']->s!='Off') store('bose102', 'Off',basename(__FILE__).':'.__LINE__,'cron2');
				if ($d['bose103']->s!='Off') store('bose103', 'Off',basename(__FILE__).':'.__LINE__,'cron2');
				if ($d['bose104']->s!='Off') store('bose104', 'Off',basename(__FILE__).':'.__LINE__,'cron2');
				if ($d['bose105']->s!='Off') store('bose105', 'Off',basename(__FILE__).':'.__LINE__,'cron2');
				if ($d['bose106']->s!='Off') store('bose106', 'Off',basename(__FILE__).':'.__LINE__,'cron2');
				if ($d['bose107']->s!='Off') store('bose107', 'Off',basename(__FILE__).':'.__LINE__,'cron2');
				if ($d['bose108']->s!='Off') store('bose108', 'Off',basename(__FILE__).':'.__LINE__,'cron2');
				if ($d['bose109']->s!='Off') store('bose109', 'Off',basename(__FILE__).':'.__LINE__,'cron2');
				if ($d['boseliving']->s!='Off') sw('boseliving', 'Off',basename(__FILE__).':'.__LINE__,'cron2');
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
