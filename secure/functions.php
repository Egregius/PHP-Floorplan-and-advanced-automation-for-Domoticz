<?php
// Using https://github.com/php-mqtt/client
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
require_once '/var/www/vendor/autoload.php';
require '/var/www/config.php';
if (!is_dir('/dev/shm/cache')) {
    mkdir('/dev/shm/cache', 0777, true);
    chmod('/dev/shm/cache', 0777);
}
define('VERSIE', 5);
$dow=date("w");
if($dow==0||$dow==6)$weekend=true; else $weekend=false;
date_default_timezone_set('Europe/Brussels');

function updateWekker(&$t, &$weekend, &$dow, &$d) {
	$d['time'] = $d['time'] ?? time();
    $dow = ($d['dag']['m'] > 180) ? date("w", $d['time'] + 43200) : date("w");
    if ($d['verlof']['s']==2) $weekend=true;
    else $weekend = ($dow == 0 || $dow == 6);
    $t = ($weekend||$d['verlof']['s']==2) ? strtotime('7:45') : strtotime('7:00');
//		$t=strtotime('6:20');
}
function check_en_slapen($locatie, $status, &$d) {
	$x = 0;
	if ($locatie === 'voordeur') {
		if ($d['deurvoordeur']['s'] !== 'Open' || $status !== 'On') return;
	} elseif ($locatie === 'poort') {
		if ($status !== 'On') return;
		if ($d['poort']['s'] !== 'On') {
			sw('poort', 'On', basename(__FILE__).':'.__LINE__);
			return;
		}
	} elseif ($locatie === 'slaapkamer') {
		if ($status !== 'On') return;
		if ($d['weg']['s'] != 0) return;
		if (!($d['time'] > strtotime('21:00') || $d['time'] < strtotime('4:00'))) return;
	}
	$ramen_deuren = [
		'achterdeur'	=> 'Achterdeur open',
		'raamliving'	=> 'Raam Living open',
		'raamkeuken'	=> 'Raam keuken open',
	];
	if ($locatie !== 'voordeur') {
		$ramen_deuren['deurvoordeur'] = 'Voordeur open';
	}
	foreach ($ramen_deuren as $k => $msg) {
		if ($d[$k]['s'] != 'Closed') {
			waarschuwing($msg, 55);
			$x++;
		}
	}
	$boses = [
		102 => '102',
		103 => 'Boven',
		104 => 'Garage',
		105 => '10-Wit',
		106 => 'Buiten20',
	];
	foreach ($boses as $k => $v) {
		if ($d['bose'.$k]['m'] === 1) {
			waarschuwing('Bose '.$v, 55);
			$x++;
		}
	}
	if ($x > 0) return;
	if ($locatie === 'voordeur' || $locatie === 'poort') {
		huisslapen(true);
		sl('zoldertrap', 0, basename(__FILE__).':'.__LINE__, true);
	} elseif ($locatie === 'slaapkamer') {
		if ($d['kamer']['s'] > 5) {
			sl('kamer', 5, basename(__FILE__).':'.__LINE__);
		} elseif (($d['dag']['s']<-4||$d['rkamerr']['s']>90)&&past('kamer')>7200&&$d['time'] > strtotime('21:00')&&$d['time'] < strtotime('23:00')) {
			sl('kamer', 1, basename(__FILE__).':'.__LINE__);
		}
		huisslapen();
	}
}

function fliving() {
	global $d,$t;
	if ($d['auto']['s']=='On'&&$d['weg']['s']==0&&$d['media']['s']=='Off'&&$d['bureellinks']['s']==0&&$d['lampkast']['s']!='On'&&$d['eettafel']['s']==0&&$d['zithoek']['s']==0) {
		if (($d['z']==0&&$d['dag']['s']<0)||($d['rkeukenl']['s']>80&&$d['rkeukenr']['s']>80&&$d['rbureel']['s']>80&&$d['rliving']['s']>80)) {
			$am=strtotime('10:00');
			if ($d['wasbak']['s']<10&&$d['time']<$am) sl('wasbak', 10, basename(__FILE__).':'.__LINE__);
			if ($d['zithoek']['s']<14) sl('zithoek', 14, basename(__FILE__).':'.__LINE__);
			if ($d['eettafel']['s']<14) sl('eettafel', 14, basename(__FILE__).':'.__LINE__);
			if ($d['bureellinks']['s']<14) sl('bureellinks', 14, basename(__FILE__).':'.__LINE__);
			if ($d['bureelrechts']['s']<14) sl('bureelrechts', 14, basename(__FILE__).':'.__LINE__);
		}
	}
}
function fgarage() {
	global $d;
	if ($d['auto']['s']=='On'&&$d['weg']['s']==0&&$d['garage']['s']!='On'&&$d['garageled']['s']!='On') {
		if ($d['z']<260) {
			zwave('poort','binary',2,'ON');
			sw('garageled', 'On', basename(__FILE__).':'.__LINE__);
		}
		if ($d['garageled']['m']!=1) {
			storemode('garageled',1);
			setBatterijLedBrightness(40);
		}
	}
}
function fkeuken() {
	global $d;
	if (1==2) {
		if ($d['wasbak']['s']<12) sl('wasbak', 12, basename(__FILE__).':'.__LINE__);
		if ($d['snijplank']['s']<12) sl('snijplank', 12, basename(__FILE__).':'.__LINE__);
	} else {
		if ($d['auto']['s']=='On'&&$d['weg']['s']==0&&$d['wasbak']['s']<10&&$d['snijplank']['s']==0&&($d['dag']['s']<1||$d['rkeukenl']['s']>80)) {
			if ($d['time']>strtotime('7:00')&&$d['time']<strtotime('20:00')) {
				zwave('wasbak','multilevel',0,10);
				sl('wasbak', 10, basename(__FILE__).':'.__LINE__);
			} else {
				zwave('wasbak','multilevel',0,6);
				sl('wasbak', 6, basename(__FILE__).':'.__LINE__);
			}
		}
	}
//	hass('input_button','press','input_button.wakeipad');
}
function finkom($force=false) {
	global $d;
	if (($d['auto']['s']=='On'&&$d['weg']['s']==0&&$d['dag']['s']<-1.4)||$force==true) {
		if ($d['inkom']['s']<30&&$d['dag']['s']<-1.4) {
			zwave('inkom','multilevel',0,30);
			sl('inkom', 30, basename(__FILE__).':'.__LINE__);
		}
		if ($d['hall']['s']<30&&$d['deuralex']['s']=='Open'&&$d['deurkamer']['s']=='Open'&&$d['time']>=strtotime('19:45')&&$d['time']<=strtotime('21:30')&&$d['alexslaapt']['s']==0) {
			zwave('hall','multilevel',0,30);
			sl('hall', 30, basename(__FILE__).':'.__LINE__);
		}
	}
}
function fhall() {
	global $d,$t;
	if ($d['auto']['s']=='On'&&$d['weg']['s']==0&&$d['dag']['s']<-2&&$d['alexslaapt']['s']==0) {
		if ($d['hall']['s']<30&&$d['weg']['s']==0) {
			zwave('hall','multilevel',0,30);
			sl('hall', 30, basename(__FILE__).':'.__LINE__);
		}
	} else finkom();
	if ($d['weg']['s']==0&&$d['rkamerl']['s']>70&&$d['rkamerr']['s']>70&&$d['time']>=strtotime('21:30')&&$d['time']<=strtotime('23:00')&&$d['kamer']['s']==0&&past('kamer')>7200) sl('kamer', 1, basename(__FILE__).':'.__LINE__);
}
function fbadkamer($level,$power=false) {
	global $d,$t;
	if ($level==0) {
		if ($d['badkamerpower']['s']=='On') sw('badkamerpower', 'Off', basename(__FILE__).':'.__LINE__);
	} else {
		if ($power===true&&$d['badkamerpower']['s']=='Off') {
			sw('badkamerpower', 'On', basename(__FILE__).':'.__LINE__);
			usleep(500000);
		}
		sl('lichtbadkamer', $level);
//		store('deurbadkamer', $d['deurbadkamer']['s'], basename(__FILE__).':'.__LINE__);
		if ($d['weg']['s']==1&&$d['time']>$t-7200) {
			if ($power===true&&$d['time']<$t+3600&&$d['boseliving']['s']=='Off') sw('boseliving', 'On', basename(__FILE__).':'.__LINE__);
			if ($d['time']<$t&&$d['living_set']['m']==0) storemode('living_set', 2, basename(__FILE__) . ':' . __LINE__);
		}
	}
}

