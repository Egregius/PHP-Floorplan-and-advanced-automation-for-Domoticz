<?php
require '/var/www/config.php';
$dow=date("w");if($dow==0||$dow==6)$weekend=true; else $weekend=false;
$time=time();
$db=dbconnect();

$memcache = new Memcache;
$memcache->connect('192.168.2.21', 11211) or die ("Could not connect");
	
function fliving() {
	global $d,$time;
	$d=fetchdata();
	if ($d['Media']['s']=='Off'&&$d['lamp kast']['s']!='On'&&$d['eettafel']['s']==0) {
		if (($d['zon']['s']==0&&$d['dag']<3)||($d['RkeukenL']['s']>80&&$d['RkeukenR']['s']>80&&$d['Rbureel']['s']>80&&$d['Rliving']['s']>80)) {
			if ($d['wasbak']['s']==0&&$time<strtotime('21:30')) sl('wasbak', 10, basename(__FILE__).':'.__LINE__);
			if ($d['lamp kast']['s']=='Off'&&$d['snijplank']['s']==0&&$time<strtotime('21:30')) sw('lamp kast', 'On', basename(__FILE__).':'.__LINE__.' dag='.$d['dag']);
		}
		if ($d['bose101']['s']=='Off'&&$time>=strtotime('5:30')&&$time<strtotime('17:30')) {
			bosezone(101);
		}
		mset('living', $time);
	}
}
function fgarage() {
	global $d;
	$d=fetchdata();
	if ($d['zon']['s']<400&&$d['garage']['s']=='Off'&&$d['garageled']['s']=='Off') sw('garageled', 'On', basename(__FILE__).':'.__LINE__);
}
function fbadkamer() {
	global $d,$time;
	$d=fetchdata();
	if (past('$ 8badkamer-8')>10) {
		if ($d['lichtbadkamer']['s']<16&&$d['dag']<3&&$d['zon']['s']<50) {
			if ($time>strtotime('5:30')&&$time<strtotime('21:30')) sl('lichtbadkamer', 16, basename(__FILE__).':'.__LINE__);
			elseif ($d['lichtbadkamer']['s']<8) sl('lichtbadkamer', 8, basename(__FILE__).':'.__LINE__);
		}
	}
}
function fkeuken() {
	global $d,$time;
	$d=fetchdata();
	echo ('fkeuken zon='.$d['zon']['s'].' dag='.$d['dag'].' wasbak='.$d['wasbak']['s'].' snijplank='.$d['snijplank']['s'].' RkeukenL='.$d['RkeukenL']['s']);
	if ($d['wasbak']['s']<10&&$d['snijplank']['s']==0&&($d['dag']<3||$d['RkeukenL']['s']>80)) {
		echo __LINE__;
		sl('wasbak', 10, basename(__FILE__).':'.__LINE__);
	}
}
function finkom($force=false) {
	global $d,$time;
	$d=fetchdata();
	if ($d['zon']['s']<50&&($d['Weg']['s']==0&&$d['inkom']['s']<28&&$d['dag']<3)||$force==true) sl('inkom', 28, basename(__FILE__).':'.__LINE__);
}
function fhall() {
	global $d,$time;
	$d=fetchdata();
	if ($d['zon']['s']<50&&$time>=strtotime('7:30')&&($d['Ralex']['s']==0||$time<=strtotime('19:45')||past('deuralex')<900)) {
		if ($d['hall']['s']<28&&$d['Weg']['s']==0&&$d['dag']<3) {
			sl('hall', 28, basename(__FILE__).':'.__LINE__);
		}
	} else finkom();
	if ($d['Weg']['s']==0&&$time>=strtotime('21:30')&&$d['kamer']['s']==0&&$d['deurkamer']['s']=='Open'&&past('kamer')>3600) sl('kamer', 1, basename(__FILE__).':'.__LINE__);
}
function huisslapen() {
	global $d,$boseipbuiten;
	if (!isset($d['zon']['s'])) $d=fetchdata();
	sl(array('hall','inkom','eettafel','zithoek','wasbak','terras','ledluifel'), 0, basename(__FILE__).':'.__LINE__);
	sw(array('garageled','garage','pirgarage','pirkeuken','pirliving','pirinkom','pirhall','kristal','bureel','lamp kast','tuin','snijplank','zolderg','wc','GroheRed','kookplaat','steenterras','houtterras'), 'Off', basename(__FILE__).':'.__LINE__);
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
	foreach(array('waskamer', 'alex') as $i) {
		if ($d[$i]['s']>0&&$d[$i]['m']!=1) storemode($i, 1, basename(__FILE__).':'.__LINE__);
	}
	if ($d['auto']['s']=='Off') sw('auto', 'On', basename(__FILE__).':'.__LINE__);
	if ($d['luchtdroger']['m']!='Auto') storemode('luchtdroger', 'Auto', basename(__FILE__).':'.__LINE__);
	if ($d['bose101']['m']!=1) storemode('bose101', 1, basename(__FILE__).':'.__LINE__);
	if ($d['imac']['s']=='On') system("sudo -u root /var/www/imacsleep.sh");
}
function huisthuis() {
	global $d;
	if (!is_array($d)) $d=fetchdata();
	store('Weg', 0);
	if ($d['bose101']['m']!=1) storemode('bose101', 1, basename(__FILE__).':'.__LINE__);
	if ($d['bose103']['m']!=0) storemode('bose103', 0, basename(__FILE__).':'.__LINE__);
	if ($d['auto']['s']!='On') store('auto', 'On', basename(__FILE__).':'.__LINE__);
	if ($d['luchtdroger']['m']!='Auto') storemode('luchtdroger', 'Auto', basename(__FILE__).':'.__LINE__);
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
function waarschuwing($msg) {
	telegram($msg, false, 1);
	sw('sirene', 'On', basename(__FILE__).':'.__LINE__);
	store('sirene', 'On', basename(__FILE__).':'.__LINE__);
	sleep(10);
	sw('sirene', 'Off', basename(__FILE__).':'.__LINE__,true);
}
function past($name) {
	global $d;
	if (!isset($d[$name]['t'])) $d=fetchdata();
	if ($name=='$ remoteauto') lg('past '.$name.'	time='.time().' t='.$d[$name]['t'].' past='.time()-$d[$name]['t']);
	if (!empty($d[$name]['t'])) return time()-$d[$name]['t'];
	else return 999999999;
}
function idx($name) {
	global $d;
	if (!is_array($d)) $d=fetchdata();
	if ($d[$name]['i']>0) return $d[$name]['i'];
	else return 0;
}
function sl($name,$level,$msg='',$force=false) {
	global $d,$user,$domoticzurl;
	if (!is_array($d)) $d=fetchdata();
	if (is_array($name)) {
		foreach ($name as $i) {
			if ($d[$i]['s']!=$level) {
				sl($i, $level, $msg);
			}
		}
	} else {
		lg(' (SETLEVEL)	'.str_pad($user, 13, ' ', STR_PAD_LEFT).' => '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' => '.$level.' ('.$msg.')');
		if ($d[$name]['i']>0) {
			if ($d[$name]['s']!=$level||$force==true) file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx='.$d[$name]['i'].'&switchcmd=Set%20Level&level='.$level);
			if (str_starts_with($name, 'R')) store($name, $level, $msg);
		} else store($name, $level, $msg);
	}
}
function rgb($name,$hue,$level,$check=false) {
	global $d,$user,$domoticzurl;
	if (!is_array($d)) $d=fetchdata();
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
	if (!is_array($d)) $d=fetchdata();
	if ($d['sirene']['s']!='Off') {
		sw('sirene', 'Off', basename(__FILE__).':'.__LINE__,true);
		store('sirene', 'Off', basename(__FILE__).':'.__LINE__);
	}
	foreach (array('SDbadkamer','SDkamer','SDalex','SDwaskamer','SDzolder','SDliving') as $i) {
		if ($d[$i]['s']!='Off') {
			file_get_contents($domoticzurl.'/json.htm?type=command&param=resetsecuritystatus&idx='.$d[$i]['i'].'&switchcmd=Normal');
			store($i, 'Off', basename(__FILE__).':'.__LINE__);
		}
	}
}
function sw($name,$action='Toggle',$msg='',$force=false) {
	global $d,$user,$domoticzurl,$db;
	if (!is_array($d)) $d=fetchdata();
	if (is_array($name)) {
		foreach ($name as $i) {
			if ($d[$i]['s']!=$action) {
				sw($i, $action, $msg);
			}
		}
	} else {
		$msg=' (SWITCH)'.str_pad($user, 13, ' ', STR_PAD_LEFT).' => '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' => '.$action.' ('.$msg.')';
		if ($d[$name]['i']>10) {
			lg($msg);
			if ($d[$name]['s']!=$action||$force==true) {
				file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx='.$d[$name]['i'].'&switchcmd='.$action);
			}
		} elseif ($d[$name]['i']>0) {
			lg($msg);
			if ($action=='On') hass('switch','turn_on','switch.plug'.$d[$name]['i']);
			elseif ($action=='Off') hass('switch','turn_off','switch.plug'.$d[$name]['i']);
			//store($name, $action, $msg);
		} else {
			store($name, $action, $msg);
		}
	}
}

function store($name='',$status,$msg='',$idx=null) {
	global $db, $user;
	$time=time();
	if ($idx>0) {
		$sql="UPDATE devices SET s='$status',t='$time' WHERE i=$idx";
	} else $sql="INSERT INTO devices (n,s,t) VALUES ('$name','$status','$time') ON DUPLICATE KEY UPDATE s='$status',t='$time';";
	$db->query($sql);
	/*if ($name!='crypto'&&!endswith($name, '_temp')&&strlen($msg>0)) */
	if ($name=='') lg(' (STORE) '.str_pad($user, 13, ' ', STR_PAD_LEFT).' => '.str_pad($idx, 13, ' ', STR_PAD_RIGHT).' => '.$status.(strlen($msg>0)?'	('.$msg.')':''));
	else lg(' (STORE) '.str_pad($user, 13, ' ', STR_PAD_LEFT).' => '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' => '.$status.(strlen($msg>0)?'	('.$msg.')':''));
}
function storemode($name,$mode,$msg='',$updatetime=false) {
	global $db, $user, $time;
	if(!isset($db)) $db=dbconnect();
	if ($updatetime==true) {
		$time=time();
		$db->query("INSERT INTO devices (n,m,t) VALUES ('$name','$mode','$time') ON DUPLICATE KEY UPDATE m='$mode',t='$time';");
		lg(' (STOREMODE+) '.str_pad($user, 9, ' ', STR_PAD_LEFT).' => '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' => '.$mode.(strlen($msg>0)?'	('.$msg.')':''));
	} else {
		$db->query("INSERT INTO devices (n,m) VALUES ('$name','$mode') ON DUPLICATE KEY UPDATE m='$mode';");
		lg(' (STOREMODE) '.str_pad($user, 9, ' ', STR_PAD_LEFT).' => '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' => '.$mode.(strlen($msg>0)?'	('.$msg.')':''));
	}
}
function storeicon($name,$icon,$msg='',$updatetime=false) {
	global $d, $db, $user, $time;
	if (!is_array($d)) $d=fetchdata();
	if ($d[$name]['icon']!=$icon) {
		if(!isset($db)) $db=dbconnect();
		if ($updatetime==true) {
			$time=time();
			$db->query("INSERT INTO devices (n,t,icon) VALUES ('$name','$time','$icon') ON DUPLICATE KEY UPDATE t='$time',icon='$icon';");
		} else $db->query("INSERT INTO devices (n,icon) VALUES ('$name','$icon') ON DUPLICATE KEY UPDATE icon='$icon';");
		if (!endswith($name, '_temp')) lg(' (STOREICON)	'.$user.'	=> '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' => '.$icon.(strlen($msg>0)?'	('.$msg.')':''));
	}
}
function alert($name,$msg,$ttl,$silent=true,$to=1) {
	global $db,$time;
	if(!isset($db)) $db=dbconnect();
	$last=0;
	$stmt=$db->query("SELECT t FROM alerts WHERE n='$name';");
	while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
		if (isset($row['t'])) $last=$row['t'];
	}
	if ($last < $time-$ttl) {
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
	global $d,$user,$domoticzurl;
	if (!is_array($d)) $d=fetchdata();
	if (isset($d[$name]['i'])&&$d[$name]['i']>0) {
		if ($check==true) {
			if ($d[$name]['s']!=$svalue) {
				return file_get_contents($domoticzurl.'/json.htm?type=command&param=udevice&idx='.$d[$name]['i'].'&nvalue='.$nvalue.'&svalue='.$svalue);
			}
		} else return file_get_contents($domoticzurl.'/json.htm?type=command&param=udevice&idx='.$d[$name]['i'].'&nvalue='.$nvalue.'&svalue='.$svalue);
	} else store($name, $svalue, basename(__FILE__).':'.__LINE__);
	lg(' (udevice) | '.$user.'=> '.str_pad($name, 13, ' ', STR_PAD_LEFT).' =>'.$nvalue.','.$svalue.(isset($msg)?' ('.$msg:')'));
}
function convertToHours($time) {
	if ($time<600) return substr(strftime('%M:%S', $time-3600), 1);
	elseif ($time>=600&&$time<3600) return strftime('%M:%S', $time-3600);
	else return strftime('%k:%M:%S', $time-3600);
}
function ping($ip) {
	$result=exec("/bin/ping -c1 -W1 -s1 $ip", $outcome, $reply);
	if ($reply==0) return true;
	else return false;
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
//	if (!is_array($d)) $d=fetchdata();
	alert($device,	$msg,	300, false, 2, true);
//	if ($d['Weg']['s']<=1) {

//		foreach (array(/*'Ralex',*/'RkamerL','RkeukenL','RkamerR','Rwaskamer','Rliving','RkeukenR','Rbureel') as $i) {
//			if ($d[$i]['s']>0) sl($i, 1, basename(__FILE__).':'.__LINE__);
//		}
//		if ($d['zon']['s']<200) {
//			foreach (array('hall','inkom','kamer','waskamer',/*'alex',*/'eettafel','zithoek','lichtbadkamer','wasbak','terras') as $i) {
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
function startsWith($haystack,$needle) {
	return $needle===""||strrpos($haystack, $needle, -strlen($haystack))!==false;
}
function endswith($string,$test) {
	$strlen=strlen($string);$testlen=strlen($test);
	if ($testlen>$strlen) return false;
	return substr_compare($string, $test, $strlen-$testlen, $testlen)===0;
}
function bosekey($key,$sleep=75000,$ip=101,$msg=null) {
	lg('bosekey '.$key.' '.$msg);
	$xml="<key state=\"press\" sender=\"Gabbo\">$key</key>";
	bosepost("key", $xml, $ip, true);
	$xml="<key state=\"release\" sender=\"Gabbo\">$key</key>";
	bosepost("key", $xml, $ip);
	if ($sleep>0) usleep($sleep);
/*	if (startsWith($key,'PRESET')&&$ip!=102) {
		for ($x=1;$x<=10;$x++) {
			$data=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.$ip:8090/now_playing"))), true);
//			lg('Bosekey '.$key.' '.$ip.' '.$x.' data='.print_r($data, true));
			if (isset($data)) {
				if (isset($data['playStatus'])&&$data['playStatus']=='PLAY_STATE'&&isset($data['artist'])&&!str_contains($data['artist'], 'Kalkbrenner')&&!str_contains($data['track'], 'Kalkbrenner')) {
					break;
				} elseif (isset($data['playStatus'])&&$data['playStatus']=='PLAY_STATE') {
					bosekey('SHUFFLE_ON', $x*75000, $ip);
					bosekey('NEXT_TRACK', $x*75000, $ip);
				}
			}
			sleep(2);
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
	global $d,$time,$dow,$weekend;
	$time=time();
	$week=strftime('%-V', $time);
	$dow=date("w");
	if($dow==0||$dow==6)$weekend=true; else $weekend=false;
	if ($weekend==true) {
		if ((int)$week % 2 == 0) $preset='PRESET_4';
		else $preset='PRESET_3';
	} else {
		if ((int)$week % 2 == 0) $preset='PRESET_2';
		else $preset='PRESET_1';
	}
	if (($d['Weg']['s']<=1&&$d['bose101']['m']==1)||$forced===true) {
		if ($d['Weg']['s']==0&&($d['Media']['s']=='Off'||$forced===true)&&$d['bose101']['s']=='Off'&&$time<strtotime('21:00')) {
//			bosekey("POWER", 1500000, 101);
			sw('bose101', 'On', basename(__FILE__).':'.__LINE__);
			bosekey($preset, 750000, 101);
			if ($d['Media']['s']=='On'&&$d['eettafel']['s']==0) bosevolume(0, 101, basename(__FILE__).':'.__LINE__);
			else bosevolume(17, 101, basename(__FILE__).':'.__LINE__);
		}
		if ($ip>101) {
			if ($d['bose'.$ip]['s']!='On') sw('bose'.$ip, 'On', basename(__FILE__).':'.__LINE__);
			    if ($ip==102) $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.102">587A628BB5C0</member></zone>';
			elseif ($ip==103) $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.103">C4F312F65070</member></zone>';
			elseif ($ip==104) $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.104">C4F312DCE637</member></zone>';
			elseif ($ip==105) $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.105">304511BC3CA5</member></zone>';
			elseif ($ip==106) $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.106">C4F312F89670</member></zone>';
			elseif ($ip==107) $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.107">B0D5CC065C20</member></zone>';
			if ($d['bose101']['s']=='Off'&&$d['bose'.$ip]['s']=='Off') {
//				bosekey("POWER", 1500000, 101);
				sw('bose101', 'On', basename(__FILE__).':'.__LINE__);
				bosekey($preset, 750000, 101);
				if ($d['Media']['s']=='On'&&$d['eettafel']['s']==0) bosevolume(0, 101, basename(__FILE__).':'.__LINE__);
				else bosevolume(21, 101, basename(__FILE__).':'.__LINE__);
				usleep(100000);
				bosepost('setZone', $xml, 101);
				if ($vol=='') {
					if ($time>strtotime('6:00')&&$time<strtotime('20:00')) bosevolume(30, $ip, basename(__FILE__).':'.__LINE__);
					else bosevolume(22, $ip, basename(__FILE__).':'.__LINE__);
				} else bosevolume($vol, $ip, basename(__FILE__).':'.__LINE__);
			} elseif ($d['bose'.$ip]['s']=='Off') {
				bosepost('setZone', $xml, 101);
				store('bose'.$ip, 'On');
//				lg($xml);
				if ($vol=='') {
					if ($time>strtotime('6:00')&&$time<strtotime('21:00')) bosevolume(30, $ip, basename(__FILE__).':'.__LINE__);
					else bosevolume(20, $ip, basename(__FILE__).':'.__LINE__);
				} else bosevolume($vol, $ip, basename(__FILE__).':'.__LINE__);
			}
		}
	}
}
function bosepost($method,$xml,$ip=101,$log=false) {
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

function sirene($msg) {
	lg(' >>> SIRENE '.$msg);
	$last=mget('sirene');
	$time=time();
	lg(' >>> last='.$last.'	time='.$time);
	if ($last>$time-300) {
		sw('sirene', 'On', basename(__FILE__).':'.__LINE__);
		telegram($msg.' om '.strftime("%k:%M:%S", $time), false, 2);
	}
	mset('sirene', $time);
}
function createheader($page='') {
	global $udevice,$ipaddress;
	echo '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
		<title>Floorplan</title>';
	if ($ipaddress=='192.168.2.202'||$ipaddress=='192.168.4.3')  { //Aarde
		echo '
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=0.868,user-scalable=yes,minimal-ui">';
	} elseif ($ipaddress=='192.168.2.203'||$ipaddress=='192.168.4.4'||$udevice=='iPad')  { //iPad
		echo '
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.35,user-scalable=yes,minimal-ui">';
	} elseif ($ipaddress=='192.168.2.23'||$ipaddress=='192.168.4.5')  { //iPhone Kirby
		echo '
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=0.755,user-scalable=yes,minimal-ui">';
	} elseif ($udevice=='iPhone') {
		echo '
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=0.755,user-scalable=yes,minimal-ui">';
	} else {
		echo '
		<meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,minimal-ui">';
	}
	echo '
		<meta name="HandheldFriendly" content="true">
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="theme-color" content="#000">
		<link rel="manifest" href="/manifest.json">
		<link rel="shortcut icon" href="images/domoticzphp48.png">
		<link rel="apple-touch-icon" href="images/domoticzphp48.png">
		<link rel="apple-touch-startup-image" href="images/domoticzphp144.png">
		<link rel="stylesheet" type="text/css" href="/styles/floorplan.css?v=6">
		<script type="text/javascript" src="/scripts/m4q.min.js"></script>
		<script type="text/javascript" src="/scripts/floorplanjs.js?v=6"></script>';
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
function daikinset($device, $power, $mode, $stemp,$msg='', $fan='A', $spmode=-1, $maxpow=false) {
	global $time;
	$time=time();
	$d=fetchdata();
	if ($device=='living') $ip=111;
	elseif ($device=='kamer') $ip=112;
	elseif ($device=='alex') $ip=113;
	if ($maxpow==false) {
		$maxpow=$d['daikin_kWh']['icon'];
	} else {
		if ($maxpow!=$d['daikin_kWh']['icon']) storeicon('daikin_kWh', $maxpow);
	}
	$url="http://192.168.2.$ip/aircon/set_control_info?pow=$power&mode=$mode&stemp=$stemp&f_rate=$fan&shum=0&f_dir=0";
	file_get_contents($url);
	sleep(1);
	$status=daikinstatus($device);
	if ($d['daikin'.$device]['s']!=$status) store('daikin'.$device, $status, basename(__FILE__).':'.__LINE__.':'.$msg);
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
function updatefromdomoticz() {
	global $db,$domoticzurl;
	$d=fetchdata();
	$domoticz=json_decode(file_get_contents($domoticzurl.'/json.htm?type=command&param=getdevices&used=true'),true);
	if ($domoticz) {
		foreach ($domoticz['result'] as $dom) {
			$update=false;
			$name=$dom['Name'];
			if (isset($dom['SwitchType'])) $switchtype=$dom['SwitchType'];
			elseif (isset($dom['SubType'])) $switchtype=$dom['SubType'];
			if($switchtype=='On/Off') $update=true;
			elseif($switchtype=='Switch') $update=true;
			elseif($switchtype=='Contact') $update=true;
			elseif($switchtype=='Door Contact') $update=true;
			elseif($switchtype=='Motion Sensor') $update=true;
			elseif($switchtype=='Push On Button') $update=true;
			elseif($switchtype=='X10 Siren') $update=true;
			elseif($switchtype=='Smoke Detector') $update=true;
			elseif($switchtype=='Selector') $update=true;
			elseif($switchtype=='Blinds Inverted') $update=true;
			if ($dom['Type']=='Temp') {
				$status=$dom['Temp'];
				 $update=false;
			} elseif ($dom['Type']=='Temp + Humidity') {
				$status=$dom['Temp'];
				 $update=false;
			} elseif ($dom['TypeImg']=='current') {
				$status=str_replace(' Watt', '', $dom['Data']);
				 $update=false;
			} elseif ($name=='luifel') {
				$status=str_replace('%', '', $dom['Level']);
				 $update=true;
			} elseif ($switchtype=='Dimmer') {
				if ($dom['Data']=='Off') $status=0;
				elseif ($dom['Data']=='On') $status=100;
				else $status=filter_var($dom['Data'], FILTER_SANITIZE_NUMBER_INT);
				 $update=true;
			} elseif ($switchtype=='Blinds Percentage') {
				if ($dom['Data']=='Open') $status=0;
				elseif ($dom['Data']=='Closed') $status=100;
				else $status=filter_var($dom['Data'], FILTER_SANITIZE_NUMBER_INT);
				$update=true;
			} elseif ($name=='achterdeur') {
				if ($dom['Data']=='Open') $status='Closed';
				else $status='Open';
			} else $status=$dom['Data'];
			if ($update==true) {
				if ($status!=$d[$name]['s']) {
					echo $name.'	= '.$status.'<br>';
					$query="UPDATE devices SET s=:status WHERE n=:name;";
					$stmt=$db->prepare($query);
					$stmt->execute(array(':status'=>$status, ':name'=>$name));
				}
			}
		}
	}

}
function hass($domain,$service,$entity) {
	lg('HASS '.$domain.' '.$service.' '.$entity);
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,'http://192.168.2.19:8123/api/services/'.$domain.'/'.$service);
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/json','Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJjZDM1MDc5MzJmMDY0MWZmODRlMzhlNTExNmM1NDFlMSIsImlhdCI6MTY4MTk3NjMwNywiZXhwIjoxOTk3MzM2MzA3fQ.Dthf5CqY06vfsnCruEclAKfds6h11EjyPsXNwZgT_vU'));
	curl_setopt($ch,CURLOPT_POSTFIELDS,'{"entity_id":"'.$entity.'"}');
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_FRESH_CONNECT,true);
	curl_setopt($ch,CURLOPT_TIMEOUT,5);
	$response=curl_exec($ch);
	lg($response);
	curl_close($ch);
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
function dbconnect() {
	global $dbname,$dbuser,$dbpass;
	return new PDO("mysql:host=127.0.0.1;dbname=$dbname;",$dbuser,$dbpass);
}
function fetchdata() {
	//unset ($GLOBALS['d']);
	//lg('fetch '.debug_backtrace()[0]['file'].':'.debug_backtrace()[0]['line']);
	global $db;
	if(!isset($db)) $db=dbconnect();
	$stmt=$db->query("select n,i,s,t,m,dt,icon from devices;");
	while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) $d[$row['n']] = $row;
	$dag=0;
	$time=time();
	if ($time>=$d['civil_twilight']['s']&&$time<=$d['civil_twilight']['m']) {
		$dag=1;
		if ($time>=$d['Sun']['s']&&$time<=$d['Sun']['m']) {
			if ($time>=$d['Sun']['s']+900&&$time<=$d['Sun']['m']-900) $dag=4;
			else $dag=3;
		} else {
			$zonop=($d['civil_twilight']['s']+$d['Sun']['s'])/2;
			$zononder=($d['civil_twilight']['m']+$d['Sun']['m'])/2;
			if ($time>=$zonop&&$time<=$zononder) $dag=2;
		}
	}
	$d['dag']=$dag;
	return $d;
}
function fetchdataidx() {
	//unset ($GLOBALS['d']);
	//lg('fetch '.debug_backtrace()[0]['file'].':'.debug_backtrace()[0]['line']);
	global $db;
	if(!isset($db)) $db=dbconnect();
	$stmt=$db->query("select n,i,s,t,m,dt,icon from devices where i is not null;");
	while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) $d[$row['i']] = $row;
	$dag=0;
	$time=time();
	if ($time>=$d['civil_twilight']['s']&&$time<=$d['civil_twilight']['m']) {
		$dag=1;
		if ($time>=$d['Sun']['s']&&$time<=$d['Sun']['m']) {
			if ($time>=$d['Sun']['s']+900&&$time<=$d['Sun']['m']-900) $dag=4;
			else $dag=3;
		} else {
			$zonop=($d['civil_twilight']['s']+$d['Sun']['s'])/2;
			$zononder=($d['civil_twilight']['m']+$d['Sun']['m'])/2;
			if ($time>=$zonop&&$time<=$zononder) $dag=2;
		}
	}
	$d['dag']=$dag;return $d;
}
function roundUpToAny($n,$x=5) {
	return round(($n+$x/2)/$x)*$x;
}
function roundDownToAny($n,$x=5) {
	return floor($n/$x) * $x;
}
function mset($key, $data, $ttl=0) {
//	lg('mset '.$key.' '.$data);
	global $memcache;
	
	$memcache->set($key, $data);
}
function mget($key) {
	global $memcache;
	$data=$memcache->get($key);
//	lg('mget '.$key.' '.$data);
	return $data;
}