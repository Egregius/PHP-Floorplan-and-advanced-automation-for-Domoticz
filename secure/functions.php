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
//$d=fetchdata();
/**
 * Function fetchdata
 *
 * Fetches all the data from the devices table
 *
 * @return array $d
 */
function fetchdata() {
	global $db;
	if(!isset($db)) $db=dbconnect();
	$stmt=$db->query("select n,i,s,t,m,dt,icon from devices;");
	while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) $d[$row['n']] = $row;
	return $d;
}
/**
 * Function huisslapen
 *
 * Switches off everything that should be off while sleeping
 *
 * @return null
 */
function huisslapen() {
	global $d,$boseipbuiten;
	sl(array('hall','inkom','eettafel','zithoek','wasbak','terras','ledluifel'), 0, basename(__FILE__).':'.__LINE__);
	sw(array('garageled','garage','pirgarage','pirkeuken','pirliving','pirinkom','pirhall','kristal','bureel','jbl','tuin','keuken','zolderg','voordeur','wc','dampkap','GroheRed','kookplaatpower','nvidia'), 'Off', basename(__FILE__).':'.__LINE__);
	foreach (array('living_set','speelkamer_set','alex_set','kamer_set','badkamer_set','eettafel','zithoek','luifel') as $i) {
		if ($d[$i]['m']!=0) storemode($i, 0, basename(__FILE__).':'.__LINE__);
	}
	$data=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.101:8090/now_playing'))), true);
	if (!empty($data)) {
		if (isset($data['@attributes']['source'])) {
			if ($data['@attributes']['source']!='STANDBY') {
				bosekey("POWER", 0, 101);
				foreach (array(101,102,103,104,105) as $x) {
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
	if ($d['buiten_temp']['s']<4&&$d['heating']['s']<4) store('heating', 4, basename(__FILE__).':'.__LINE__);
	elseif ($d['buiten_temp']['s']>10&&$d['heating']['s']>1) store('heating', 1, basename(__FILE__).':'.__LINE__);
	if ($d['auto']['s']=='Off') sw('auto', 'On', basename(__FILE__).':'.__LINE__);
}
/**
 * Function douche
 *
 * Calculates the gas and water consumption of the shower, sents a telegram
 * and resets the gas and water counters
 *
 * @return null
 */
function douche() {
	global $d;
	$douchegas=$d['douche']['s']*10;
	$douchewater=$d['douche']['m']*1;
	if ($douchegas>=1&&$douchewater>=1) {
		$msg='Douche__Gas: '.$douchegas.'L = '.number_format($douchegas*0.005, 2, ',', '').'€__Water: '.$douchewater.'L = '.number_format($douchewater*0.0018529, 2, ',', '').'€__Som = '.number_format(($douchegas*0.005)+($douchewater*0.0018529), 2, ',', '').'€';
		telegram($msg);
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
/**
 * Function waarschuwing
 *
 * Plays a sound on the Xiami doorbell and a regular doorbell
 * Says the message on the Bose Soundtouch speakers
 * and sents a telegram message
 *
 * @param string $msg Message to sent to telegram
 *
 * @return null
 */
function waarschuwing($msg) {
	global $d;
	if ($d['bose101']['s']=='On') boseplayinfo($msg, 50);
//	if ($d['bose102']['s']=='On') shell_exec('curl -s "http://127.0.0.1/secure/pass2php/belknopbose102.php" > /dev/null 2>/dev/null &');
//	if ($d['bose103']['s']=='On') shell_exec('curl -s "http://127.0.0.1/secure/pass2php/belknopbose103.php" > /dev/null 2>/dev/null &');
//	if ($d['bose104']['s']=='On') shell_exec('curl -s "http://127.0.0.1/secure/pass2php/belknopbose104.php" > /dev/null 2>/dev/null &');
//	if ($d['bose105']['s']=='On') shell_exec('curl -s "http://127.0.0.1/secure/pass2php/belknopbose105.php" > /dev/null 2>/dev/null &');
	if ($d['Xvol']['s']!=25) sl('Xvol', 25, basename(__FILE__).':'.__LINE__);
	sl('Xring', 30, basename(__FILE__).':'.__LINE__);
	sw('deurbel', 'On', basename(__FILE__).':'.__LINE__);
	telegram($msg, false, 2);
	usleep(1500000);
	sl('Xring', 0, basename(__FILE__).':'.__LINE__);
	die($msg);
}
/**
 * Function past
 *
 * Calculates how long it's ago that this device was updated
 *
 * @param string $name Name of the device to check
 *
 * @return int
 */
function past($name) {
	global $d;
	if (!empty($d[$name]['t'])) return TIME-$d[$name]['t'];
	else return 999999999;
}

function idx($name) {
	global $d;
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
			if ($d[$name]['s']!=$action||$name=='deurbel') echo file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx='.$d[$name]['i'].'&switchcmd='.$action);
		} else store($name, $action, $msg);
		if ($name=='denon') {
			if ($action=='Off') storemode('denon', 'UIT', basename(__FILE__).':'.__LINE__);
		} /*else {
  			if (in_array($name, array('brander','badkamervuur1','badkamervuur2','regenpomp','zoldervuur1','zoldervuur2','daikin'))) {
  				$stamp=TIME;
  				if (!isset($db)) $db=dbconnect();
  				$db->query("INSERT INTO ontime (device,stamp,status) VALUES ('$name','$stamp','$action');");
  			}
		}*/
	}
}
function fvolume($cmd) {
	global $d;
	if (!isset($d)) $d=fetchdata();
//	lg('fvolume '.$cmd);
	if ($cmd=='down') {
		if ($d['denon']['s']=='On'&&$d['denonpower']['s']=='ON') {
			denon('MVDOWN');
			denon('MVDOWN');
			denon('MVDOWN');
			denon('MVDOWN');
			denon('MVDOWN');
			denon('MVDOWN');
		} elseif ($d['tv']['s']=='On'&&$d['lgtv']['s']=='On') {
			exec('/var/www/html/secure/lgtv.py -c volume-down 192.168.2.27');
			exec('/var/www/html/secure/lgtv.py -c volume-down 192.168.2.27');
		} elseif ($d['bose101']['s']=='On') {
			$nowplaying=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.101:8090/now_playing'))), true);
			if (!empty($nowplaying)) {
				if (isset($nowplaying['@attributes']['source'])) {
					if ($nowplaying['@attributes']['source']!='STANDBY') {
						$volume=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.101:8090/volume'))), true);
						$cv=$volume['actualvolume'];
						if ($cv>50) bosevolume($cv-5);
						elseif ($cv>30) bosevolume($cv-4);
						elseif ($cv>20) bosevolume($cv-3);
						elseif ($cv>10) bosevolume($cv-2);
						else bosevolume($cv-1);
					}
				}
			}
		}
	} elseif ($cmd=='up') {
		if ($d['denon']['s']=='On'&&$d['denonpower']['s']=='ON') {
			denon('MVUP');
			denon('MVUP');
			denon('MVUP');
			denon('MVUP');
			denon('MVUP');
			denon('MVUP');
		} elseif ($d['tv']['s']=='On'&&$d['lgtv']['s']=='On') {
			exec('/var/www/html/secure/lgtv.py -c volume-up 192.168.2.27');
			exec('/var/www/html/secure/lgtv.py -c volume-up 192.168.2.27');
		} elseif ($d['bose101']['s']=='On') {
			$nowplaying=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.101:8090/now_playing'))), true);
			if (!empty($nowplaying)) {
				if (isset($nowplaying['@attributes']['source'])) {
					if ($nowplaying['@attributes']['source']!='STANDBY') {
						$volume=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.101:8090/volume'))), true);
						$cv=$volume['actualvolume'];
						if ($cv>80) exit;
						elseif ($cv>50) bosevolume($cv+5);
						elseif ($cv>30) bosevolume($cv+4);
						elseif ($cv>20) bosevolume($cv+3);
						elseif ($cv>10) bosevolume($cv+2);
						else bosevolume($cv+1);
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
	if(!isset($db)) $db=dbconnect();
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
function ud($name,$nvalue,$svalue,$check=false,$smg='') {
	global $user,$d,$domoticzurl;
	if ($d[$name]['i']>0) {
		if ($check==true) {
			if ($d[$name]['s']!=$svalue) {
				lg($domoticzurl.'/json.htm?type=command&param=udevice&idx='.$d[$name]['i'].'&nvalue='.$nvalue.'&svalue='.$svalue);
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
	if ($time<600) return substr(strftime('%k:%M:%S', $time-3600), 1);
	elseif ($time>=600&&$time<3600) return strftime('%k:%M:%S', $time-3600);
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
	$result=exec("/bin/ping -c1 -w1 $ip", $outcome, $reply);
	if ($reply==0) return true;
	else return false;
}
function pingport($ip,$port) {
	$file=@fsockopen($ip, $port, $errno, $errstr, 5);
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
	global $d;
	if ($d['Weg']['s']<=1) {
		alert($device,	$msg,	300, false, 2, true);
		foreach (array(/*'Ralex',*/'Rspeelkamer','RkamerL','RkeukenL','RkamerR','Rliving','RkeukenR','Rbureel') as $i) {
			if ($d[$i]['s']>0) sl($i, 0, basename(__FILE__).':'.__LINE__);
		}
		if ($d['zon']['s']<500) {
			foreach (array('hall','inkom','kamer','speelkamer',/*'alex',*/'eettafel','zithoek','lichtbadkamer', 'terras') as $i) {
				if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
			foreach (array('keuken','garage','jbl','bureel', 'tuin') as $i) {
				if ($d[$i]['s']!='On') sw($i, 'On', basename(__FILE__).':'.__LINE__);
			}
		}
		bosezone(101, true);
		bosezone(102, true);
		bosezone(103, true);
		bosezone(104, true);
		boseplayinfo($msg, 45);
		sleep(5);
		boseplayinfo($msg, 45);
	}
	resetsecurity();
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
	lg('bosekey '.$key);
	$xml="<key state=\"press\" sender=\"Gabbo\">$key</key>";
	bosepost("key", $xml, $ip, true);
	$xml="<key state=\"release\" sender=\"Gabbo\">$key</key>";
	bosepost("key", $xml, $ip);
	if ($sleep>0) usleep($sleep);
	if (startsWith($key,'PRESET')) {
		for ($x=1;$x<=30;$x++) {
			$data=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.$ip:8090/now_playing"))), true);
			lg('Bosekey '.$key.' '.$ip.' '.$x.' data='.print_r($data, true));
			if (isset($data)) {
				if (isset($data['shuffleSetting'])&&$data['shuffleSetting']!='SHUFFLE_ON') bosekey('SHUFFLE_ON', 750000, $ip);
				if (isset($data['playStatus'])&&$data['playStatus']=='PLAY_STATE'&&isset($data['artist'])&&($data['artist']!='Paul Kalkbrenner'&&$data['artist']!='Florian Appl'&&$data['track']!='Cloud Rider'&&$data['track']!='Sky and Sand'&&$data['track']!='Burg Hohenzollern')) {
					break;
				} elseif (isset($data['playStatus'])&&$data['playStatus']=='PLAY_STATE') bosekey('NEXT_TRACK', 750000, $ip);
			}
			sleep(1);
		}
	}
}
function bosevolume($vol,$ip=101, $msg='') {
	$vol=1*$vol;
	$xml="<volume>$vol</volume>";
	bosepost("volume", $xml, $ip, true);
	if ($ip==101) {
		if ($vol>50) bosebass(0, $ip);
		elseif ($vol>40) bosebass(-1, $ip);
		elseif ($vol>30) bosebass(-2, $ip);
		elseif ($vol>20) bosebass(-3, $ip);
		elseif ($vol>10) bosebass(-4, $ip);
		else bosebass(-5, $ip);
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
	$d=fetchdata();
	if (TIME>strtotime('21:00')) $preset='PRESET_1';
	else  $preset='PRESET_2';
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
function denon($cmd) {
	for ($x=1;$x<=10;$x++) {
		if (denontcp($cmd, $x)) break;
	}
}
function denontcp($cmd, $x) {
	$sleep=102000*$x;
	$socket=fsockopen("192.168.2.6", "23", $errno, $errstr, 2);
	if ($socket) {
		fputs($socket, "$cmd\r\n");
		fclose($socket);
		usleep($sleep);
		return true;
	} else {
		usleep($sleep);
		echo 'sleeping '.$sleep.'<br>';
		return false;
	}
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
			if ($d['wasbak']['s']==0&&TIME<strtotime('21:30')) sl('wasbak', 3, basename(__FILE__).':'.__LINE__);
			if ($d['bureel']['s']=='Off'&&$d['keuken']['s']=='Off'&&TIME<strtotime('21:30')) sw('bureel', 'On', basename(__FILE__).':'.__LINE__);
//			if ($d['jbl']['s']=='Off'&&$d['keuken']['s']=='Off'&&TIME<strtotime('21:30')) sw('jbl', 'On', basename(__FILE__).':'.__LINE__);
		}
		if (TIME>=strtotime('5:30')&&TIME<strtotime('17:30')) bosezone(101);
		apcu_store('living', TIME);
	}

}
function fgarage() {
	global $d;
	if ($d['Weg']['s']==0&&($d['zon']['s']<=50||TIME<strtotime('7:00')||TIME>strtotime('22:00'))&&$d['garage']['s']=='Off'&&$d['garageled']['s']=='Off') sw('garageled', 'On', basename(__FILE__).':'.__LINE__);
	if (TIME>=strtotime('5:30')&&TIME<strtotime('21:30')) bosezone(104);
}
function fbadkamer() {
	global $d;
	if (past('$ 8badkamer-8')>10) {
		if ($d['lichtbadkamer']['s']<16&&$d['zon']['s']==0) {
			if (TIME>strtotime('5:30')&&TIME<strtotime('21:30')) sl('lichtbadkamer', 16, basename(__FILE__).':'.__LINE__);
			elseif ($d['lichtbadkamer']['s']<8) sl('lichtbadkamer', 8, basename(__FILE__).':'.__LINE__);
		}
//		if (TIME>strtotime('5:30')&&TIME<strtotime('8:00')) {
//			if ($d['bose102']['s']=='Off'&&past('bose102')>30) bosezone(102);
//		}
	}
}
function fkeuken() {
	global $d;
	if (TIME>=strtotime('6:30')&&TIME<strtotime('20:00')&&$d['Weg']['s']==0&&$d['wasbak']['s']<14&&(($d['zon']['s']==0&&TIME<$d['Sun']['s']||TIME>$d['Sun']['m'])||($d['RkeukenL']['s']>70&&$d['RkeukenR']['s']>70))) {
		sl('wasbak', 14, basename(__FILE__).':'.__LINE__);
	} elseif ((TIME<strtotime('6:30')||TIME>=strtotime('20:00'))&&$d['Weg']['s']==0&&$d['wasbak']['s']<3&&($d['zon']['s']==0||($d['RkeukenL']['s']>70&&$d['RkeukenR']['s']>70))) {
		sl('wasbak', 3, basename(__FILE__).':'.__LINE__);
	}
//	if ($d['Weg']['s']==0&&$d['lgtv']['s']=='Off'&&$d['bureel']['s']=='Off'&&$d['eettafel']['s']==0&&TIME>strtotime('6:30')&&TIME<strtotime('19:00')) {
//		if ($d['bose102']['s']=='Off'&&$d['lgtv']['s']=='Off') bosezone(102, false, 27);
//	}
}
function finkom() {
	global $d;
	if ($d['Weg']['s']==0&&$d['inkom']['s']<32&&TIME>strtotime('6:00')&&TIME<=strtotime('21:00')&&$d['zon']['s']==0&&(TIME<$d['Sun']['s']||TIME>$d['Sun']['m'])) sl('inkom', 32, basename(__FILE__).':'.__LINE__);
	elseif ($d['Weg']['s']==0&&$d['inkom']['s']<27&&$d['zon']['s']==0&&(TIME<$d['Sun']['s']||TIME>$d['Sun']['m'])) sl('inkom', 27, basename(__FILE__).':'.__LINE__);
}
function fhall() {
	global $d,$device;
	if (TIME>=strtotime('7:30')&&TIME<=strtotime('21:00')&&(TIME<$d['Sun']['s']||TIME>$d['Sun']['m'])&&($d['Ralex']['s']==0||TIME<=strtotime('19:45'))) {
		if ($d['hall']['s']<32) {
			if ($d['Weg']['s']==0&&TIME>strtotime('6:00')&&TIME<=strtotime('21:00')&&$d['zon']['s']==0) {
				if ($d['hall']['s']<32) {
					sl('hall', 32, basename(__FILE__).':'.__LINE__);
					sleep(1);
					sl('hall', 32, basename(__FILE__).':'.__LINE__);
				}
			} elseif ($d['Weg']['s']==0&&$d['zon']['s']==0&&$d['hall']['s']<27) {
				if ($d['hall']['s']<27) {
					sl('hall', 27, basename(__FILE__).':'.__LINE__);
					sleep(1);
					sl('hall', 27, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	} else finkom();
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
	if ($ipaddress=='192.168.2.198')  { //Aarde
		echo '
		<meta name="HandheldFriendly" content="true">
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=0.768,user-scalable=yes,minimal-ui">';
	} elseif ($ipaddress=='192.168.2.199')  { //Nero
		echo '
		<meta name="HandheldFriendly" content="true">
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.205,user-scalable=yes,minimal-ui">';
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
/**
 * Function daikinstatus
 *
 * Returns the status of a Daikin airco
 *
 * @param string $device devicename of the Daikin airco
 *
 * @return array();
 */
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
/**
 * Function daikinset
 *
 * Sets a Daikin airco in a mode and a temperature.
 *
 * @param string $device devicename of the Daikin airco
 * @param int $power 0 = Off, 1 = On
 * @param int $mode 0,1,7 = Auto, 2 = Dry, 3 = Cool, 4 = Heat, 6 = Fan only
 * @param float $temp Temperature of the setpoint
 * @param string $fan A = Auto, B = Silence, 3 = Level 1, 4 = Level 2, 5 = Level 3, 6 = Level 4, 7 = level 5
 * @param int $swing 0 = all wings stopped, 1 = vertical wings motion, 2 = horizontal wings motion, 3 = vertical and horizontal wings motion
 * @param int $hum
 *
 * @return array();
 */
function daikinset($device, $power, $mode, $stemp,$msg='', $fan='A', $swing=0, $hum=0) {
	if ($device=='living') $ip=111;
	elseif ($device=='kamer') $ip=112;
	elseif ($device=='alex') $ip=113;
	file_get_contents("http://192.168.2.$ip/aircon/set_control_info?pow=$power&mode=$mode&stemp=$stemp&f_rate=$fan&shum=$hum&f_dir=$swing");
	lg("Daikin $device pow=$power&mode=$mode&stemp=$stemp&f_rate=$fan&shum=$hum&f_dir=$swing ($msg)");
	sleep(1);
	store('daikin'.$device, daikinstatus($device));
	if ($power==0) storemode('daikin'.$device, 0, basename(__FILE__).':'.__LINE__.':'.$msg);
	else storemode('daikin'.$device, $mode, basename(__FILE__).':'.__LINE__.':'.$msg);
	usleep(100000);
	//file_get_contents('http://192.168.2.'.$ip.'/aircon/set_special_mode?set_spmode=1&spmode_kind=2');
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
/**
 * Function setradiator: calculates the setpoint for the Danfoss thermostat valve
 *
 * @param string  $name   Not used anymore
 * @param int	 $dif	Difference in temperature
 * @param boolean $koudst Is it the coldest room of all?
 * @param int	 $set	default setpoint
 *
 * @return null
 */
function setradiator($name,$dif,$koudst=false,$set=14) {
	if ($koudst==true) $setpoint=28;
	else $setpoint=$set-ceil($dif*4);
	if ($setpoint>28) $setpoint=28;
	elseif ($setpoint<4) $setpoint=4;
	return round($setpoint, 0);
}

/**
 * Function zwaveNodeNeighbourUpdate
 * Updates the neighbours of a node
 *
 * @param int $node idx of the node
 *
 * @return string result
 */
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
/**
 * Function zwaveRefreshNode
 * Refreshes the config of a node
 *
 * @param int $node idx of the node
 *
 * @return string result
 */
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

/**
 * Function zwaveHasnodefailed
 * Tries to revive a dead node
 *
 * @param int $node idx of the node
 *
 * @return null
 */
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