function huisslapen($weg=false) {
	global $d;
	if ($weg===3) {
		store('weg', 3, basename(__FILE__).':'.__LINE__);
		if ($d['badkamerpower']['s']=='On') sw('badkamerpower', 'Off', basename(__FILE__).':'.__LINE__);
	} elseif ($weg===true) {
		store('weg', 2, basename(__FILE__).':'.__LINE__);
		if ($d['badkamerpower']['s']=='On') sw('badkamerpower', 'Off', basename(__FILE__).':'.__LINE__);
	} else {
		store('weg', 1, basename(__FILE__).':'.__LINE__);
	}
	sl(['hall','inkom','eettafel','zithoek','bureellinks','bureelrechts','wasbak','snijplank','terras'], 0, basename(__FILE__).':'.__LINE__);
	sw(['lampkast','garageled','garage','pirgarage','pirkeuken','pirliving','pirinkom','pirhall','tuin','zolderg','wc','grohered','kookplaat','steenterras','tuintafel','bosekeuken','boseliving','mac','ipaddock','zetel'], 'Off', basename(__FILE__).':'.__LINE__);
	foreach (['living_set','alex_set','kamer_set','badkamer_set'/*,'eettafel','zithoek'*/,'luifel'] as $i) {
		if ($d[$i]['m']!=0&&$d[$i]['s']!='D'&&past($i)>180) storemode($i, 0, basename(__FILE__).':'.__LINE__);
	}
	hass('script', 'turn_on', 'script.alles_uitschakelen');
}

function huisthuis($msg='') {
	store('weg', 0);
	if (strlen($msg)>0) lg($msg);
	else lg('Huis thuis');
}
function boseplayinfo($sound, $vol=50, $log='', $ip=101) {
	$raw=rawurlencode($sound);
	if(file_exists('/var/www/html/sounds/'.$sound.'.mp3')) {
		$volume=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.101:8090/volume'))), true);
		$vol=$volume['actualvolume'];
		$xml="<play_info><app_key>UJvfKvnMPgzK6oc7tTE1QpAVcOqp4BAY</app_key><url>http://192.168.2.2/sounds/$raw.mp3</url><service>$sound</service><reason>$sound</reason><message>$sound</message><volume>$vol</volume></play_info>";
		bosepost('speaker', $xml);
		bosevolume($volume['actualvolume'], 101, basename(__FILE__).':'.__LINE__);
	}
}
function alert($name,$msg,$ttl,$silent=true,$to=1) {
	global $d;
	$last=0;
	$db = Database::getInstance();
	$stmt=$db->query("SELECT t FROM alerts WHERE n='$name';");
	while ($row=$stmt->fetch(PDO::FETCH_NUM)) {
		if (isset($row[0])) $last=$row[0];
	}
	if ($last < $d['time']-$ttl) {
		$time=$d['time'];
		$db->query("INSERT INTO alerts (n,t) VALUES ('$name','$time') ON DUPLICATE KEY UPDATE t='$time';");
		if ($to==1) {
			if ($silent==true) telegram($msg, $silent, 1);
			else hassnotify('Alert!', $msg, 'mobile_app_iphone_guy', false);
		} else {
			if ($silent==true) {
				telegram($msg, $silent, 3);
			} else {
				hassnotify('Alert!', $msg, 'mobile_app_iphone_guy', false);
				telegram($msg, $silent, 2);
			}
		}
	}
}
function waarschuwing($msg) {
	hassnotify('Waarschuwing!', $msg, 'mobile_app_iphone_guy', true);
	telegram($msg, false, 2);
	sw('sirene', 'On');
	sleep(2);
	sw('sirene', 'Off','',true);
}
function past($name,$lg='') {
	global $d;
	$d['time']=time();
	return $d['time']-$d[$name]['t'];
}
function sl(string|array $name, int $level, ?string $msg = null): void {
    global $d, $user, $mqtt;
    if (is_array($name)) {
        foreach ($name as $i) {
            if ($d[$i]['s'] !== $level) {
                sl($i, $level, $msg);
                $d['time'] = time();
            }
        }
        return;
    }
    $d ??= fetchdata();
//    lg('💡 SL ' . str_pad($user, 9) . ' => ' . str_pad($name, 13) . ' => ' . $level . ($msg ? " ($msg)" : ''), 4);
    $device = $d[$name]['d'] ?? null;
    $entityPrefix = in_array($device, ['r', 'luifel']) ? 'cover' : 'light';
    $entity = "$entityPrefix.$name";
    match($device) {
        'hd' => $level > 0 
            ? hass('light', 'turn_on', $entity, ['brightness_pct' => $level])
            : hass('light', 'turn_off', $entity),
        
        'd' => $level > 0
            ? hass('light', 'turn_on', $entity, ['brightness_pct' => $level])
            : hass('light', 'turn_off', $entity),
        
        'r' => hass('cover', 'set_cover_position', $entity, [
            'position' => $name === 'rbureel' ? 100 - $level : $level
        ]),
        'luifel' => hass('cover', 'set_cover_position', $entity, ['position' => $level]),
        default => null
    };
    if($d[$name]['s']==$level) return;
    $d[$name]['s']=$level;
    $d[$name]['t']=$d['time'];
}
function resetsecurity() {
	global $d;
	if ($d['sirene']['s']!='Off') {
		sw('sirene', 'Off', basename(__FILE__).':'.__LINE__,true);
	}
}
function sw($name,$action='Toggle',$msg=null) {
	global $d,$user,$db,$mqtt;
	if (is_array($name)) {
		foreach ($name as $i) {
			if ($d[$i]['s']!=$action) {
				sw($i, $action, $msg);
				usleep(200000);
				$now = time();
				if ($d['time'] !== $now) {
					$d['time'] = $now;
				}
			}
		}
	} else {
		if(!isset($d)) $d=fetchdata();
		$msg=str_pad($user, 9, ' ', STR_PAD_LEFT).' => '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' => '.$action.($msg?' ('.$msg.')':'');
		if (isset($d[$name]['d'])&&$d[$name]['d']=='s') {
			if ($action=='Toggle') {
				if ($d[$name]['s']=='On') $action='Off';
				else $action='On';
			}
//			lg('💡 SW '.$msg,4);
			if ($action=='On') hass('switch','turn_on','switch.'.$name);
			elseif ($action=='Off') hass('switch','turn_off','switch.'.$name);
		} else {
			store($name, $action, $msg);
		}
		if($d[$name]['s']==$action) return;
		$d[$name]['t']=$d['time'];
		$d[$name]['s']=$action;
	}
}
function zigbee($device,$action) {
	global $mqtt;
//	lg(" ☠️ zigbee2mqtt/{$device}/set",$action);
	$mqtt->publish("zigbee2mqtt/{$device}/set",$action);
}
function zwave($device,$type,$endpoint,$action) {
	global $mqtt;
//	lg(" ☠️ zwave2mqtt/{$device}/switch_{$type}/endpoint_{$endpoint}/targetValue/set/".$action);
	$mqtt->publish("zwave2mqtt/{$device}/switch_{$type}/endpoint_{$endpoint}/targetValue/set",$action);
}
function setpoint($name, $value,$msg='') {
	global $d,$user,$db;
	$msg='(SETPOINT)'.str_pad($user, 9, ' ', STR_PAD_LEFT).' => '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' => '.$value.' ('.$msg.')';
	store($name, $value, $msg);
}
function store($name='',$status='',$msg='') {
	global $d,$user;
	if (is_numeric($status)) {
        $status = $status + 0;
    }
	for ($attempt = 0; $attempt <= 4; $attempt++) {
		try {
			$d['time']??=time();
			$d[$name]['s']=$status;
			$d[$name]['t']=$d['time'];
			$db=Database::getInstance();
			$stmt=$db->prepare("UPDATE devices SET s = :s, t = :t WHERE n = :n");
			$stmt->execute([':s'=>$status,':t'=>$d['time'],':n'=>$name]);
			$affected=$stmt->rowCount();
			break;
		} catch (PDOException $e) {
			if (in_array($e->getCode(),[2006,'HY000']) && $attempt < 4) {
				lg('♻ DB gone away → reconnect & retry', 5);
				Database::reset();
				if($attempt>0) sleep($attempt);
				continue;
			}
			throw $e;
		}
	}
	if($affected>0/*&&!in_array($name,['dag'])*/){
		if(isset($d[$name]['f'])) {
			$x=$d[$name];
			unset($x['f']);
			if(!isset($x['rt'])) unset($x['t'],$x['rt']);
			else unset($x['rt']);
			publishmqtt('d/'.$name,json_encode($x),$msg);
		}
		lg('💾 STORE     '.str_pad($user??'',9).' '.str_pad($name??'',13).' '.$status.($msg?' ('.$msg.')':''),10);
	}
	return $affected ?? 0;
}
function isCli(): bool {
    return PHP_SAPI === 'cli';
}

