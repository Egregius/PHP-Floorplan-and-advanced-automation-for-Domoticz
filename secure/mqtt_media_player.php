#!/usr/bin/php
<?php
declare(strict_types=1);
ini_set('error_reporting',E_ALL);
ini_set('display_errors',true);
gc_enable();
// Using https://github.com/php-mqtt/client
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
require_once '/var/www/vendor/autoload.php';
require '/var/www/html/secure/functions.php';
$user='MEDIA';
lg('🟢 Starting '.$user.' loop ','mediaplayer');
$time=time();
$lastcheck=$time;
$t = null;
$weekend = null;
$dow = null;
$startloop=time();
define('LOOP_START', $startloop);
$lastEvent=$startloop;
$connectionSettings=(new ConnectionSettings)
	->setUsername('mqtt')
    ->setPassword('mqtt')
    ->setKeepAliveInterval(60);
$mqtt=new MqttClient('192.168.30.22',1883,basename(__FILE__) . '_' . getmypid().VERSIE,MqttClient::MQTT_3_1);
$mqtt->connect($connectionSettings,true);
$validDevices = [];
foreach (glob('/var/www/html/secure/pass2php/*.php') as $file) {
	$basename = basename($file, '.php');
	$validDevices[$basename] = true;
}
$d=fetchdata();
$d['rand']=rand(100,200);
$d['rand']=5;
updateWekker($t, $weekend, $dow, $d);
$mqtt->subscribe('homeassistant/media_player/+/state',function (string $topic,string $status) use ($startloop,$validDevices,&$d, &$lastcheck, &$time, $user, $wifiiot) {
	try {
		$path=explode('/',$topic);
		$device=$path[2];
		if (isset($validDevices[$device])) {
			$time=time();
			if (($time - LOOP_START) <= 2) return;
			$d['time']=$time;
			$d=fetchdata();
			$status = ucfirst(strtolower($status));
			if ($d[$device]->s!=$status) {
				include '/var/www/html/secure/pass2php/'.$device.'.php';
				store($device,$status,'','mediaplayer');
			}
		}
	} catch (Throwable $e) {
		lg("Fout in {$user}: ".__LINE__.' '.$topic.' '.$e->getMessage(),'mediaplayer');
	}
	if ($lastcheck < $time - $d['rand']) {
        $lastcheck = $time;
        stoploop();
        updateWekker($t, $weekend, $dow, $d);
    }
},MqttClient::QOS_AT_LEAST_ONCE);

$mqtt->subscribe('homeassistant/media_player/+/source',function (string $topic,string $status) use ($startloop,$validDevices,&$d, &$lastcheck, $user) {
	try {
		$path=explode('/',$topic);
		$device=$path[2];
		if ($device=='nvidia') {
			$time=time();
			$d['time']=$time;
			$d=fetchdata();
			$status = ucfirst(strtolower(trim($status, '"')));
			if ($d[$device]->m!=$status) {
				storemode($device,$status,'','mediaplayer');
			}
		}
	} catch (Throwable $e) {
		lg("Fout in MQTT {$user}: " . __LINE__ . ' ' . $topic . ' ' . $e->getMessage(),'mediaplayer');
	}
},MqttClient::QOS_AT_LEAST_ONCE);

