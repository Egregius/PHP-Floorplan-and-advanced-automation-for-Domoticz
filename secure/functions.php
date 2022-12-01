<?php
/**
 * Pass2PHP
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
require '/var/www/config.php';
$dow=date("w");
if($dow==0||$dow==6)$weekend=true; else $weekend=false;
function dbconnect() {
	global $dbname,$dbuser,$dbpass;
	return new PDO("mysql:host=127.0.0.1;dbname=$dbname;",$dbuser,$dbpass);
}
$db=dbconnect();
function fetchdata() {
	global $db;
	if(!isset($db)) $db=dbconnect();
	$stmt=$db->query("select n,i,s,t,m,dt,icon from devices;");
	while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) $d[$row['n']] = $row;
	return $d;
}
function huisslapen() {
	global $d,$boseipbuiten;
	sl(array('hall','inkom','eettafel','zithoek','wasbak','terras','ledluifel'), 0, basename(__FILE__).':'.__LINE__);
	sw(array('garageled','garage','pirgarage','pirkeuken','pirliving','pirinkom','pirhall','kristal','bureel','lamp kast','tuin','snijplank','zolderg','voordeur','wc','dampkap','GroheRed','kookplaat','nvidia'), 'Off', basename(__FILE__).':'.__LINE__);
	foreach (array('living_set','alex_set','kamer_set','badkamer_set','eettafel','zithoek','luifel') as $i) {
		if ($d[$i]['m']!=0) storemode($i, 0, basename(__FILE__).':'.__LINE__);
	}
	$data=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.101:8090/now_playing'))), true);
	if (!empty($data)) {
		if (isset($data['@attributes']['source'])) {
			if ($data['@attributes']['source']!='STANDBY') {
				bosekey("POWER", 0, 101);
				foreach (array(101,102,103,104,105,106,107) as $x) {
					if ($d['bose'.$x]['s']!='Off') sw('bose'.$x, 'Off', basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
	foreach(array('speelkamer', 'alex') as $i) {
		if ($d[$i]['s']>0&&$d[$i]['m']!=1) storemode($i, 1, basename(__FILE__).':'.__LINE__);
	}
	if ($d['auto']['s']=='Off') sw('auto', 'On', basename(__FILE__).':'.__LINE__);
}
function huisthuis() {
	global $d;
	store('Weg', 0);
}
function douche() {
	global $d;
	$douchegas=$d['douche']['s']*10;
	$douchewater=$d['douche']['m']*1;
	$gas=0.00111;
	$water=0.00477;
	if ($douchegas>=1&&$douchewater>=1) {
		$msg='Douche__Gas: '.$douchegas.'L = '.number_format($douchegas*$gas, 2, ',', '').'€__Water: '.$douchewater.'L = '.number_format($douchewater*$water, 2, ',', '').'€__Som = '.number_format(($douchegas*$gas)+($douchewater*$water), 2, ',', '').'€';
		telegram($msg, true, 2);
	}
	store('douche', 0, basename(__FILE__).':'.__LINE__);
	storemode('douche', 0, basename(__FILE__).':'.__LINE__);
}
function roundUpToAny($n,$x=5) {
	return round(($n+$x/2)/$x)*$x;
}
function boseplayinfo($sound, $vol=50, $log='', $ip=101) {
	global $d, $googleTTSAPIKey;
	if(empty($d)) $d=fetchdata();
	lg('boseplayinfo: '.$sound);
	$raw=rawurlencode($sound);
	if(file_exists('/var/www/html/sounds/'.$sound.'.mp3')) {
		$volume=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.101:8090/volume'))), true);
		$vol=$volume['actualvolume'];
		$xml="<play_info><app_key>UJvfKvnMPgzK6oc7tTE1QpAVcOqp4BAY</app_key><url>http://192.168.2.2/sounds/$raw.mp3</url><service>$sound</service><reason>$sound</reason><message>$sound</message><volume>$vol</volume></play_info>";
		bosepost('speaker', $xml);
		bosevolume($volume['actualvolume'], 101, basename(__FILE__).':'.__LINE__);
	} else {
		require 'gcal/google-api-php-client/vendor/autoload.php';
		$client=new GuzzleHttp\Client();
		$requestData=['input'=>['text'=>$sound],'voice'=>['languageCode'=>'nl-NL','name'=>'nl-NL-Wavenet-B'],'audioConfig'=>['audioEncoding'=>'MP3','pitch'=>0.00,'speakingRate'=>1.00,'effectsProfileId' => 'large-home-entertainment-class-device']];
		try {
			$response=$client->request('POST', 'https://texttospeech.googleapis.com/v1beta1/text:synthesize?key='.$googleTTSAPIKey, ['json'=>$requestData]);
			$fileData=json_decode($response->getBody()->getContents(), true);
			$audio=base64_decode($fileData['audioContent']);
			if(strlen($audio)>10) {
				file_put_contents('/var/www/html/sounds/'.$sound.'.mp3', $audio);
				$volume=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.101:8090/volume'))), true);
				$vol=$volume['actualvolume'];
				$xml="<play_info><app_key>UJvfKvnMPgzK6oc7tTE1QpAVcOqp4BAY</app_key><url>http://192.168.2.2/sounds/$raw.mp3</url><service>$sound</service><reason>$sound</reason><message>$sound</message><volume>$vol</volume></play_info>";
				bosepost('speaker', $xml);
				bosevolume($volume['actualvolume'], 101, basename(__FILE__).':'.__LINE__);
			}
		} catch (Exception $e) {
			exit('Something went wrong: ' . $e->getMessage());
		}

	}
}
function waarschuwing($msg) {
	telegram($msg, false, 2);
	sl('Xring', 40, basename(__FILE__).':'.__LINE__);
	sw('sirene', 'On', basename(__FILE__).':'.__LINE__);
	sleep(3);
	sw('sirene', 'Off', basename(__FILE__).':'.__LINE__);
	sl('Xring', 0, basename(__FILE__).':'.__LINE__);
	die($msg);
}
function past($name) {
	global $d;
	if (!empty($d[$name]['t'])) return TIME-$d[$name]['t'];
	else return 999999999;
}
function idx($name) {
	global $d;
	if (!is_array($d)) $d=fetchdata();
	if ($d[$name]['i']>0) return $d[$name]['i'];
	else return 0;
}
function sl($name,$level,$msg='') {
	global $user,$d,$domoticzurl;
	if(!isset($d))$d=fetchdata();
	 if (is_array($name)) {
		foreach ($name as $i) {
			if ($d[$i]['s']!=$level) {
				sl($i, $level, $msg);
				usleep(100000);
			}
		}
	} else {
		lg(' (SETLEVEL)	'.str_pad($user, 13, ' ', STR_PAD_LEFT).' => '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' => '.$level.' ('.$msg.')');
		if ($d[$name]['i']>0) {
			if ($d[$name]['s']!=$level) file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx='.$d[$name]['i'].'&switchcmd=Set%20Level&level='.$level);
			if (str_starts_with($name, 'R')) store($name, $level, $msg);
		} else store($name, $level, $msg);
	}
	if ($name=='Rbureel') sl('rolluik bureel', $level, basename(__FILE__).':'.__LINE__);
	elseif ($name=='Rliving') sl('rolluik achteraan', $level, basename(__FILE__).':'.__LINE__);
	elseif ($name=='RkeukenL') sl('rolluik keuken', $level, basename(__FILE__).':'.__LINE__);
}
function rgb($name,$hue,$level,$check=false) {
	global $user,$d,$domoticzurl;
	lg(' (RGB)		'.$user.' => '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' => '.$level);
	if ($d[$name]['i']>0) {
		if ($check==false) file_get_contents($domoticzurl.'/json.htm?type=command&param=setcolbrightnessvalue&idx='.$d[$name]['i'].'&hue='.$hue.'&brightness='.$level.'&iswhite=false');
		else {
			if ($d[$name]['s']!=$$level) {
				file_get_contents($domoticzurl.'/json.htm?type=command&param=setcolbrightnessvalue&idx='.$d[$name]['i'].'&hue='.$hue.'&brightness='.$level.'&iswhite=false');
			}
		}
	} else store($name, $level);
}
function resetsecurity() {
	global $d,$domoticzurl;
	if (!isset($d)) $d=fetchdata();
	if ($d['sirene']['s']!='Off') {
		sw('sirene', 'Off', basename(__FILE__).':'.__LINE__);
		usleep(100000);
		store('sirene', 'Off', basename(__FILE__).':'.__LINE__);
	}
	foreach (array('SDbadkamer','SDkamer','SDalex','SDspeelkamer','SDzolder','SDliving') as $i) {
		if ($d[$i]['s']!='Off') {
			file_get_contents($domoticzurl.'/json.htm?type=command&param=resetsecuritystatus&idx='.$d[$i]['i'].'&switchcmd=Normal');
			store($i, 'Off', basename(__FILE__).':'.__LINE__);
		}
	}
}
function sw($name,$action='Toggle',$msg='') {
	global $user,$d,$domoticzurl,$db;
	if (!isset($d)) $d=fetchdata();
	if (is_array($name)) {
		foreach ($name as $i) {
			if ($d[$i]['s']!=$action) {
				sw($i, $action, $msg);
				usleep(100000);
			}
		}
	} else {
		$msg=' (SWITCH)'.str_pad($user, 13, ' ', STR_PAD_LEFT).' => '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' => '.$action.' ('.$msg.')';
		if ($d[$name]['i']>0) {
			lg($msg);
			if ($d[$name]['s']!=$action) file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx='.$d[$name]['i'].'&switchcmd='.$action);
		} else store($name, $action, $msg);
	}
}
function fvolume($cmd) {
	global $d;
	if (!isset($d)) $d=fetchdata();
	if ($d['sony']['s']=='On') {
		if ($cmd<0) sony('audio','{"method":"setAudioVolume","id":1,"params":[{"volume":"'.$cmd.'","output":""}],"version":"1.1"}');
		else sony('audio','{"method":"setAudioVolume","id":1,"params":[{"volume":"+'.trim($cmd).'","output":""}],"version":"1.1"}');
	} else {
		if ($cmd=='down') {
			if ($d['tv']['s']=='On'&&$d['lgtv']['s']=='On') {
				exec('/var/www/html/secure/lgtv.py -c volume-down 192.168.2.6');
				exec('/var/www/html/secure/lgtv.py -c volume-down 192.168.2.6');
			} elseif ($d['bose101']['s']=='On') {
				$nowplaying=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.101:8090/now_playing'))), true);
				if (!empty($nowplaying)) {
					if (isset($nowplaying['@attributes']['source'])) {
						if ($nowplaying['@attributes']['source']!='STANDBY') {
							$volume=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.101:8090/volume'))), true);
							$cv=$volume['actualvolume'];
							if ($cv==0) exit;
							elseif ($cv>50) bosevolume($cv-6);
							elseif ($cv>30) bosevolume($cv-5);
							elseif ($cv>20) bosevolume($cv-4);
							elseif ($cv>10) bosevolume($cv-3);
							else bosevolume($cv-2);
						}
					}
				}
			}
		} elseif ($cmd=='up') {
			if ($d['tv']['s']=='On'&&$d['lgtv']['s']=='On') {
				exec('/var/www/html/secure/lgtv.py -c volume-up 192.168.2.6');
				exec('/var/www/html/secure/lgtv.py -c volume-up 192.168.2.6');
			} elseif ($d['bose101']['s']=='On') {
				$nowplaying=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.101:8090/now_playing'))), true);
				if (!empty($nowplaying)) {
					if (isset($nowplaying['@attributes']['source'])) {
						if ($nowplaying['@attributes']['source']!='STANDBY') {
							$volume=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.101:8090/volume'))), true);
							$cv=$volume['actualvolume'];
							if ($cv>80) exit;
							elseif ($cv>50) bosevolume($cv+6);
							elseif ($cv>30) bosevolume($cv+5);
							elseif ($cv>20) bosevolume($cv+4);
							elseif ($cv>10) bosevolume($cv+3);
							else bosevolume($cv+2);
						}
					}
				}
			}
		}
	}
}
function lgcommand($action,$msg='') {
	global $lgtvip, $lgtvmac;
	if ($action=='on') exec('/var/www/html/secure/lgtv.py -c on -a '.$lgtvmac.' '.$lgtvip, $output, $return_var);
	else shell_exec('/var/www/html/secure/lgtv.py -c '.$action.' '.$lgtvip.' > /dev/null 2>&1 &');
}
function store($name,$status,$msg='',$idx=null,$force=true) {
	global $d, $db, $user;
	if (!isset($d)) $d=fetchdata();
	$time=time();
	if ($idx>0) $db->query("INSERT INTO devices (n,i,s,t) VALUES ('$name','$idx','$status','$time') ON DUPLICATE KEY UPDATE s='$status',i='$idx',t='$time';");
	else $db->query("INSERT INTO devices (n,s,t) VALUES ('$name','$status','$time') ON DUPLICATE KEY UPDATE s='$status',t='$time';");
	if ($name!='crypto'/*&&!endswith($name, '_temp')*/) lg(' (STORE) '.str_pad($user, 13, ' ', STR_PAD_LEFT).' => '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' => '.$status.'	('.$msg.')');
}
function storemode($name,$mode,$msg='',$time=0) {
	global $db, $user;
	$time=time()+$time;
	if(!isset($db)) $db=dbconnect();
	$db->query("INSERT INTO devices (n,m,t) VALUES ('$name','$mode','$time') ON DUPLICATE KEY UPDATE m='$mode',t='$time';");
	lg(' (STOREMODE) '.str_pad($user, 9, ' ', STR_PAD_LEFT).' => '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' => '.$mode.'	('.$msg.')');
}
function storeicon($name,$icon,$msg='') {
	global $d, $db, $user;
	$time=TIME;
	if ($d[$name]['icon']!=$icon) {
		if(!isset($db)) $db=dbconnect();
		$db->query("INSERT INTO devices (n,t,icon) VALUES ('$name','$time','$icon') ON DUPLICATE KEY UPDATE t='$time',icon='$icon';");
		if (!endswith($name, '_temp')) lg(' (STOREICON)	'.$user.'	=> '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' => '.$icon.'	('.$msg.')');
	}
}
function alert($name,$msg,$ttl,$silent=true,$to=1) {
	global $db;
	if(!isset($db)) $db=dbconnect();
	$last=0;
	$stmt=$db->query("SELECT t FROM alerts WHERE n='$name';");
	while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
		if (isset($row['t'])) $last=$row['t'];
	}
	if ($last < TIME-$ttl) {
		$time=TIME;
		$db->query("INSERT INTO alerts (n,t) VALUES ('$name','$time') ON DUPLICATE KEY UPDATE t='$time';");
		telegram($msg, $silent, $to);
		lg('alert='.$last);
	}
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
function sony($lib,$json) {
	global $kodiurl;
	$ch=curl_init('http://192.168.2.5:10000/sony/'.$lib);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	$result=curl_exec($ch);
	curl_close($ch);
	return $result;
}
function ud($name,$nvalue,$svalue,$check=false,$smg='') {
	global $user,$d,$domoticzurl;
	if ($d[$name]['i']>0) {
		if ($check==true) {
			if ($d[$name]['s']!=$svalue) {
				return file_get_contents($domoticzurl.'/json.htm?type=command&param=udevice&idx='.$d[$name]['i'].'&nvalue='.$nvalue.'&svalue='.$svalue);
			}
		} else return file_get_contents($domoticzurl.'/json.htm?type=command&param=udevice&idx='.$d[$name]['i'].'&nvalue='.$nvalue.'&svalue='.$svalue);
	} else store($name, $svalue, basename(__FILE__).':'.__LINE__);
	lg(' (udevice) | '.$user.'=> '.str_pad($name, 13, ' ', STR_PAD_LEFT).' =>'.$nvalue.','.$svalue.(isset($msg)?' ('.$msg:')'));
}
function zwavecancelaction(){global $domoticzurl;file_get_contents($domoticzurl.'/ozwcp/admpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>'cancel')),),)));}
function zwaveCommand($node,$command) {
	global $domoticzurl;
	$cm=array('Refresh'=>'racp','AssignReturnRoute'=>'assrr','DeleteAllReturnRoutes'=>'delarr','NodeNeighbourUpdate'=>'reqnnu','RefreshNodeInformation'=>'refreshnode','RequestNetworkUpdate'=>'reqnu','HasNodeFailed'=>'hnf','Cancel'=>'cancel');
	$cm=$cm[$command];
	for($k=1;$k<=5;$k++){
		$result=file_get_contents($domoticzurl.'/ozwcp/admpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>$cm,'node'=>'node'.$node)),),)));
		if ($result=='OK') break;
		sleep(1);
	}
	return $result;
}
function controllerBusy($retries){global $domoticzurl;for($k=1;$k<=$retries;$k++){$result=file_get_contents($domoticzurl.'/ozwcp/poll.xml');$p=xml_parser_create();xml_parse_into_struct($p,$result,$vals,$index);xml_parser_free($p);foreach($vals as $val){if($val['tag']=='ADMIN'){$result=$val['attributes']['ACTIVE'];break;}}if($result=='false'){break;}if($k==$retries){zwaveCommand(1,'Cancel');break;}sleep(1);}}
function convertToHours($time) {
	if ($time<600) return substr(strftime('%M:%S', $time-3600), 1);
	elseif ($time>=600&&$time<3600) return strftime('%M:%S', $time-3600);
	else return strftime('%k:%M:%S', $time-3600);
}
function checkport($ip,$port='None') {
	if ($port=='None') {
		if (ping($ip)) {
			$prevcheck=$d['ping'.$ip]['s'];
			if ($prevcheck>=5) telegram($ip.' online', true);
			if ($prevcheck>0) store('ping'.$ip, 0, basename(__FILE__).':'.__LINE__);
			return 1;
		} else {
			$check=$d['ping'.$ip]['s']+1;
			if ($check>0) store('ping'.$ip, $check, basename(__FILE__).':'.__LINE__);
			if ($check==5) telegram($ip.' Offline', true);
			if ($check%120==0) telegram($ip.' nog steeds Offline', true);
			return 0;
		}
	} else {
		if (pingport($ip, $port)==1) {
			$prevcheck=$d['ping'.$ip]['s'];
			if ($prevcheck>=5) telegram($ip.':'.$port.' online', true);
			if ($prevcheck>0) store('ping'.$ip, 0, basename(__FILE__).':'.__LINE__);
			return 1;
		} else {
			$check=$d['ping'.$ip]['s']+1;
			if ($check>0) store('ping'.$ip, $check, basename(__FILE__).':'.__LINE__);
			if ($check==5) telegram($ip.':'.$port.' Offline', true);
			if ($check%120==0) telegram($ip.':'.$port.' nog steeds Offline', true);
			return 0;
		}
	}
}
function ping($ip) {
	$result=exec("/bin/ping -c1 -w2 $ip", $outcome, $reply);
	if ($reply==0) return true;
	else return false;
}
function pingport($ip,$port) {
	$file=@fsockopen($ip, $port, $errno, $errstr, 2);
	$reply=0;
	if (!$file) $reply=-1;
	else {
		fclose($file);
		$reply=1;
	}
	return $reply;
}
function double($name, $action, $msg='') {
	sw($name, $action, $msg);
	usleep(2000000);
	sw($name, $action, $msg);
}

function rookmelder($msg) {
	global $device;
	resetsecurity();
//	global $d,$device;
	alert($device,	$msg,	300, false, 2, true);
//	if ($d['Weg']['s']<=1) {

//		foreach (array(/*'Ralex',*/'RkamerL','RkeukenL','RkamerR','Rspeelkamer','Rliving','RkeukenR','Rbureel') as $i) {
//			if ($d[$i]['s']>0) sl($i, 1, basename(__FILE__).':'.__LINE__);
//		}
//		if ($d['zon']['s']<200) {
//			foreach (array('hall','inkom','kamer','speelkamer',/*'alex',*/'eettafel','zithoek','lichtbadkamer','wasbak','terras') as $i) {
//				if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
//			}
//			foreach (array('snijplank','garage','lamp kast','bureel', 'tuin') as $i) {
//				if ($d[$i]['s']!='On') sw($i, 'On', basename(__FILE__).':'.__LINE__);
//			}
//		}
//		boseplayinfo($msg, 45);
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
	lg('Telegram sent: '.$msg);
}
function lg($msg) {
	global $log;
	if ($log==true) {
		$fp=fopen('/temp/domoticz.log', "a+");
		$time=microtime(true);
		$dFormat="Y-m-d H:i:s";
		$mSecs=$time-floor($time);
		$mSecs=substr(number_format($mSecs, 3), 1);
		fwrite($fp, sprintf("%s%s %s\n", date($dFormat), $mSecs, $msg));
		fclose($fp);
	}
}
function logwrite($msg,$msg2=null) {
	global $log;
	if ($log==true) {
		$time=microtime(true);
		$dFormat="Y-m-d H:i:s";
		$mSecs=$time-floor($time);
		$mSecs=substr(number_format($mSecs, 3), 1);
		$fp=fopen('/temp/domoticz.log', "a+");
		fwrite($fp,sprintf("%s%s %s %s\n",date($dFormat),$mSecs,' > '.$msg,$msg2));
		fclose($fp);
	}
}
function fail2ban($ip) {
	$time=microtime(true);
	$dFormat="Y-m-d H:i:s";
	$mSecs=$time-floor($time);
	$mSecs=substr(number_format($mSecs, 3), 1);
	$fp=fopen('/temp/home2ban.log', "a+");
	fwrite($fp, sprintf("%s %s\n", date($dFormat), $ip));
	fclose($fp);
}
function startsWith($haystack,$needle) {
	return $needle===""||strrpos($haystack, $needle, -strlen($haystack))!==false;
}
function endswith($string,$test) {
	$strlen=strlen($string);$testlen=strlen($test);
	if ($testlen>$strlen) return false;
	return substr_compare($string, $test, $strlen-$testlen, $testlen)===0;
}
function bosekey($key,$sleep=75000,$ip=101) {
	global $d;
	if (!is_array($d)) $d=fetchdata();
	lg('bosekey '.$key);
	$xml="<key state=\"press\" sender=\"Gabbo\">$key</key>";
	bosepost("key", $xml, $ip, true);
	$xml="<key state=\"release\" sender=\"Gabbo\">$key</key>";
	bosepost("key", $xml, $ip);
	if ($sleep>0) usleep($sleep);
	if (startsWith($key,'PRESET')&&$ip!=102) {
		for ($x=1;$x<=10;$x++) {
			$data=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.$ip:8090/now_playing"))), true);
			lg('Bosekey '.$key.' '.$ip.' '.$x.' data='.print_r($data, true));
			if (isset($data)) {
				if (isset($data['shuffleSetting'])&&$data['shuffleSetting']!='SHUFFLE_ON') bosekey('SHUFFLE_ON', 750000, $ip);
				if (isset($data['playStatus'])&&$data['playStatus']=='PLAY_STATE'&&isset($data['artist'])&&($data['artist']!='Paul Kalkbrenner'&&$data['artist']!='Florian Appl'&&$data['track']!='Cloud Rider'&&$data['track']!='Sky and Sand'&&$data['track']!='Seaside')) {
					break;
				} elseif (isset($data['playStatus'])&&$data['playStatus']=='PLAY_STATE') bosekey('NEXT_TRACK', 750000, $ip);
			}
			sleep(2);
		}
	}
/*	if ($key=='POWER') {
		if ($ip==101) {
			if ($d['bose101']['s']=='On') sw('Bose Living', 'Off', basename(__FILE__).':'.__LINE__);
			else sl('Bose Living', 17, basename(__FILE__).':'.__LINE__);
		} elseif ($ip==105) {
			if ($d['bose105']['s']=='On') sw('Bose Keuken', 'Off', basename(__FILE__).':'.__LINE__);
			else sl('Bose Keuken', 17, basename(__FILE__).':'.__LINE__);
		}
	}*/
}
function bosevolume($vol,$ip=101, $msg='') {
	$vol=1*$vol;
	$xml="<volume>$vol</volume>";
	bosepost("volume", $xml, $ip, true);
	if ($ip==101) {
		if ($vol>50) bosebass(-2, $ip);
		elseif ($vol>40) bosebass(-3, $ip);
		elseif ($vol>30) bosebass(-4, $ip);
		elseif ($vol>20) bosebass(-5, $ip);
		elseif ($vol>10) bosebass(-6, $ip);
		else bosebass(-7, $ip);
	}
	lg('bosevolume '.$ip.' -> '.$vol.' '.$msg);
}
function bosebass($bass,$ip=101) {
	$bass=1*$bass;
	$xml="<bass>$bass</bass>";
	bosepost("bass", $xml, $ip);
}
function bosepreset($pre,$ip=101) {
	$pre=1*$pre;
	if ($pre<1||$pre>6) return;
	bosekey("PRESET_$pre", 0, $ip, true);
}
function bosezone($ip,$forced=false,$vol='') {
	global $d;
	if (!is_array($d)) $d=fetchdata();
	$preset='PRESET_5';
	if (($d['Weg']['s']<=1&&$d['bose101']['m']==1)||$forced===true) {
		if ($d['Weg']['s']==0&&($d['lgtv']['s']=='Off'||$forced===true)&&$d['bose101']['s']=='Off'&&TIME<strtotime('21:00')) {
			bosekey("POWER", 1500000, 101);
			sw('bose101', 'On', basename(__FILE__).':'.__LINE__);
			bosekey($preset, 750000, 101);
			if ($d['lgtv']['s']=='On'&&$d['eettafel']['s']==0) bosevolume(0, 101, basename(__FILE__).':'.__LINE__);
			else bosevolume(17, 101, basename(__FILE__).':'.__LINE__);
		}
		if ($ip>101) {
			if ($d['bose'.$ip]['s']!='On') sw('bose'.$ip, 'On', basename(__FILE__).':'.__LINE__);
			    if ($ip==102) $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.102">304511BC3CA5</member></zone>';
			elseif ($ip==103) $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.103">C4F312F65070</member></zone>';
			elseif ($ip==104) $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.104">C4F312DCE637</member></zone>';
			elseif ($ip==105) $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.105">587A628BB5C0</member></zone>';
			elseif ($ip==106) $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.106">C4F312F89670</member></zone>';
			elseif ($ip==107) $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.107">B0D5CC065C20</member></zone>';
			if ($d['bose101']['s']=='Off'&&$d['bose'.$ip]['s']=='Off') {
				bosekey("POWER", 1500000, 101);
				sw('bose101', 'On', basename(__FILE__).':'.__LINE__);
				bosekey($preset, 750000, 101);
				if ($d['lgtv']['s']=='On'&&$d['eettafel']['s']==0) bosevolume(0, 101, basename(__FILE__).':'.__LINE__);
				else bosevolume(21, 101, basename(__FILE__).':'.__LINE__);
				usleep(100000);
				bosepost('setZone', $xml, 101);
				if ($vol=='') {
					if (TIME>strtotime('6:00')&&TIME<strtotime('20:00')) bosevolume(30, $ip, basename(__FILE__).':'.__LINE__);
					else bosevolume(22, $ip, basename(__FILE__).':'.__LINE__);
				} else bosevolume($vol, $ip, basename(__FILE__).':'.__LINE__);
			} elseif ($d['bose'.$ip]['s']=='Off') {
				bosepost('setZone', $xml, 101);
				store('bose'.$ip, 'On');
				lg($xml);
				if ($vol=='') {
					if (TIME>strtotime('6:00')&&TIME<strtotime('21:00')) bosevolume(30, $ip, basename(__FILE__).':'.__LINE__);
					else bosevolume(20, $ip, basename(__FILE__).':'.__LINE__);
				} else bosevolume($vol, $ip, basename(__FILE__).':'.__LINE__);
			}
		}
	}
}
function bosepost($method,$xml,$ip=101,$log=false) {
	global $user;
	for($x=1;$x<=100;$x++) {
		$ch=curl_init("http://192.168.2.$ip:8090/$method");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		$response=curl_exec($ch);
		curl_close($ch);
		if ($response=='<?xml version="1.0" encoding="UTF-8" ?><status>/'.$method.'</status>') break;
		usleep(100000);
	}
	return $response;
}
function strafter($string, $substring) {
	$pos=strpos($string, $substring);
	if ($pos===false) return '';
	else return(substr($string, $pos+strlen($substring)));
}
function strbefore($string, $substring) {
	$pos=strpos($string, $substring);
	if ($pos===false) return '';
	else return(substr($string, 0, $pos));
}
function fliving() {
	global $d;
	if ($d['Weg']['s']==0&&$d['lgtv']['s']=='Off'&&$d['bureel']['s']=='Off'&&$d['eettafel']['s']==0) {
		$zonop=($d['civil_twilight']['s']+$d['Sun']['s'])/2;
		$zononder=($d['civil_twilight']['m']+$d['Sun']['m'])/2;
		if ($d['zon']['s']==0&&(TIME<$zonop||TIME>$zononder)) {
			if ($d['snijplank']['s']==0&&TIME<strtotime('21:30')) sl('snijplank', 15, basename(__FILE__).':'.__LINE__);
			if ($d['bureel']['s']=='Off'&&$d['snijplank']['s']=='Off'&&TIME<strtotime('21:30')) sw('bureel', 'On', basename(__FILE__).':'.__LINE__);
		}
		if (TIME>=strtotime('5:30')&&TIME<strtotime('17:30')) bosezone(101);
		apcu_store('living', TIME);
	}

}
function fgarage() {
	global $d;
	if ($d['Weg']['s']==0&&($d['zon']['s']<=50||TIME<strtotime('7:00')||TIME>strtotime('22:00'))&&$d['garage']['s']=='Off'&&$d['garageled']['s']=='Off') sw('garageled', 'On', basename(__FILE__).':'.__LINE__);
}
function fbadkamer() {
	global $d;
	if (past('$ 8badkamer-8')>10) {
		if ($d['lichtbadkamer']['s']<16&&$d['zon']['s']==0) {
			if (TIME>strtotime('5:30')&&TIME<strtotime('21:30')) sl('lichtbadkamer', 16, basename(__FILE__).':'.__LINE__);
			elseif ($d['lichtbadkamer']['s']<8) sl('lichtbadkamer', 8, basename(__FILE__).':'.__LINE__);
		}
	}
}
function fkeuken() {
	global $d;
	if (TIME>=strtotime('6:30')&&TIME<strtotime('19:30')&&$d['Weg']['s']==0&&$d['wasbak']['s']<6&&$d['snijplank']['s']==0&&(($d['zon']['s']==0&&TIME<$d['Sun']['s']||TIME>$d['Sun']['m'])||($d['RkeukenL']['s']>70&&$d['RkeukenR']['s']>70))) {
		sl('wasbak', 7, basename(__FILE__).':'.__LINE__);
	} elseif ((TIME<strtotime('6:30')||TIME>=strtotime('19:30'))&&$d['Weg']['s']==0&&$d['wasbak']['s']==0&&$d['snijplank']['s']<15&&($d['zon']['s']==0||($d['RkeukenL']['s']>70&&$d['RkeukenR']['s']>70))) {
		sl('snijplank', 15, basename(__FILE__).':'.__LINE__);
	}
}
function finkom() {
	global $d;
	if ($d['Weg']['s']==0&&$d['inkom']['s']<28&&$d['zon']['s']==0&&(TIME<$d['Sun']['s']||TIME>$d['Sun']['m'])) sl('inkom', 28, basename(__FILE__).':'.__LINE__);
}
function fhall() {
	global $d,$device;
	if (TIME>=strtotime('7:30')&&TIME<=strtotime('21:00')&&(TIME<$d['Sun']['s']||TIME>$d['Sun']['m'])&&($d['Ralex']['s']==0||TIME<=strtotime('19:45'))) {
		if ($d['hall']['s']<28&&$d['Weg']['s']==0&&$d['zon']['s']==0) {
			sl('hall', 28, basename(__FILE__).':'.__LINE__);
//			sleep(1);
//			sl('hall', 27, basename(__FILE__).':'.__LINE__);
		}
	} else finkom();
	if (TIME>=strtotime('21:30')&&$d['kamer']['s']==0&&$d['Weg']['s']==0&&$d['deurkamer']['s']=='Open') sl('kamer', 1, basename(__FILE__).':'.__LINE__);
}
function sirene($msg) {
	global $d,$device,$status;
	if ($d['Weg']['s']==0) return false;
	elseif (isset($status)&&($status=='On'||$status=='Open')&&$device!=$d['Weg']['icon']) {
		if (in_array($device, array('pirhall', 'deuralex', 'deurkamer', 'deurspeelkamer', 'deurkamer', 'deurbadkamer', 'raamhall', 'raamkamer', 'raamspeelkamer', 'raamalex'))) {
			if ($d['Weg']['s']>=2) {
				sw('sirene', 'On', basename(__FILE__).':'.__LINE__);
				telegram($msg.' om '.strftime("%k:%M:%S", TIME), false, 2);
				storeicon('Weg', $device, basename(__FILE__).':'.__LINE__);
			}
		} else {
			if ($d['Weg']['s']>=1) {
				sw('sirene', 'On', basename(__FILE__).':'.__LINE__);
				telegram($msg.' om '.strftime("%k:%M:%S", TIME), false, 2);
				storeicon('Weg', $device, basename(__FILE__).':'.__LINE__);
			}
		}
	}
}
function createheader($page='') {
	global $udevice,$ipaddress;
	echo '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
		<title>Floorplan</title>';
	if ($ipaddress=='192.168.2.202')  { //Aarde
		echo '
		<meta name="HandheldFriendly" content="true">
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=0.768,user-scalable=yes,minimal-ui">';
	} elseif ($ipaddress=='192.168.2.201')  { //Nero
		echo '
		<meta name="HandheldFriendly" content="true">
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.265,user-scalable=yes,minimal-ui">';
	} elseif ($udevice=='iPhone') {
		echo '
		<meta name="HandheldFriendly" content="true">
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=0.755,user-scalable=yes,minimal-ui">';
	} else {
		echo '
		<meta name="HandheldFriendly" content="true">
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,minimal-ui">';
	}
	echo '
		<link rel="manifest" href="/manifest.json">
		<link rel="shortcut icon" href="images/domoticzphp48.png">
		<link rel="apple-touch-icon" href="images/domoticzphp48.png">
		<link rel="apple-touch-startup-image" href="images/domoticzphp144.png">
		<link rel="stylesheet" type="text/css" href="/styles/floorplan.css">
		<script type="text/javascript" src="/scripts/m4q.min.js"></script>
		<script type="text/javascript" src="/scripts/floorplanjs.js?v=5"></script>';
	if ($page!='') {
		echo '
		<script type=\'text/javascript\'>
			$(document).ready(function(){initview();});
		</script>';
	}
	echo '
	</head>';
}
function daikinstatus($device) {
	if ($device=='living') $ip=111;
	elseif ($device=='kamer') $ip=112;
	elseif ($device=='alex') $ip=113;
	$ctx=stream_context_create(array('http'=>array('timeout' =>2)));
	$data = @file_get_contents("http://192.168.2.$ip/aircon/get_control_info", false, $ctx);
	if($data === FALSE) return FALSE;
	else {
		$array=explode(",",$data);
		$ci=array();
		foreach ($array as $value){
			$pair= explode("=",$value);
			if ($pair[0]=='pow') $ci['power']=$pair[1];
			elseif ($pair[0]=='mode') $ci['mode']=$pair[1];
			elseif ($pair[0]=='adv') $ci['adv']=$pair[1];
			elseif ($pair[0]=='stemp') $ci['set']=$pair[1];
			elseif ($pair[0]=='f_rate') $ci['fan']=$pair[1];
		}
		return json_encode($ci);
	}
}
function daikinset($device, $power, $mode, $stemp,$msg='', $fan='A', $spmode=-1, $maxpow=40) {
	global $d;
	if ($device=='living') $ip=111;
	elseif ($device=='kamer') $ip=112;
	elseif ($device=='alex') $ip=113;
	storeicon('daikinliving', $maxpow);
	$url="http://192.168.2.$ip/aircon/set_control_info?pow=$power&mode=$mode&stemp=$stemp&f_rate=$fan&shum=0&f_dir=0";
	file_get_contents($url);
	sleep(1);
	$status=daikinstatus($device);
	if ($d['daikin'.$device]['s']!=$status) store('daikin'.$device, $status);
	if ($power==0&&$d['daikin'.$device]['m']!=0) storemode('daikin'.$device, 0, basename(__FILE__).':'.__LINE__.':'.$msg);
	elseif ($d['daikin'.$device]['s']!=$mode) storemode('daikin'.$device, $mode, basename(__FILE__).':'.__LINE__.':'.$msg);
	sleep(1);
	if ($spmode==-1) file_get_contents('http://192.168.2.'.$ip.'/aircon/set_special_mode?set_spmode=1&spmode_kind=2'); // Eco
	elseif ($spmode==0) file_get_contents('http://192.168.2.'.$ip.'/aircon/set_special_mode?set_spmode=0&spmode_kind=1'); // Normal
	elseif ($spmode==1) file_get_contents('http://192.168.2.'.$ip.'/aircon/set_special_mode?set_spmode=1&spmode_kind=1'); // Power
	sleep(1);
	foreach(array(111, 112, 113) as $ip) {
		if ($maxpow==100) $url='http://192.168.2.'.$ip.'/aircon/set_demand_control?type=1&en_demand=0&mode=0&max_pow=100&scdl_per_day=0&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0';
		else $url='http://192.168.2.'.$ip.'/aircon/set_demand_control?type=1&en_demand=1&mode=0&max_pow='.$maxpow.'&scdl_per_day=0&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0';
		file_get_contents($url);
		usleep(100000);
	}
}
function RefreshZwave($node){
	$devices=json_decode(file_get_contents('http://127.0.0.1:8080/json.htm?type=openzwavenodes&idx=3',false),true);
	foreach($devices['result'] as $devozw)
		if($devozw['NodeID']==$node){
			$device=$devozw['Description'].' '.$devozw['Name'];
			break;
		}
	if(!isset($device))exit;
	for($k=1;$k<=5;$k++){
		$result=file_get_contents('http://127.0.0.1:8080/ozwcp/refreshpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>'racp','node'=>$node)),),)));
		if($result==='OK')break;
		sleep(1);
	}
}
function human_filesize($bytes,$dec=2){
	$size=array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
	$factor=floor((strlen($bytes)-1)/3);
	return sprintf("%.{$dec}f",$bytes/pow(1024,$factor)).@$size[$factor];
}
function setradiator($name,$dif,$koudst=false,$set=14) {
	if ($koudst==true) $setpoint=28;
	else $setpoint=$set-ceil($dif*4);
	if ($setpoint>28) $setpoint=28;
	elseif ($setpoint<4) $setpoint=4;
	return round($setpoint, 0);
}
function zwaveNodeNeighbourUpdate($node) {
	global $domoticzurl;
	for ($k=1;$k<=5;$k++) {
		sleep(1);
		$result=file_get_contents($domoticzurl.'/ozwcp/admpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>'reqnnu','node'=>'node'.$node))))));
		if ($result=='OK') break;
		sleep(1);
	}
	return $result;
}
function zwaveRefreshNode($node) {
	global $domoticzurl;
	for ($k=1;$k<=5;$k++) {
		sleep(1);
		$result=file_get_contents($domoticzurl.'/ozwcp/admpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>'refreshnode','node'=>'node'.$node))))));
		if ($result=='OK') break;
		sleep(1);
	}
	return $result;
}
function zwaveHasnodefailed($node) {
	global $domoticzurl;
	for ($k=1;$k<=5;$k++) {
		sleep(1);
		$result=file_get_contents($domoticzurl.'/ozwcp/admpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>'hnf','node'=>'node'.$node))	))));
		if ($result=='OK') break;
		sleep(1);
	}
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