function publishmqtt($topic,$msg,$log='') {
	global $mqtt,$user;
	if($mqtt&&$mqtt->isConnected()) {
		lgmqtt("🟢 {$user}	{$topic}	{$msg}	{$log}");
		$mqtt->publish($topic,$msg,1,true);
	} else {
		$connectionSettings=(new ConnectionSettings)
		->setUsername('mqtt')
		->setPassword('mqtt');
		$mqtt=new MqttClient('192.168.2.22',1883,basename(__FILE__) . '_' . getmypid(),MqttClient::MQTT_3_1);
		$mqtt->connect($connectionSettings,true);
		lgmqtt("🛑 {$user}	{$topic}	{$msg}	{$log}");
		$mqtt->publish($topic,$msg,1,true);
		if (PHP_SAPI !== 'cli') $mqtt->disconnect();
	}
}
function storemode($name,$mode,$msg='') {
	global $d,$user;
	if (is_numeric($mode)) {
        $mode = $mode + 0;
    }
    if((string)$d[$name]['m']==(string)$mode) return;
    for ($attempt = 0; $attempt <= 4; $attempt++) {
		try {
			$d['time']??=time();
			$d[$name]['m']=$mode;
			$d[$name]['t']=$d['time'];
			$db=Database::getInstance();
			$stmt=$db->prepare("UPDATE devices SET m = :m, t = :t WHERE n = :n");
			$stmt->execute([':m'=>$mode,':t'=>$d['time'],':n'=>$name]);
			$affected=$stmt->rowCount();
			break;
		} catch (PDOException $e) {
			if (in_array($e->getCode(),[2006,'HY000']) && $attempt < 4) {
				lg('♻ DB gone away → reconnect & retry', 5);
				Database::reset();
				if($attempt>0) sleep($attempt);
				continue;
			}
			throw $e;
		}
	}	
	if($affected>0&&!in_array($name,['dag'])) {
		if(isset($d[$name]['f'])) {
			$x=$d[$name];
			unset($x['f']);
			if(!isset($x['rt'])) unset($x['t'],$x['rt']);
			else unset($x['rt']);
			publishmqtt('d/'.$name,json_encode($x),$msg);
		}
		lg('💾 STOREM	'.str_pad($user??'', 9, ' ', STR_PAD_RIGHT).' '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' '.$mode.(strlen($msg>0)?'	('.$msg.')':''),10);
	}
	return $affected ?? 0;
}
function storesm($name,$s,$m,$msg='') {
	global $d,$user;
	if (is_numeric($s)) {
        $s = $s + 0;
    }
    if (is_numeric($m)) {
        $m = $m + 0;
    }
    if((string)$d[$name]['s']==(string)$s&&(string)$d[$name]['m']==(string)$m) return;
    for ($attempt = 0; $attempt <= 4; $attempt++) {
		try {
			$d['time']??=time();
			$d[$name]['s']=$s;
			$d[$name]['m']=$m;
			$d[$name]['t']=$d['time'];
			$db=Database::getInstance();
			$stmt=$db->prepare("UPDATE devices SET s = :s, m = :m, t = :t WHERE n = :n");
			$stmt->execute([':s'=>$s,':m'=>$m,':t'=>$d['time'],':n'=>$name]);
			$affected=$stmt->rowCount();
			break;
		} catch (PDOException $e) {
			if (in_array($e->getCode(),[2006,'HY000']) && $attempt < 4) {
				lg('♻ DB gone away → reconnect & retry', 5);
				Database::reset();
				if($attempt>0) sleep($attempt);
				continue;
			}
			throw $e;
		}
	}	
	if($affected>0) {
		if(isset($d[$name]['f'])) {
			$x=$d[$name];
			unset($x['f']);
			if(!isset($x['rt'])) unset($x['t'],$x['rt']);
			else unset($x['rt']);
			publishmqtt('d/'.$name,json_encode($x),$msg);
		}
		lg('💾 STORESM   '.str_pad($user??'', 9, ' ', STR_PAD_RIGHT).' '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' S='.$s.' M='.$m.(strlen($msg>0)?'	('.$msg.')':''),10);
	}
	return $affected ?? 0;
}
function storesmi($name,$s,$m,$i,$msg='') {
	global $d,$user;
	if (is_numeric($s)) {
        $s = $s + 0;
    }
    if (is_numeric($m)) {
        $m = $m + 0;
    }
    if (is_numeric($i)) {
        $i = $i + 0;
    }
    if((string)$d[$name]['s']==(string)$s&&(string)$d[$name]['m']==(string)$m&&(string)$d[$name]['i']==(string)$i) return;
    for ($attempt = 0; $attempt <= 4; $attempt++) {
		try {
			$d['time']??=time();
			$d[$name]['s']=$s;
			$d[$name]['m']=$m;
			$d[$name]['i']=$i;
			$d[$name]['t']=$d['time'];
			$db=Database::getInstance();
			$stmt=$db->prepare("UPDATE devices SET s = :s, m = :m, i = :i, t = :t WHERE n = :n");
			$stmt->execute([':s'=>$s,':m'=>$m,':i'=>$i,':t'=>$d['time'],':n'=>$name]);
			$affected=$stmt->rowCount();
			break;
		} catch (PDOException $e) {
			if (in_array($e->getCode(),[2006,'HY000']) && $attempt < 4) {
				lg('♻ DB gone away → reconnect & retry', 5);
				Database::reset();
				if($attempt>0) sleep($attempt);
				continue;
			}
			throw $e;
		}
	}	
	if($affected>0) {
		if(isset($d[$name]['f'])) {
			$x=$d[$name];
			unset($x['f']);
			if(!isset($x['rt'])) unset($x['t'],$x['rt']);
			else unset($x['rt']);
			publishmqtt('d/'.$name,json_encode($x),$msg);
		}
		lg('💾 STORESMI   '.str_pad($user??'', 9, ' ', STR_PAD_RIGHT).' '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' S='.$s.' M='.$m.' I='.$i.(strlen($msg>0)?'	('.$msg.')':''),10);
	}
	return $affected ?? 0;
}
function storesp($name,$s,$p,$msg='') {
	global $d,$user;
	if (is_numeric($s)) {
        $s = $s + 0;
    }
    if (is_numeric($p)) {
        $p = $p + 0;
    }
    if((string)$d[$name]['s']==(string)$s&&(string)$d[$name]['p']==(string)$p) return;
    for ($attempt = 0; $attempt <= 4; $attempt++) {
		try {
			$d['time']??=time();
			$d[$name]['s']=$s;
			$d[$name]['p']=$p;
			$d[$name]['t']=$d['time'];
			$db=Database::getInstance();
			$stmt=$db->prepare("UPDATE devices SET s = :s, p = :p WHERE n = :n");
			$stmt->execute([':s'=>$s,':p'=>$p,':t'=>$d['time'],':n'=>$name]);
			$affected=$stmt->rowCount();
			break;
		} catch (PDOException $e) {
			if (in_array($e->getCode(),[2006,'HY000']) && $attempt < 4) {
				lg('♻ DB gone away → reconnect & retry', 5);
				Database::reset();
				if($attempt>0) sleep($attempt);
				continue;
			}
			throw $e;
		}
	}	
	if($affected>0) {
		if(isset($d[$name]['f'])) {
			$x=$d[$name];
			unset($x['f']);
			if(!isset($x['rt'])) unset($x['t'],$x['rt']);
			else unset($x['rt']);
			publishmqtt('d/'.$name,json_encode($x),$msg);
		}
		lg('💾 STORESP   '.str_pad($user??'', 9, ' ', STR_PAD_RIGHT).' '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' S='.$s.' P='.$p.(strlen($msg>0)?'	('.$msg.')':''),10);
	}
	return $affected ?? 0;
}
function storep($name,$p,$msg='') {
	global $d,$user;
	if (is_numeric($p)) {
        $p = $p + 0;
    }
    if((string)$d[$name]['p']==(string)$p) return;
    for ($attempt = 0; $attempt <= 4; $attempt++) {
		try {
			$d['time']??=time();
			if ($p>20) $d[$name]['s']=='On';
			$d[$name]['p']=$p;
			$d[$name]['t']=$d['time'];
			$db=Database::getInstance();
			$stmt=$db->prepare("UPDATE devices SET p = :p, t = :t WHERE n = :n");
			$stmt->execute([':p'=>$p,':t'=>$d['time'],':n'=>$name]);
			$affected=$stmt->rowCount();
			break;
		} catch (PDOException $e) {
			if (in_array($e->getCode(),[2006,'HY000']) && $attempt < 4) {
				lg('♻ DB gone away → reconnect & retry', 5);
				Database::reset();
				if($attempt>0) sleep($attempt);
				continue;
			}
			throw $e;
		}
	}	
	if($affected>0) {
		if(isset($d[$name]['f'])) {
			$x=$d[$name];
			unset($x['f']);
			if(!isset($x['rt'])) unset($x['t'],$x['rt']);
			else unset($x['rt']);
			publishmqtt('d/'.$name,json_encode($x),$msg);
		}
		lg('💾 STOREP	'.str_pad($user??'', 9, ' ', STR_PAD_RIGHT).' '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' '.$p.(strlen($msg>0)?'	('.$msg.')':''),10);
	}
	return $affected ?? 0;
}
function storeicon($name,$i,$msg='') {
	global $d,$user;
	if (is_numeric($i)) {
        $i = $i + 0;
    }
    if($d[$name]['i']==$i) return;
    for ($attempt = 0; $attempt <= 4; $attempt++) {
		try {
			$d['time']??=time();
			$d[$name]['i']=$i;
			$d[$name]['t']=$d['time'];
			$db=Database::getInstance();
			$stmt=$db->prepare("UPDATE devices SET i = :i, t = :t WHERE n = :n");
			$stmt->execute([':i'=>$i,':t'=>$d['time'],':n'=>$name]);
			$affected=$stmt->rowCount();
			break;
		} catch (PDOException $e) {
			if (in_array($e->getCode(),[2006,'HY000']) && $attempt < 4) {
				lg('♻ DB gone away → reconnect & retry', 5);
				Database::reset();
				if($attempt>0) sleep($attempt);
				continue;
			}
			throw $e;
		}
	}	
	if (str_ends_with($name, '_temp')) return;
	if($affected>0) {
		if(isset($d[$name]['f'])) {
			$x=$d[$name];
			unset($x['f']);
			if(!isset($x['rt'])) unset($x['t'],$x['rt']);
			else unset($x['rt']);
			publishmqtt('d/'.$name,json_encode($x),$msg);
		}
		lg('💾 STOREIC	'.str_pad($user??'', 9, ' ', STR_PAD_RIGHT).' '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' '.$i.(strlen($msg>0)?'	('.$msg.')':''),10);
	}
	return $affected ?? 0;
}