$mqtt->subscribe('wiim_raw/+', function (string $topic, string $status) use (&$d, &$lastcheck, &$time, $user) {
	static $lasttrackid = '';
	static $lastartist = '';
	static $lasttitle = '';
	static $lastscore = '';
	static $lastgenre = '';
	static $lastadded_at = '';
	static $current = [];
	try {
		$subtopic = str_replace('wiim_raw/', '', $topic);
		switch ($subtopic) {
			case 'wiim_reltime':
				publishmqtt("d/wiim/wiim_reltime", $status);
				return;
			case 'wiim_artist':
				$time = time();
				$current['wiim_artist'] = $status;
				publishmqtt("d/wiim/wiim_artist", $status);
				break;
			case 'wiim_title':
				$current['wiim_title'] = $status;
				publishmqtt("d/wiim/wiim_title", $status);
				break;
			case 'wiim_currenttrackuri':
				$status=str_replace('spotify:track:', '', $status);
				publishmqtt("d/wiim/wiim_trackid", $status);
				$current['wiim_trackid'] = $status;
				break;
			case 'wiim_album':
				publishmqtt("d/wiim/wiim_album", $status);
				return;
			case 'wiim_album_art':
				publishmqtt("d/wiim/wiim_album_art", $status);
				return;
			case 'wiim_bitdepth':
				publishmqtt("d/wiim/wiim_bitdepth", $status);
				return;
			case 'wiim_samplerate':
				publishmqtt("d/wiim/wiim_samplerate", $status);
				return;
			case 'wiim_bitrate':
				publishmqtt("d/wiim/wiim_bitrate", $status);
				return;
			case 'wiim_transportstate':
			case 'wiim_trackduration':
				publishmqtt("d/wiim/{$subtopic}", $status);
				return;
			default:
				return;
		}
		$trackid = $current['wiim_trackid'] ?? '';
		$artist = $current['wiim_artist'] ?? '';
		$title = $current['wiim_title'] ?? '';
		if (!empty($trackid) && !empty($artist) && !empty($title)) {
			lg($artist.' '.$title, 'mediaplayer');
			if ($trackid !== $lasttrackid || $artist !== $lastartist || $title !== $lasttitle) {
				$lasttrackid = $trackid;
				$lastartist = $artist;
				$lasttitle = $title;

				$resolvedtitle = 'UNKNOWN';
				$score = '?';
				$genre = '?';
				$added_at = '?';

				$db = Database::getInstance();
				$playlists_config = [
					'4c0A3wTsAYGtPjuvnBSQVz' => 'EDM',
					'1HYGeiybmNCNDH5gqgpMlD' => 'EDM',
					'4PAKU1ueS1ZLGHV6RQ61vU' => 'EDM',
					'00c1C9TicWtR4jT8l6Umtn' => 'EDM',
					'0RC4oPTsTDYeJAQ42r18Xo' => 'EDM',
					'6Di8efQzh5qYyL6Th9GovZ' => 'EDM',
					'6ZAcAvI9J054PToebnPCM6' => 'EDM',
					'2KSNeL9Xgsobjdb0PBDEd3' => 'EDM',
					'4qRh2rWz5i76SsnHjnJX84' => 'EDM',
					'2wZyxbgTwnlgOu7qNCxFXG' => 'EDM',
					'4hTylVEtYMWLto7UxmFXVS' => 'POP',
					'6VxheCoIPPcR3PTHUfjDJ6' => 'POP',
					'4ofsYQ8HVOIxNXUwZYuT2C' => 'POP',
					'2y7sX8H7KiFMAYo3NR1Lxz' => 'POP',
					'5NeUAm0FkXS3fWJGnCvzoE' => 'POP',
				];

				// 1. Matchen op track_id
				$stmt = $db->prepare("SELECT clean_title, score, playlist_id, added_at FROM track_mapping WHERE track_id = ? LIMIT 1");
				$stmt->execute([$trackid]);
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				if ($row) {
					$score = (int)$row['score'];
					$added_at = formatBE($row['added_at']);
					$genre = $playlists_config[$row['playlist_id']] ?? '?';
					$resolvedtitle = $row['clean_title'];
				}

				// 2. Exacte clean_title match
				if ($score === '?' && empty($resolvedtitle)) {
					$stmt = $db->prepare("SELECT clean_title, score, playlist_id, added_at FROM track_mapping WHERE clean_title = ? LIMIT 1");
					$stmt->execute([$resolvedtitle]);
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					if ($row) {
						$score = (int)$row['score'];
						$added_at = formatBE($row['added_at']);
						$genre = $playlists_config[$row['playlist_id']] ?? '?';
						$resolvedtitle = $row['clean_title'];
					} else {
						// 3. Fallback LIKE
						$stmt = $db->prepare("SELECT clean_title, score, playlist_id, added_at FROM track_mapping WHERE clean_title LIKE ? LIMIT 1");
						$stmt->execute(['%' . cleanTitle($artist, '') . '%' . cleanTitle('', $title) . '%']);
						$row = $stmt->fetch(PDO::FETCH_ASSOC);
						if ($row) {
							$score = (int)$row['score'];
							$added_at = formatBE($row['added_at']);
							$genre = $playlists_config[$row['playlist_id']] ?? '?';
							$resolvedtitle = $row['clean_title'];
						} else {
							// 4. Extra fallback
							$cleanArtist = cleanTitle($artist, '');
							$cleanTrackWords = cleanTitle('', $title);
							if (!empty($cleanArtist) && !empty($cleanTrackWords)) {
								$stmt = $db->prepare("SELECT clean_title, score, playlist_id, added_at FROM track_mapping WHERE clean_title LIKE ? AND clean_title LIKE ? LIMIT 1");
								$stmt->execute([$cleanArtist . '%', '%' . substr($cleanTrackWords, 0, 10) . '%']);
								$row = $stmt->fetch(PDO::FETCH_ASSOC);
								if ($row) {
									$score = (int)$row['score'];
									$added_at = formatBE($row['added_at']);
									$genre = $playlists_config[$row['playlist_id']] ?? '?';
									$resolvedtitle = $row['clean_title'];
								} else {
									if ($score === '?') {
										$rawTrack = trim(explode('(', $title)[0]);
										$rawTrack = trim(explode('-', $rawTrack)[0]);
										$baseTrack = cleanTitle('', $rawTrack);
										if (!empty($baseTrack)) {
											$stmt = $db->prepare("SELECT clean_title, score, playlist_id, added_at FROM track_mapping WHERE clean_title LIKE ? LIMIT 500");
											$stmt->execute(['%' . $baseTrack . '%']);
											$bestMatch = null;
											$highestSimilar = -1;
											$searchTerm = cleanTitle($artist, $title);
											while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
												similar_text($searchTerm, $row['clean_title'], $percent);
												if ($percent > $highestSimilar) {
													$highestSimilar = $percent;
													$bestMatch = $row;
												}
											}
											if ($bestMatch && $highestSimilar >= 90) {
												$score = (int)$bestMatch['score'];
												$added_at = formatBE($bestMatch['added_at']);
												$genre = $playlists_config[$bestMatch['playlist_id']] ?? '?';
												$resolvedtitle = $bestMatch['clean_title'];
											}
										}
									}
								}
							}
						}
					}
				}

				// 5. Fallback check track_add
				if ($resolvedtitle === 'UNKNOWN') {
					$stmt = $db->prepare("SELECT track_id FROM track_add WHERE track_id = ? LIMIT 1");
					$stmt->execute([$trackid]);
					if ($stmt->fetch(PDO::FETCH_ASSOC)) {
						$resolvedtitle = 'NEW';
					}
				}

				lg('resolvedTitle = ' . $resolvedtitle, 'mediaplayer');
				
				// Publiceer resolvedtitle wel, maar exclusief via jouw eigen controle
				publishmqtt('d/wiim/wiim_cleantitle', $resolvedtitle);
				
				if ($score !== $lastscore) {
					publishmqtt('d/wiim/wiim_score', $score);
					$lastscore = $score;
				}
				if ($added_at !== $lastadded_at) {
					publishmqtt('d/wiim/wiim_added_at', $added_at);
					$lastadded_at = $added_at;
				}
				if ($genre !== $lastgenre) {
					publishmqtt('d/wiim/wiim_genre', $genre);
					$lastgenre = $genre;
				}

				// Spotify/Boseliving logica
				$d = fetchdata();
				if ($d['auto']->s == 'On' && substr($d['boseliving']->m, 0, 7) != 'NoScore') {
					if ($score == 19) {
						if ($d['spotify']->s != 'On' && $d['lgtv']->s != 'On' && past('spotify') > 6) {
							sw('spotify', 'On', basename(__FILE__) . ':' . __LINE__, 'cron2');
						}
					} else {
						if ($d['spotify']->s != 'Off' || $d['lgtv']->s == 'On') {
							sw('spotify', 'Off', basename(__FILE__) . ':' . __LINE__, 'cron2');
						}
					}
				}
			}
		}

	} catch (Throwable $e) {
		lg("Fout in MQTT {$user}: " . __LINE__ . ' ' . $topic . ' ' . $e->getMessage(), 'mediaplayer');
	}

	if ($lastcheck < $time - $d['rand']) {
		$lastcheck = $time;
		stoploop();
		updateWekker($t, $weekend, $dow, $d);
	}
}, MqttClient::QOS_AT_LEAST_ONCE);


