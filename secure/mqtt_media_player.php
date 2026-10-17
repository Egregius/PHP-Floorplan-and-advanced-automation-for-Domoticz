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

$mqtt->subscribe('d/wiim/+', function (string $topic, string $status) use ($startloop, $validDevices, &$d, &$lastcheck, &$time, $user) {
    // Statische array om bij te houden wat we al hebben verwerkt voor de huidige track
    static $lastTrackId = '';
    static $lastArtist = '';
    static $lastTitle = '';

    try {
        // Strip de 'd/wiim/' prefix voor eenvoudig vergelijken
        $subtopic = str_replace('d/wiim/', '', $topic);

        switch ($subtopic) {
            case 'wiim_artist':
                $time = time();
                $d['wiim']['artist'] = $status;
                break;
            case 'wiim_title':
                $d['wiim']['title'] = $status;
                break;
            case 'wiim_album':
                $d['wiim']['album'] = $status;
                break;
            case 'wiim_album_art':
                $d['wiim']['album_art'] = $status;
                break;
            case 'wiim_transportstate':
                $d['wiim']['transportstate'] = $status;
                break;
            case 'wiim_trackduration':
                $d['wiim']['trackduration'] = $status;
                break;
            case 'wiim_reltime':
                $d['wiim']['reltime'] = $status;
                break;
            case 'wiim_currenttrackuri':
                $d['wiim']['trackid'] = str_replace('spotify:track:', '', $status);
                break;
        }

        // We triggeren de database lookups ALLEEN als we minstens artist, title én trackid hebben,
        // en de track daadwerkelijk is veranderd ten opzichte van de vorige keer.
        $currentTrackId = $d['wiim']['trackid'] ?? '';
        $currentArtist  = $d['wiim']['artist'] ?? '';
        $currentTitle   = $d['wiim']['title'] ?? '';

        if (!empty($currentTrackId) && !empty($currentArtist) && !empty($currentTitle)) {
            
            // Check of er daadwerkelijk een nieuw nummer is gestart
            if ($currentTrackId !== $lastTrackId /*|| $currentArtist !== $lastArtist || $currentTitle !== $lastTitle*/) {
                $d=fetchdata();
                // Onthoud deze meteen zodat we niet in een loop belanden
                $lastTrackId = $currentTrackId;
                $lastArtist  = $currentArtist;
                $lastTitle   = $currentTitle;

                $d['wiim']['cleantitle'] = '';
                $d['wiim']['score'] = '?';
                $d['wiim']['genre'] = '?';
                $d['wiim']['added_at'] = '?';
                $resolvedTitle = 'UNKNOWN';
                
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

                // 1. Probeer eerst te matchen op track_id
				$stmt = $db->prepare("SELECT clean_title, score, playlist_id, added_at FROM track_mapping WHERE track_id = ? LIMIT 1");
				$stmt->execute([$currentTrackId]);
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				if ($row) {
					$d['wiim']['score'] = (int)$row['score'];
					$d['wiim']['added_at'] = formatBE($row['added_at']);
					$d['wiim']['genre'] = $playlists_config[$row['playlist_id']];
					$resolvedTitle = $row['clean_title'];
				}
			
				// 2. Als track_id niets opleverde, probeer via clean_title exacte match
				if ($d['wiim']['score'] === '?' && empty($resolvedTitle)) {
					$stmt = $db->prepare("SELECT clean_title, score, playlist_id, added_at FROM track_mapping WHERE clean_title = ? LIMIT 1");
					$stmt->execute([$d['wiim']['cleantitle']]);
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					if ($row) {
						$d['wiim']['score'] = (int)$row['score'];
						$d['wiim']['added_at'] = formatBE($row['added_at']);
						$d['wiim']['genre'] = $playlists_config[$row['playlist_id']];
						$resolvedTitle = $row['clean_title'];
					} else {
						// 3. Fallback: standaard LIKE met artiest en track
						$stmt = $db->prepare("SELECT clean_title, score, playlist_id, added_at FROM track_mapping WHERE clean_title LIKE ? LIMIT 1");
						$stmt->execute(['%' . cleanTitle($d['wiim']['artist'], '') . '%' . cleanTitle('', $d['wiim']['title']) . '%']);
						$row = $stmt->fetch(PDO::FETCH_ASSOC);
						if ($row) {
							$d['wiim']['score'] = (int)$row['score'];
							$d['wiim']['added_at'] = formatBE($row['added_at']);
							$d['wiim']['genre'] = $playlists_config[$row['playlist_id']];
							$resolvedTitle = $row['clean_title'];
						} else {
							// 4. Extra fallback: split op hoofdwoorden uit de track (bijv. artiest % deel1 % deel2)
							$cleanArtist = cleanTitle($d['wiim']['artist'], '');
							// Pak bijvoorbeeld de eerste 2 à 3 woorden uit de track na cleaning
							$cleanTrackWords = cleanTitle('', $d['wiim']['title']);
							if (!empty($cleanArtist) && !empty($cleanTrackWords)) {
								$stmt = $db->prepare("SELECT clean_title, score, playlist_id, added_at FROM track_mapping WHERE clean_title LIKE ? AND clean_title LIKE ? LIMIT 1");
								$stmt->execute([$cleanArtist . '%', '%' . substr($cleanTrackWords, 0, 10) . '%']);
								$row = $stmt->fetch(PDO::FETCH_ASSOC);
								if ($row) {
									$d['wiim']['score'] = (int)$row['score'];
									$d['wiim']['added_at'] = formatBE($row['added_at']);
									$d['wiim']['genre'] = $playlists_config[$row['playlist_id']];
									$resolvedTitle = $row['clean_title'];
								} else {
									if ($d['score'] === '?') {
										$rawTrack = trim(explode('(', $d['wiim']['title'])[0]);
										$rawTrack = trim(explode('-', $rawTrack)[0]);
										$baseTrack = cleanTitle('', $rawTrack);
										if (!empty($baseTrack)) {
											$stmt = $db->prepare("SELECT clean_title, score, playlist_id, added_at FROM track_mapping WHERE clean_title LIKE ? LIMIT 500");
											$stmt->execute(['%' . $baseTrack . '%']);
											$bestMatch = null;
											$highestSimilar = -1;
											$searchTerm = cleanTitle($d['wiim']['artist'], $d['wiim']['title']);
											while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
												similar_text($searchTerm, $row['clean_title'], $percent);
												if ($percent > $highestSimilar) {
													$highestSimilar = $percent;
													$bestMatch = $row;
												}
											}
											if ($bestMatch && $highestSimilar >= 90) {
												$d['wiim']['score'] = (int)$bestMatch['score'];
												$d['wiim']['added_at'] = formatBE($bestMatch['added_at']);
												$d['wiim']['genre'] = $playlists_config[$bestMatch['playlist_id']];
												$resolvedTitle = $bestMatch['clean_title'];
											}
										}
									}
								}
							}
						}
					}
				}
				lg('resolvedTitle = '.$resolvedTitle, 'mediaplayer');
                // Publiceer de verrijkte data door
                publishmqtt('d/wiim/wiim_cleantitle', $resolvedTitle);
                publishmqtt('d/wiim/wiim_score', $d['wiim']['score']);
                publishmqtt('d/wiim/wiim_added_at', $d['wiim']['added_at']);
                publishmqtt('d/wiim/wiim_genre', $d['wiim']['genre']);
                publishmqtt('d/wiim/wiim_trackid', $d['wiim']['trackid']);
                if($d['auto']->s=='On'&&substr($d['boseliving']->m,0,7)!='NoScore') {
					if ($d['wiim']['score']==19) {
						if($d['spotify']->s!='On'&&$d['lgtv']->s!='On'&&past('spotify')>6) sw('spotify','On',basename(__FILE__).':'.__LINE__,'cron2');
					} else {
						if($d['spotify']->s!='Off'||$d['lgtv']->s=='On') sw('spotify','Off',basename(__FILE__).':'.__LINE__,'cron2');
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
    usleep(500000);
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