function kodi($json) {
	global $kodiurl;
	$ch=curl_init($kodiurl.'/jsonrpc');
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	$result=curl_exec($ch);
	curl_close($ch);
	return $result;
}
function convertToHours($time) {
	if ($time<600) return substr(date('i:s', $time-3600), 1);
	elseif ($time>=600&&$time<3600) return date('i:s', $time-3600);
	else return date('G:i:s', $time-3600);
}
function ping($ip) {
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        return false;
    }
    $result = null;
    exec("ping -c 1 -w 1 " . escapeshellarg($ip), $output, $result);
    return $result === 0;
}
function double($name, $action, $msg='') {
	sw($name, $action, $msg);
	usleep(2000000);
	sw($name, $action, $msg);
}

function rookmelder($msg) {
	global $d,$device;
	resetsecurity();
	alert($device,	$msg,	300, false, 2, true);
//	if ($d['weg']['s']<=1) {

//		foreach (array(/*'ralex',*/'rkamerl','rkeukenl','rkamerr','rwaskamer','rliving','rkeukenr','rbureel') as $i) {
//			if ($d[$i]['s']>0) sl($i, 1, basename(__FILE__).':'.__LINE__);
//		}
//		if ($d['z']<200) {
//			foreach (array('hall','inkom','kamer','waskamer',/*'alex',*/'eettafel','zithoek','lichtbadkamer','wasbak','terras') as $i) {
//				if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
//			}
//			foreach (array('snijplank','garage','lampkast','bureel', 'tuin') as $i) {
//				if ($d[$i]['s']!='On') sw($i, 'On', basename(__FILE__).':'.__LINE__);
//			}
//		}
//	}
}
function koekje($user,$expirytime) {
	global $cookie,$domainname;
	setcookie($cookie, $user, $expirytime, '/', $domainname, true, true);
}
function telegram($msg,$silent=true,$to=1) {
	if ($silent==true) $silent='true';
	else $silent='false';
	shell_exec('/var/www/html/secure/telegram.sh "'.$msg.'" "'.$silent.'" "'.$to.'" > /dev/null 2>/dev/null &');
	lg('✉️  Telegram sent: '.$msg);
}
function lg($msg,$level=0) {
/*
Levels:
0:	Default / Undefined
1:	Loop starts
2:	
3:	
4:	Switch commands
5:	Setpoints
6:	OwnTracks
7:	
8:	Update kWh devices
9:	Update temperatures
10: Store/Storemode
99:	SQL Fetchdata
*/
	global $d;
	if (isset($d['auto']['m'])) {
		$loglevel = $d['auto']['m'];
	} else $loglevel = 0;

	if ($level <= $loglevel) {
		$fp = fopen('/temp/domoticz.log', "a+");
		$time = microtime(true);
		$dFormat = "d-m H:i:s";
		$mSecs = $time - floor($time);
		$mSecs = substr(number_format($mSecs, 3), 1);
		fwrite($fp, sprintf("%s%s %s\n", date($dFormat), $mSecs, $msg));
		fclose($fp);
	}
}
function lgmqtt($msg) {
	$fp = fopen('/temp/mqttpublish.log', "a+");
	$time = microtime(true);
	$dFormat = "d-m H:i:s";
	$mSecs = $time - floor($time);
	$mSecs = substr(number_format($mSecs, 3), 1);
	fwrite($fp, sprintf("%s%s %s\n", date($dFormat), $mSecs, $msg));
	fclose($fp);
}
function bosekey($key,$sleep=75000,$ip=101,$msg=null) {
	lg('bosekey '.$ip.' '.$key.' '.$msg);
	$xml="<key state=\"press\" sender=\"Gabbo\">$key</key>";
	bosepost("key", $xml, $ip, true);
	$xml="<key state=\"release\" sender=\"Gabbo\">$key</key>";
	bosepost("key", $xml, $ip);
	if ($sleep>0) usleep($sleep);
}
function bosevolume($vol,$ip=101, $msg='') {
	$vol=1*$vol;
	$xml="<volume>$vol</volume>";
	bosepost("volume", $xml, $ip, true);
	if ($ip==101) {
		if ($vol>=50) bosebass(-5, $ip);
		elseif ($vol>=40) bosebass(-6, $ip);
		elseif ($vol>=30) bosebass(-7, $ip);
		elseif ($vol>=20) bosebass(-8, $ip);
		else bosebass(-9, $ip);
	}
	lg('🔊 bosevolume '.$ip.' -> '.$vol.' '.$msg);
}
function bosebass($bass,$ip=101) {
	$bass=1*$bass;
	$xml="<bass>$bass</bass>";
	bosepost("bass", $xml, $ip);
}
function bosepreset($preset,$ip=101) {
	bosekey($preset, 0, $ip, true);
}
function boseplaylist() {
	global $time;
	$dag=floor($time/86400);
	$dow=date("w");
	if($dow==0||$dow==6)$weekend=true; else $weekend=false;
	if ($weekend==true) {
		if ($dag % 3 == 0) $preset='MIX-3';
		elseif ($dag % 2 == 0) $preset='MIX-2';
		else $preset='MIX-1';
	} else {
		if ($dag % 3 == 0) $preset='EDM-3';
		elseif ($dag % 2 == 0) $preset='EDM-2';
		else $preset='EDM-1';
	}
	$map = [
		'EDM-1' => 'PRESET_1',
		'EDM-2' => 'PRESET_2',
		'EDM-3' => 'PRESET_3',
		'MIX-1' => 'PRESET_4',
		'MIX-2' => 'PRESET_5',
		'MIX-3' => 'PRESET_6',
	];
	return $map[$preset];
}
function bosezone($ip,$vol='') {
	global $d,$time,$dow,$weekend,$t;
	if ($d['weg']['s']<=1) {
		if ($d['boseliving']['s']=='Off') sw('boseliving', 'On', basename(__FILE__).':'.__LINE__);
		if ($time<strtotime('21:00')&&$d['boseliving']['s']=='On'&&past('boseliving')>60) {
			if ($d['bose101']['s']=='Off') {
				lg(basename(__FILE__).':'.__LINE__);
				sw('bose101', 'On', basename(__FILE__).':'.__LINE__);
				bosekey(boseplaylist(), 750000, 101, basename(__FILE__).':'.__LINE__);
				lg('Bose zone time='.$time.'|'.$t+1800);
				if ($d['lgtv']['s']=='On'&&$d['eettafel']['s']==0) bosevolume(0, 101, basename(__FILE__).':'.__LINE__);
				elseif ($d['alexslaapt']['s']==1) bosevolume(11, 101, basename(__FILE__).':'.__LINE__);
				else bosevolume(22, 101, basename(__FILE__).':'.__LINE__);
			}		
			if ($ip>101) {
				if ($d['bose'.$ip]['s']=='Off') sw('bose'.$ip, 'On', basename(__FILE__).':'.__LINE__);
				$mapip = [
					102 => '<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.102">304511BC3CA5</member></zone>',
					103 => '<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.103">C4F312F65070</member></zone>',
					104 => '<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.104">C4F312DCE637</member></zone>',
					105 => '<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.105">587A628BB5C0</member></zone>',
					106 => '<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.106">C4F312F89670</member></zone>',
					107 => '<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.107">B0D5CC065C20</member></zone>',
				];
				if ($d['bose101']['s']=='Off'&&$d['bose'.$ip]['s']=='Off') {
					lg(basename(__FILE__).':'.__LINE__);
					sw('bose101', 'On', basename(__FILE__).':'.__LINE__);
					if ($d['lgtv']['s']=='On'&&$d['eettafel']['s']==0) bosevolume(0, 101, basename(__FILE__).':'.__LINE__);
					elseif ($d['alexslaapt']['s']==1) bosevolume(15, 101, basename(__FILE__).':'.__LINE__);
					else bosevolume(22, 101, basename(__FILE__).':'.__LINE__);
					usleep(100000);
					bosepost('setZone', $mapip[$ip], 101);
					if ($vol=='') {
						if ($d['alexslaapt']['s']==1) bosevolume(15, $ip, basename(__FILE__).':'.__LINE__);
						else bosevolume(22, $ip, basename(__FILE__).':'.__LINE__);
					} else {
						if ($d['alexslaapt']['s']==1) $vol-=10;
						bosevolume($vol, $ip, basename(__FILE__).':'.__LINE__);
					}
				} else /*if ($d['bose'.$ip]['s']=='Off') */{
					bosepost('setZone',  $mapip[$ip], 101);
					store('bose'.$ip, 'On');
					if ($vol=='') {
						if ($d['alexslaapt']['s']==1) bosevolume(15, $ip, basename(__FILE__).':'.__LINE__);
						else bosevolume(22, $ip, basename(__FILE__).':'.__LINE__);
					} else {
						
						if ($d['alexslaapt']['s']==1) $vol-=10;
						bosevolume($vol, $ip, basename(__FILE__).':'.__LINE__);
					}
				}
			}
		}
	}
}
function bosepost($method, $xml, $ip=101, $log=false) {
    $host = "192.168.2.$ip";
    $port = 8090;
    $path = "/$method";
    $headers = "POST $path HTTP/1.1\r\n";
    $headers .= "Host: $host\r\n";
    $headers .= "Content-Type: application/xml\r\n";
    $headers .= "Content-Length: ".strlen($xml)."\r\n";
    $headers .= "Connection: Close\r\n\r\n";
    $fp = @fsockopen($host, $port, $errno, $errstr, 0.25);
    if ($fp) {
        fwrite($fp, $headers.$xml);
        fclose($fp);
//        if ($log) lg("💡 Bose $method verstuurd naar $host");
    } else {
        if ($log) lg("❌ Bose socket fout: $errstr ($errno)");
    }
}