$mqtt->publish(
    'homeassistant/sensor/kodi/last_action/config',
    json_encode([
        'name'        => 'Kodi Last Action',
        'state_topic' => 'kodi/last_action',
        'unique_id'   => 'kodi_last_action',
    ], JSON_UNESCAPED_SLASHES),
    0,
    true
);

while (true) {
	$time = time();
    $mqtt->loopOnce($time);
    usleep(300000);
}
$mqtt->disconnect();
lg("🛑 MQTT {$user} loop stopped ".__FILE__,'mediaplayer');

function stoploop() {
    global $mqtt;
    $script = __FILE__;
    if (filemtime(__DIR__ . '/functions.php') > LOOP_START) {
        lg('🛑 functions.php gewijzigd → restarting '.basename($script).' loop...','mediaplayer');
        $mqtt->disconnect();
        exit;
    }
    if (filemtime($script) > LOOP_START) {
        lg('🛑 '.basename($script) . ' gewijzigd → restarting ...','mediaplayer');
        $mqtt->disconnect();
        exit;
    }
	static $cycles=0;
	if($cycles>=50) {
		gc_collect_cycles();
		$cycles=0;
	} else $cycles++;
}
function formatBE($dateStr, $dateonly=true, $stripyear=false) {
	if (empty($dateStr)) {
		return '';
	}
	$dt = new DateTime($dateStr);
	$monthMap = [1=>'jan',2=>'feb',3=>'mar',4=>'apr',5=>'mei',6=>'jun',7=>'jul',8=>'aug',9=>'sep',10=>'okt',11=>'nov',12=>'dec'];
	$day = $dt->format('j');
	$month = $monthMap[(int)$dt->format('n')];
	$year = $dt->format('Y');
	$time = $dt->format('G:i:s');
	
	if($dateonly===true) return ($stripyear && $year === date('Y')) ? "{$day} {$month}" : "{$day} {$month} {$year}";
	else return ($year === date('Y')) ? "{$day} {$month} {$time}" : "{$day} {$month} {$year} {$time}";
}