function sirene($msg) {
	lg(' >>> SIRENE '.$msg);
	$last=getCache('sirene');
	$time=time();
	lg(' >>> last='.$last.'	time='.$time);
	if ($last>$time-300) {
		sw('sirene', 'On', basename(__FILE__).':'.__LINE__);
		telegram($msg.' om '.date("G:i:s", $time), false, 3);
	}
	setCache('sirene', $time);
}
function daikin_ips() {
	return [
		'living' => 161,
		'kamer'  => 162,
		'alex'	=> 163,
	];
}

function http_get($url, $retries = 2, $timeout = 2) {
	$ctx = stream_context_create(['http' => ['timeout' => $timeout]]);
	for ($i=0; $i <= $retries; $i++) {
		$data = @file_get_contents($url, false, $ctx);
		if ($data !== FALSE) return $data;
		usleep(200000);
	}
	return FALSE;
}
function daikinstatus($device,$log='') {
	$ips = daikin_ips();
	if (!isset($ips[$device])) return FALSE;
	$url = "http://192.168.2.{$ips[$device]}/aircon/get_control_info";
	$data = http_get($url);
	if ($data === FALSE) {
		if (strlen($log)>0) $msg="daikinstatus: geen antwoord van $device ($url)	| ".$log;
		else $msg="daikinstatus: geen antwoord van $device ($url)";
		alert('daikinstatus', $msg, 1800);
		return FALSE;
	}
	if (stripos($data, "SERIAL IF FAILURE") !== false) {
		alert('daikinstatus', "daikinstatus: SERIAL IF FAILURE van $device ($url) → power cycle",1800);
//		sw('daikin', 'Off', $user.':'.__LINE__);
//		sleep(5);
//		sw('daikin', 'On',  $user.':'.__LINE__);
		return FALSE;
	}
	$array = explode(",", $data);
	$ci = [
		'power' => null,
		'mode'  => null,
		'adv'	=> 'default', 
		'set'	=> null,
		'fan'	=> null
	];
	foreach ($array as $value){
		$pair = explode("=", $value, 2);
		if (count($pair) !== 2) continue;
		list($key, $val) = $pair;
		if	 ($key=='pow') $ci['power'] = $val;
		elseif ($key=='mode') $ci['mode']  = $val;
		elseif ($key=='adv') $ci['adv']	= $val;
		elseif ($key=='stemp') $ci['set']	= $val;
		elseif ($key=='f_rate') $ci['fan']	= $val;
	}
	return json_encode($ci);
}
function daikinset($device, $power, $mode, $stemp, $msg='', $fan='A', $spmode=-1, $maxpow=false) {
	global $d, $time, $lastfetch,$daikin;
	$lastfetch = $time;
	$ips = daikin_ips();
	$base = "http://192.168.2.{$ips[$device]}";
	$url = "$base/aircon/set_control_info?pow=$power&mode=$mode&stemp=$stemp&f_rate=$fan&shum=0&f_dir=0";
	if(!http_get($url)) return false;
	if ($d['heating']['s']>=0) lg("🔥 daikinset [$device] power=$power | mode=$mode | temp=$stemp | fan=$fan | maxpow=$maxpow");
	else  lg("❄️ daikinset [$device] power=$power | mode=$mode | temp=$stemp | fan=$fan | maxpow=$maxpow");
	usleep(100000);
	if ($spmode==-1) {
		if(!http_get("$base/aircon/set_special_mode?set_spmode=1&spmode_kind=2")) return false;
	} elseif ($spmode==0) {
		if(!http_get("$base/aircon/set_special_mode?set_spmode=0&spmode_kind=1")) return false;
	} elseif ($spmode==1) {
		if(!http_get("$base/aircon/set_special_mode?set_spmode=1&spmode_kind=1")) return false;
	}
	usleep(100000);
	foreach($ips as $k=>$ip) {
		if ($maxpow==100) {
			$url="http://192.168.2.$ip/aircon/set_demand_control?type=1&en_demand=0&mode=0&max_pow=100&scdl_per_day=0&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0";
		} else {
			$url="http://192.168.2.$ip/aircon/set_demand_control?type=1&en_demand=1&mode=0&max_pow=$maxpow&scdl_per_day=0&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0";
		}
		if(!http_get($url)) return false;
		usleep(50000);
	}
	return true;
}
function hasstoken() {
	global $user;
	switch ($user) {
		case 'cron10': return 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiI2YmQ0NDZjNTgyZTY0NDU5YTkxNmE4ZThmZDVhNWFjNCIsImlhdCI6MTc0OTMxODM1NywiZXhwIjoyMDY0Njc4MzU3fQ.S6oPTz8PrEChIU2Ogx4qFgcCBLzKy8tLeFKA_NfDbH8';
		case 'cron60': return 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJiZTc4NDhiOGNhMmY0OTIyODQxOGJkMDAyYmJkMDM0YyIsImlhdCI6MTc0OTMxOTM3NSwiZXhwIjoyMDY0Njc5Mzc1fQ.gn-THiHH1yf_CugxLoqNvbeftRxW_CsLJ2lPWt5c2Ro';
		case 'cron300': return 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiIzNTE5NjQ1Zjk5NzY0MDcxYjIyODU3Mzg2YmQ3NWIzYiIsImlhdCI6MTc1MDE1MjI1NSwiZXhwIjoyMDY1NTEyMjU1fQ.eMWEEwlxDQL-t4xhpqwenJ1xZh8Ct44vQ1f5_5RB-UU';
		case 'cron3600': return 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiI2MjliNzVmZTY3ZDc0MWI0YmM3NDc2ZDA5ODQzNTEyOCIsImlhdCI6MTc1MDE1MjM2NCwiZXhwIjoyMDY1NTEyMzY0fQ.76X_fwqF1JVeZKN6Vrv-H7DrzGQ2NJnIQbIr7yCHCrI';
		case 'heating': return 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJjNDk4ZDU1MTA2ZWI0MWJkYWE3ZWZjNTMwMmEyYzg3NiIsImlhdCI6MTc1MDE1MjQxMCwiZXhwIjoyMDY1NTEyNDEwfQ.kKqGJU4ALE6_HMQ5c4kwtcW8IeOVhhBc4Spg3lmheJs';
		case 'Guy': return 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJmMTQ1ZThmNjYyNTk0Mjk5OWM2ZTUyMWNhZWY3MTUxYSIsImlhdCI6MTc0ODQwMDM0OCwiZXhwIjoyMDYzNzYwMzQ4fQ.SDUxztRFwr9p7w29LQ-_fDa5l4KB1cOTrz_riHQCFlY';
		case 'Kirby': return 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiIxNGM2YmJhY2EwMzY0NzYwOTI4Y2VhMjdjZDVjOWEwNCIsImlhdCI6MTc1MDE1MjQ2MSwiZXhwIjoyMDY1NTEyNDYxfQ.IrQG72soNQcprvzDwKajkuQnmG-kULIiBS35sKLDxsI';
		default: return 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJjODYzYTllZGY2OGI0ZTc4YjFkOGFkOWQ4YzM3MDRhMiIsImlhdCI6MTc1MDE1MjUwOCwiZXhwIjoyMDY1NTEyNTA4fQ.U-t5m66b9sx7QCWVXEStmt6AIcSN0zbSHHKnR13zEu0';
	}
}

function hass(string $domain, string $service, string $entity = '', array $data = []): void {
    global $d;
    static $socket = null;
    static $lastUse = 0;
    $d['time'] ??= time();
    $keepAliveTime = 300;
    if ($socket === null || ($d['time'] - $lastUse) >= $keepAliveTime || @feof($socket)) {
        $socket && @fclose($socket);
        $socket = @stream_socket_client(
            'tcp://192.168.2.26:8123',
            $errno,
            $errstr,
            0.2,
            STREAM_CLIENT_CONNECT
        );
        if (!$socket) {
            lg("❌ HASS socket fout: $errstr ($errno)");
            return;
        }
        stream_set_blocking($socket, true);
        stream_set_write_buffer($socket, 0);
        stream_set_timeout($socket, 30);
    }
    if ($entity !== '') {
        $data['entity_id'] = $entity;
    }
    $payload = json_encode($data);
    $request = sprintf(
        "POST /api/services/%s/%s HTTP/1.1\r\n" .
        "Host: 192.168.2.26\r\n" .
        "Content-Type: application/json\r\n" .
        "Authorization: Bearer %s\r\n" .
        "Content-Length: %d\r\n" .
        "Connection: keep-alive\r\n" .
        "Keep-Alive: timeout=300\r\n\r\n%s",
        $domain, $service, hasstoken(), strlen($payload), $payload
    );
    
    fwrite($socket, $request);
    fflush($socket);
    $lastUse = $d['time'];
}
function hassinput($domain,$service,$entity,$input) {
	lg('HASSinput '.$domain.' '.$service.' '.$entity,4);
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,'http://192.168.2.26:8123/api/services/'.$domain.'/'.$service);
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/json','Authorization: Bearer '.hasstoken()));
	curl_setopt($ch,CURLOPT_POSTFIELDS,'{"entity_id":"'.$entity.'","source":"'.$input.'"}');
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_FRESH_CONNECT,true);
	curl_setopt($ch,CURLOPT_TIMEOUT,5);
	curl_exec($ch);
	curl_close($ch);
}
function hassget() {
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,'http://192.168.2.26:8123/api/states');
	curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/json','Authorization: Bearer '.hasstoken()));
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_FRESH_CONNECT,true);
	curl_setopt($ch,CURLOPT_TIMEOUT,5);
	$response=curl_exec($ch);
	curl_close($ch);
	return $response;
}
function hassservices() {
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,'http://192.168.2.26:8123/api/services');
	curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/json','Authorization: Bearer '.hasstoken()));
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_FRESH_CONNECT,true);
	curl_setopt($ch,CURLOPT_TIMEOUT,5);
	$response=curl_exec($ch);
	curl_close($ch);
	return $response;
}
function hassnotify($title, $message, $target = 'mobile_app_iphone_guy', $critical = false) {
    $token = hasstoken();
    if ($critical) {
        $ha_url_push = 'http://192.168.2.26:8123/api/services/notify/' . $target;
        $push_data = [
            "message" => $message,
            "title"   => $title,
            "data"    => [
                "ttl"      => 0,
                "priority" => "high",
                "persistent" => true,
                "push" => [
                    "sound" => [
                        "name"     => "default",
                        "critical" => 1,
                        "volume"   => 1.0
                    ]
                ]
            ]
        ];
        for ($x = 1; $x <= 10; $x++) {
            $options = [
                "http" => [
                    "method"  => "POST",
                    "header"  => "Authorization: Bearer $token\r\nContent-Type: application/json\r\n",
                    "content" => json_encode($push_data),
                    "timeout" => 3
                ]
            ];
            $context = stream_context_create($options);
            $response = @file_get_contents($ha_url_push, false, $context);
            if ($response !== FALSE) break;
            sleep($x);
        }
        $ha_url_dashboard = 'http://192.168.2.26:8123/api/services/persistent_notification/create';
        $dashboard_data = [
            "title" => $title,
            "message" => $message,
            "notification_id" => uniqid("notif_")
        ];
        $options = [
            "http" => [
                "method"  => "POST",
                "header"  => "Authorization: Bearer $token\r\nContent-Type: application/json\r\n",
                "content" => json_encode($dashboard_data),
                "timeout" => 10
            ]
        ];
        $context = stream_context_create($options);
        @file_get_contents($ha_url_dashboard, false, $context);
    } else {
        telegram($title . ": " . $message);
    }
    return true;
}

function curl($url) {
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_FRESH_CONNECT,true);
	curl_setopt($ch,CURLOPT_TIMEOUT,5);
	$data=curl_exec($ch);
	curl_close($ch);
	return $data;
}
final class Database {
    private static ?PDO $instance = null;
    
    private function __construct() {}
    private function __clone(): void {}
    
    public static function getInstance(): PDO {
        return self::$instance ??= self::createConnection();
    }
    
    private static function createConnection(): PDO {
        try {
            return new PDO(
                dsn: "mysql:host=192.168.2.23;dbname=domotica;charset=latin1",
                username: 'dbuser',
                password: 'dbuser',
                options: [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_STRINGIFY_FETCHES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES latin1",
                    PDO::ATTR_TIMEOUT => 5,
                    PDO::MYSQL_ATTR_FOUND_ROWS => true
                ]
            );
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            throw new RuntimeException('Database connection failed.', 0, $e);
        }
    }
    
    public static function reset(): void {
        self::$instance = null;
    }
    
    public static function isConnected(): bool {
        return self::$instance !== null;
    }
}
function fetchdata(): array {
	global $d;
	for ($attempt = 0; $attempt <= 4; $attempt++) {
		try {
			$db = Database::getInstance();
			static $stmt = null;
			$stmt ??= $db->prepare("SELECT n,s,t,m,d,i,p,rt,f FROM devices");
			$stmt->execute();
			foreach ($stmt->fetchAll(PDO::FETCH_NUM) as [$n, $s, $t, $m, $deviceD, $i, $p, $rt, $f]) {
				$d[$n] = array_filter(
					compact('s', 't', 'm', 'deviceD', 'i', 'p', 'rt', 'f'),
					static fn($v) => $v !== null
				);
				if (isset($d[$n]['deviceD'])) {
					$d[$n]['d'] = $d[$n]['deviceD'];
					unset($d[$n]['deviceD']);
				}
			}
			break;
		} catch (PDOException $e) {
			$isRecoverable = in_array($e->getCode(), [2006, 'HY000'], true) && $attempt < 4;
			if ($isRecoverable) {
				lg(' ♻  DB gone away → reconnect & retry fetchdata', 5);
				Database::reset();
				$stmt = null;
				$attempt > 0 && sleep($attempt);
				continue;
			}
			lg('FETCHDATA ERROR! ' . $e->getCode());
			throw $e;
		}
	}
	if ($en = json_decode(getCache('en'))) {
		$d['n'] = $en->n ?? null;
		$d['a'] = $en->a ?? null;
		$d['b'] = $en->b ?? null;
		$d['c'] = $en->c ?? null;
		$d['z'] = $en->z ?? null;
	}
	return $d;
}

function roundUpToAny($n,$x=5) {
	return round(($n+$x/2)/$x)*$x;
}
function roundDownToAny($n,$x=5) {
	return floor($n/$x) * $x;
}
function isoToLocalTimestamp(string $isoTime): int {
	$utc = new DateTime($isoTime, new DateTimeZone("UTC"));
	$utc->setTimezone(new DateTimeZone(date_default_timezone_get()));
	return $utc->getTimestamp();
}
function republishmqtt() {
	global $d;
	$ha_url = 'http://192.168.2.26:8123';
	$ha_token = 'Bearer '.hasstoken();
	$base_topic = 'homeassistant';
	foreach ($d as $device => $i) {
		if (!isset($i['d'])) continue;
		$entity_id = null;
		$to_publish = [];
		if ($i['d'] === 's') {
			$entity_id = "switch.$device";
			$url = "$ha_url/api/states/$entity_id";
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: $ha_token"]);
			$result = curl_exec($ch);
			curl_close($ch);
			$data = json_decode($result, true);
			if (!isset($data['state'])) {
				lg("❌ Fout: kon status van $entity_id niet ophalen");
				continue;
			}
			list($domain, $object_id) = explode('.', $entity_id);
			$state = ucfirst($data['state']);
			if ($state!=$i['s']) $to_publish[] = [
				'topic' => "$base_topic/$domain/$object_id/state",
				'payload' => $state
			];
		} elseif ($i['d'] === 'hd') {
			$entity_id = "light.$device";
			$url = "$ha_url/api/states/$entity_id";
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: $ha_token"]);
			$result = curl_exec($ch);
			curl_close($ch);
			$data = json_decode($result, true);
			$attributes = $data['attributes'] ?? [];
			if (!array_key_exists('brightness', $attributes)) {
				lg("❌ Geen brightness attribuut voor $entity_id");
				continue;
			}
			list($domain, $object_id) = explode('.', $entity_id);
			$brightness = $attributes['brightness'] ?? 0;
			$brightness=round((int)$brightness / 2.55);
			if ($brightness!=$i['s']) {
				if ($device=='bureellinks') lg('bureellinks: '.$brightness.'|'.$i['s']);
				elseif ($device=='bureelrechts') lg('bureelrechts: '.$brightness.'|'.$i['s']);
				$to_publish[] = [
					'topic' => "$base_topic/$domain/$object_id/brightness",
					'payload' => $brightness
				];
			}
		} else continue;
		usleep(50000);
		foreach ($to_publish as $pub) {
			$payload = [
				'topic' => $pub['topic'],
				'payload' => $pub['payload'],
				'retain' => true
			];
			$ch = curl_init("$ha_url/api/services/mqtt/publish");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				"Authorization: $ha_token",
				"Content-Type: application/json"
			]);
			$response = curl_exec($ch);
			curl_close($ch);
			lg("🔁 MQTT: $entity_id → {$pub['payload']} → {$pub['topic']}");
			usleep(50000);
		}
	}
}
function setBatterijLedBrightness(int $brightness) { 
	$payload = json_encode([ 'status_led_brightness_pct' => $brightness ]);
	$ch = curl_init("https://battery/api/system");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	curl_setopt($ch, CURLOPT_HTTPHEADER, [ "Authorization: Bearer 9D03BCA88274A4C1603E4D0F5DD21AB0", "X-Api-Version: 2", "Content-Type: application/json" ]);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	$response = curl_exec($ch);
	$error = curl_error($ch);
	curl_close($ch);
	if ($error) {
		lg("❌ Fout bij LED brightness: $error");
		return false;
	} else {
		return json_decode($response, true);
	}
}
function setCache(string $key, $value): bool {
    return file_put_contents('/dev/shm/cache/' . $key .'.txt', $value, LOCK_EX) !== false;
}
function getCache(string $key, $default = false) {
    $data = @file_get_contents('/dev/shm/cache/' . $key .'.txt');
    return $data === false ? $default : $data;
}
static $localCache = [];
function setCacheFast(string $key, $value) {
    global $localCache;
    $localCache[$key] = $value;
}
function getCacheFast(string $key, $default = false) {
    global $localCache;
    if (isset($localCache[$key])) {
        return $localCache[$key];
    }
    $file = '/dev/shm/cache/' . $key . '.txt';
    $data = @file_get_contents($file);
    if ($data === false) return $default;
    $localCache[$key] = $data;
    return $data;
}