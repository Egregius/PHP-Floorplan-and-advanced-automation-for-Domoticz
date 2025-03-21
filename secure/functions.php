<?php
require '/var/www/config.php';
$dow=date("w");if($dow==0||$dow==6)$weekend=true; else $weekend=false;
$time=time();
if (!isset($db)) $db=dbconnect(basename(__FILE__).':'.__LINE__);
$memcache=new Memcache;
$memcache->connect('192.168.2.21',11211) or die ("Could not connect");
date_default_timezone_set('Europe/Brussels');

function t() {
	global $dow,$t;
	$dow=date("w");
	if($dow==1) $t=strtotime('7:00');
	elseif($dow==2) $t=strtotime('7:00');
	elseif($dow==3) $t=strtotime('7:00');
	elseif($dow==4) $t=strtotime('7:00');
	elseif($dow==5) $t=strtotime('7:00');
	elseif($dow==6) $t=strtotime('7:45');
	elseif($dow==0) $t=strtotime('7:45');
	return $t;
}
	
function fliving() {
	global $d,$time,$t;
	$time=time();
	if ($d['Media']['s']=='Off'&&$d['lamp kast']['s']!='On'&&$d['eettafel']['s']==0&&$d['zithoek']['s']==0) {
		if (($d['zon']==0&&$d['dag']<4)||($d['RkeukenL']['s']>80&&$d['RkeukenR']['s']>80&&$d['Rbureel']['s']>80&&$d['Rliving']['s']>80)) {
			$am=strtotime('10:00');
			$t=t();
			if ($d['lamp kast']['s']=='Off'&&$d['wasbak']['s']==0&&$d['zithoek']['s']==0&&$d['snijplank']['s']==0&&$time>$t&&$time<$am&&past('langekast')>10) sw('lamp kast', 'On', basename(__FILE__).':'.__LINE__.' dag='.$d['dag']);
			if ($d['zithoek']['s']==0&&$time<$am) sl('zithoek', 8, basename(__FILE__).':'.__LINE__);
			if ($d['wasbak']['s']==0&&$time<$am) sl('wasbak', 10, basename(__FILE__).':'.__LINE__);
		}
	}
	mset('living', $time);
//	if ($d['lg_webos_tv_cd9e']['s']!='On'&&$d['langekast']['s']=='On'&&$d['bose101']['s']=='Off'&&$time>=strtotime('5:30')&&$time<strtotime('19:30')&&past('langekast')>60) {
//		bosezone(101);
//	}
}
function fgarage() {
	global $d;
	if ($d['zon']<100&&$d['garage']['s']=='Off'&&$d['garageled']['s']=='Off') sw('garageled', 'On', basename(__FILE__).':'.__LINE__);
}
function fkeuken() {
	global $d;
	if (1==2) {
		if ($d['wasbak']['s']<12) sl('wasbak', 12, basename(__FILE__).':'.__LINE__);
		if ($d['snijplank']['s']<12) sl('snijplank', 12, basename(__FILE__).':'.__LINE__);
	} else {
		if ($d['wasbak']['s']<10&&$d['snijplank']['s']==0&&($d['dag']<3||$d['RkeukenL']['s']>80)) sl('wasbak', 10, basename(__FILE__).':'.__LINE__);
	}
//	hass('input_button','press','input_button.wakeipad');
}
function finkom($force=false) {
	global $d,$time;
	if (($d['dag']<3&&$d['Weg']['s']==0)||$force==true) {
		if ($d['inkom']['s']<30&&$d['dag']<4) sl('inkom', 30, basename(__FILE__).':'.__LINE__);
		if ($d['deuralex']['s']=='Open'&&$d['deurkamer']['s']=='Open'&&$time>=strtotime('19:45')&&$time<=strtotime('20:30')) sl('hall', 30, basename(__FILE__).':'.__LINE__);
	}
}
function fhall() {
	global $d,$t,$time;
	$time=time();
	$dow=date("w");
	if($dow==0||$dow==6) $t=strtotime('7:30');
	else $t=strtotime('7:00');
	if ($d['dag']<3&&$time<=strtotime('20:45')&&($time>=$t+1800||$d['Ralex']['s']==0||$d['deuralex']['s']=='Open'||past('deuralex')<900)) {
		if ($d['hall']['s']<30&&$d['Weg']['s']==0&&$d['dag']<3) {
			sl('hall', 30, basename(__FILE__).':'.__LINE__);
		}
	} else finkom();
	if ($d['Weg']['s']==0&&$d['RkamerL']['s']>70&&$d['RkamerR']['s']>70&&$time>=strtotime('21:00')&&$time<=strtotime('23:00')&&$d['kamer']['s']==0&&$d['deurkamer']['s']=='Open'&&past('kamer')>7200) sl('kamer', 1, basename(__FILE__).':'.__LINE__);
}
function huisslapen($weg=false) {
	global $d;
	sl(array('hall','inkom','eettafel','zithoek','wasbak','snijplank','terras','ledluifel'), 0, basename(__FILE__).':'.__LINE__);
	sw(array('lamp kast','kristal','garageled','garage','pirgarage','pirkeuken','pirliving','pirinkom','pirhall','bureel','tuin','zolderg','wc','GroheRed','kookplaat','steenterras','tuintafel','kerstboom','langekast'), 'Off', basename(__FILE__).':'.__LINE__);
	foreach (array('living_set','alex_set','kamer_set','badkamer_set'/*,'eettafel','zithoek'*/,'luifel') as $i) {
		if ($d[$i]['m']!=0&&$d[$i]['s']!='D') storemode($i, 0, basename(__FILE__).':'.__LINE__);
	}
}
function huisthuis($msg='') {
	store('Weg', 0);
	lg('Huis thuis '.$msg);
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
function past($name,$lg='') {
	global $d,$time;
	$time=time();
	if ($name=='$ remoteauto') lg('past '.$name.'	time='.$time.' t='.$d[$name]['t'].' past='.$time-$d[$name]['t']);
	if (!empty($d[$name]['t'])) return $time-$d[$name]['t'];
	else return 999999999;
}
function idx($name) {
	global $d;
	if ($d[$name]['i']>0) return $d[$name]['i'];
	else return 0;
}
function sl($name,$level,$msg='',$force=false) {
	global $d,$user,$domoticzurl;
	if (!isset($d)) $d=fetchdata(0, basename(__FILE__).':'.__LINE__);
	if (is_array($name)) {
		foreach ($name as $i) {
			if ($d[$i]['s']!=$level) {
				sl($i, $level, $msg);
			}
		}
	} else {
		lg('(SETLEVEL)	'.str_pad($user??'', 13, ' ', STR_PAD_LEFT).' => '.str_pad($name??'', 13, ' ', STR_PAD_RIGHT).' => '.$level.' ('.$msg.')',4);
		if ($d[$name]['i']>0) {
			if ($d[$name]['s']!=$level||$force==true) file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx='.$d[$name]['i'].'&switchcmd=Set%20Level&level='.$level);
			if (str_starts_with($name, 'R')) store($name, $level, $msg);
		} else store($name, $level, $msg);
	}
}
function rgb($name,$hue,$level,$check=false) {
	global $d,$user,$domoticzurl;
	lg(' (RGB)		'.$user.' => '.str_pad($name??'', 13, ' ', STR_PAD_RIGHT).' => '.$level,4);
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
	if ($d['sirene']['s']!='Off') {
		sw('sirene', 'Off', basename(__FILE__).':'.__LINE__,true);
//		store('sirene', 'Off', basename(__FILE__).':'.__LINE__);
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
	if (!isset($d)) $d=fetchdata(0, basename(__FILE__).':'.__LINE__);
	if (is_array($name)) {
		foreach ($name as $i) {
			if ($d[$i]['s']!=$action) {
				sw($i, $action, $msg);
				usleep(300000);
			}
		}
	} else {
		$msg='(SWITCH)'.str_pad($user??'', 13, ' ', STR_PAD_LEFT).' => '.str_pad($name??'', 13, ' ', STR_PAD_RIGHT).' => '.$action.' ('.$msg.')';
		if ($d[$name]['i']>10) {
			lg($msg,4);
			if ($d[$name]['s']!=$action||$force==true) {
				file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx='.$d[$name]['i'].'&switchcmd='.$action);
			}
		} elseif ($d[$name]['i']>0) {
			lg($msg,4);
			if ($action=='On') hass('switch','turn_on','switch.plug'.$d[$name]['i']);
			elseif ($action=='Off') hass('switch','turn_off','switch.plug'.$d[$name]['i']);
			//store($name, $action, $msg);
		} else {
			store($name, $action, $msg);
		}
	}
}
function setpoint($name, $value,$msg='') {
	global $d,$user,$domoticzurl,$db;
	if(!isset($d)) $d=fetchdata();
	$msg='(SETPOINT)'.str_pad($user??'', 13, ' ', STR_PAD_LEFT).' => '.str_pad($name??'', 13, ' ', STR_PAD_RIGHT).' => '.$value.' ('.$msg.')';
	lg($msg,3);
	if ($d[$name]['i']>0) {
		file_get_contents($domoticzurl.'/json.htm?type=command&param=setsetpoint&idx='.$d[$name]['i'].'&setpoint='.$value);
	}
}
function store($name='',$status='',$msg='',$idx=null) {
	global $db, $user;
	$time=time();
	if ($idx>0) {
		$sql="UPDATE devices SET s='$status',t='$time' WHERE i=$idx";
	} else $sql="INSERT INTO devices (n,s,t) VALUES ('$name','$status','$time') ON DUPLICATE KEY UPDATE s='$status',t='$time';";
	$db->query($sql);
//	mset($name,$status);
	if ($name=='') lg('(STORE) '.str_pad($user??'', 9, ' ', STR_PAD_LEFT).' => '.str_pad($idx??'', 13, ' ', STR_PAD_RIGHT).' => '.$status.(strlen($msg>0)?'	('.$msg.')':''),10);
	else {
		if (endswith($name, '_temp')) return;
		elseif(endswith($name, '_kWh')) return;
		elseif(endswith($name, '_hum')) return;
		else lg('(STORE) '.str_pad($user??'', 9, ' ', STR_PAD_LEFT).' => '.str_pad($name??'', 13, ' ', STR_PAD_RIGHT).' => '.$status.(strlen($msg>0)?'	('.$msg.')':''),10);
	}
}
function storemode($name,$mode,$msg='',$updatetime=true) {
	global $db, $user, $time;
	$time=time();
	$db->query("INSERT INTO devices (n,m,t) VALUES ('$name','$mode','$time') ON DUPLICATE KEY UPDATE m='$mode',t='$time';");
	lg('(STOREMODE) '.str_pad($user, 9, ' ', STR_PAD_LEFT).' => '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' => '.$mode.(strlen($msg>0)?'	('.$msg.')':''),10);
}
function storeicon($name,$icon,$msg='',$updatetime=false) {
	global $d, $db, $user, $time;
	if ($d[$name]['icon']!=$icon) {
		$time=time();
		$db->query("INSERT INTO devices (n,t,icon) VALUES ('$name','$time','$icon') ON DUPLICATE KEY UPDATE t='$time',icon='$icon';");
		if (endswith($name, '_temp')) return;
		lg('(STOREICON)	'.$user.'	=> '.str_pad($name??'', 13, ' ', STR_PAD_RIGHT).' => '.$icon.(strlen($msg>0)?'	('.$msg.')':''),10);
	}
}
function alert($name,$msg,$ttl,$silent=true,$to=1) {
	global $db,$time;
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
	if (isset($d[$name]['i'])&&$d[$name]['i']>0) {
		if ($check==true) {
			if ($d[$name]['s']!=$svalue) {
				return file_get_contents($domoticzurl.'/json.htm?type=command&param=udevice&idx='.$d[$name]['i'].'&nvalue='.$nvalue.'&svalue='.$svalue);
			}
		} else return file_get_contents($domoticzurl.'/json.htm?type=command&param=udevice&idx='.$d[$name]['i'].'&nvalue='.$nvalue.'&svalue='.$svalue);
	} else store($name, $svalue, basename(__FILE__).':'.__LINE__);
	lg('(udevice) | '.$user.'=> '.str_pad($name??'', 13, ' ', STR_PAD_LEFT).' =>'.$nvalue.','.$svalue.(isset($msg)?' ('.$msg:')'));
}
function convertToHours($time) {
	if ($time<600) return substr(date('i:s', $time-3600), 1);
	elseif ($time>=600&&$time<3600) return date('i:s', $time-3600);
	else return date('G:i:s', $time-3600);
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
	global $d,$device;
	resetsecurity();
//	global $d,$device;
	alert($device,	$msg,	300, false, 2, true);
//	if ($d['Weg']['s']<=1) {

//		foreach (array(/*'Ralex',*/'RkamerL','RkeukenL','RkamerR','Rwaskamer','Rliving','RkeukenR','Rbureel') as $i) {
//			if ($d[$i]['s']>0) sl($i, 1, basename(__FILE__).':'.__LINE__);
//		}
//		if ($d['zon']<200) {
//			foreach (array('hall','inkom','kamer','waskamer',/*'alex',*/'eettafel','zithoek','lichtbadkamer','wasbak','terras') as $i) {
//				if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
//			}
//			foreach (array('snijplank','garage','lamp kast','bureel', 'tuin') as $i) {
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
	lg('Telegram sent: '.$msg);
}
function lg($msg,$level=0) {
/*
Levels:
0:	Default / Undefined
1:	Loop starts
2:	Heating
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
	global $db,$d;
	$loglevel=0;
	if (isset($d['auto']['m'])) $loglevel=$d['auto']['m'];
	else {
		if(!isset($db)) $db=dbconnect(basename(__FILE__).':'.__LINE__.'-'.__FUNCTION__);
		$stmt=$db->query("select m from devices WHERE n='auto';");
		while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) $loglevel = $row['m'];
	}
	if ($level<=$loglevel) {
		$fp=fopen('/temp/domoticz.log', "a+");
		$time=microtime(true);
		$dFormat="Y-m-d H:i:s";
		$mSecs=$time-floor($time);
		$mSecs=substr(number_format($mSecs, 3), 1);
		fwrite($fp, sprintf("%s%s %s\n", date($dFormat), $mSecs, $msg.' ['.$level.']'));
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
		if ($vol>50) bosebass(-4, $ip);
		elseif ($vol>40) bosebass(-5, $ip);
		elseif ($vol>30) bosebass(-6, $ip);
		elseif ($vol>20) bosebass(-7, $ip);
		elseif ($vol>10) bosebass(-8, $ip);
		else bosebass(-9, $ip);
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
function boseplaylist() {
	$time=time();
	$jaardag=date('z')+1;
	$dow=date("w");
	if($dow==0||$dow==6)$weekend=true; else $weekend=false;
	if ($weekend==true) {
		if ((int)$jaardag % 3 == 0) $preset='MIX-3';
		elseif ((int)$jaardag % 2 == 0) $preset='MIX-2';
		else $preset='MIX-1';
	} else {
		if ((int)$jaardag % 3 == 0) $preset='EDM-3';
		elseif ((int)$jaardag % 2 == 0) $preset='EDM-2';
		else $preset='EDM-1';
	}
	return $preset;
}
function bosezone($ip,$forced=false,$vol='') {
	global $d,$time,$dow,$weekend;
	if (!isset($d)) $d=fetchdata(0,basename(__FILE__).':'.__LINE__);
	$time=time();
	$t=t();
	$playlist=boseplaylist();
	    if ($playlist=='EDM-1') $preset='PRESET_1';
	elseif ($playlist=='EDM-2') $preset='PRESET_2';
	elseif ($playlist=='EDM-3') $preset='PRESET_3';
	elseif ($playlist=='MIX-1') $preset='PRESET_4';
	elseif ($playlist=='MIX-2') $preset='PRESET_5';
	elseif ($playlist=='MIX-3') $preset='PRESET_6';
	lg($playlist.' '.$preset);
	if (($d['Weg']['s']<=1&&$d['bose101']['m']==1)||$forced===true) {
		if ($d['Weg']['s']==0&&($d['lg_webos_tv_cd9e']['s']!='On'||$forced===true)&&$d['bose101']['s']=='Off'&&$time<strtotime('21:00')&&$d['langekast']['s']=='On'&&past('langekast')>60) {
			sw('bose101', 'On', basename(__FILE__).':'.__LINE__);
			bosekey($preset, 750000, 101, basename(__FILE__).':'.__LINE__);
			lg('Bose zone time='.$time.'|'.$t+1800);
			if ($d['lg_webos_tv_cd9e']['s']=='On'&&$d['eettafel']['s']==0) bosevolume(0, 101, basename(__FILE__).':'.__LINE__);
			elseif ($time<$t+1800) bosevolume(12, 101, basename(__FILE__).':'.__LINE__);
			else bosevolume(20, 101, basename(__FILE__).':'.__LINE__);
		}
		if ($ip>101) {
			if ($d['bose'.$ip]['s']=='Off') sw('bose'.$ip, 'On', basename(__FILE__).':'.__LINE__);
			    if ($ip==102) $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.102">304511BC3CA5</member></zone>';
			elseif ($ip==103) $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.103">C4F312F65070</member></zone>';
			elseif ($ip==104) $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.104">C4F312DCE637</member></zone>';
			elseif ($ip==105) $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.105">587A628BB5C0</member></zone>';
			elseif ($ip==106) $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.106">C4F312F89670</member></zone>';
			elseif ($ip==107) $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.107">B0D5CC065C20</member></zone>';
			if ($d['bose101']['s']=='Off'&&$d['bose'.$ip]['s']=='Off') {
				sw('bose101', 'On', basename(__FILE__).':'.__LINE__);
				bosekey($preset, 750000, 101, basename(__FILE__).':'.__LINE__);
				if ($d['lg_webos_tv_cd9e']['s']=='On'&&$d['eettafel']['s']==0) bosevolume(0, 101, basename(__FILE__).':'.__LINE__);
				elseif ($time<strtotime('7:00')) bosevolume(12, 101, basename(__FILE__).':'.__LINE__);
				else bosevolume(20, 101, basename(__FILE__).':'.__LINE__);
				usleep(100000);
				bosepost('setZone', $xml, 101);
				if ($vol=='') {
					if ($time>strtotime('6:00')&&$time<strtotime('20:00')) bosevolume(30, $ip, basename(__FILE__).':'.__LINE__);
					else bosevolume(22, $ip, basename(__FILE__).':'.__LINE__);
				} else bosevolume($vol, $ip, basename(__FILE__).':'.__LINE__);
			} elseif ($d['bose'.$ip]['s']=='Off') {
				bosepost('setZone', $xml, 101);
				store('bose'.$ip, 'On');
				if ($vol=='') {
					if ($time>strtotime('6:00')&&$time<strtotime('21:00')) bosevolume(30, $ip, basename(__FILE__).':'.__LINE__);
					else bosevolume(20, $ip, basename(__FILE__).':'.__LINE__);
				} else bosevolume($vol, $ip, basename(__FILE__).':'.__LINE__);
			}
		}
	}
}
function bosepost($method,$xml,$ip=101,$log=false) {
	for($x=1;$x<=10;$x++) {
		$ch=curl_init("http://192.168.2.$ip:8090/$method");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 1); 
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
		telegram($msg.' om '.date("G:i:s", $time), false, 2);
	}
	mset('sirene', $time);
}
function createheader($page='') {
	global $scale;
	echo '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
		<title>Floorplan</title>
		<meta name="viewport" content="width=device-width,initial-scale='.$scale.',user-scalable=yes,minimal-ui">
		<meta name="HandheldFriendly" content="true">
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="theme-color" content="#000">
		<link rel="manifest" href="/manifest.json">
		<link rel="shortcut icon" href="images/domoticzphp48.png">
		<link rel="apple-touch-icon" href="images/domoticzphp48.png">
		<link rel="apple-touch-startup-image" href="images/domoticzphp144.png">
		<link rel="stylesheet" type="text/css" href="/styles/floorplan.css">
		<script type="text/javascript" src="/scripts/m4q.min.js"></script>
		<script type="text/javascript" src="/scripts/floorplanjs.js"></script>';
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
	global $d,$time,$lastfetch;
	$lastfetch=$time;
	if ($device=='living') $ip=111;
	elseif ($device=='kamer') $ip=112;
	elseif ($device=='alex') $ip=113;
	$ip=array(
		'living'=>111,
		'kamer'=>112,
		'alex'=>113
	);
	if ($maxpow==false) {
		$maxpow=$d['daikin_kWh']['icon'];
	} else {
		if ($maxpow!=$d['daikin_kWh']['icon']) storeicon('daikin_kWh', $maxpow);
	}
	$url="http://192.168.2.".$ip[$device]."/aircon/set_control_info?pow=$power&mode=$mode&stemp=$stemp&f_rate=$fan&shum=0&f_dir=0";
	file_get_contents($url);
	sleep(2);
	$status=daikinstatus($device);
	if ($d['daikin'.$device]['s']!=$status) store('daikin'.$device, $status, basename(__FILE__).':'.__LINE__.':'.$msg);
	if ($power==0&&$d['daikin'.$device]['m']!=0) storemode('daikin'.$device, 0, basename(__FILE__).':'.__LINE__.':'.$msg);
	elseif ($d['daikin'.$device]['m']!=$mode) storemode('daikin'.$device, $mode, basename(__FILE__).':'.__LINE__.':'.$msg);
	if ($spmode==-1) file_get_contents('http://192.168.2.'.$ip[$device].'/aircon/set_special_mode?set_spmode=1&spmode_kind=2'); // Eco
	elseif ($spmode==0) file_get_contents('http://192.168.2.'.$ip[$device].'/aircon/set_special_mode?set_spmode=0&spmode_kind=1'); // Normal
	elseif ($spmode==1) file_get_contents('http://192.168.2.'.$ip[$device].'/aircon/set_special_mode?set_spmode=1&spmode_kind=1'); // Power
	sleep(2);
	foreach($ip as $k=>$ip) {
		if ($d['daikin'.$k]['m']!=0) {
			if ($maxpow==100) $url='http://192.168.2.'.$ip.'/aircon/set_demand_control?type=1&en_demand=0&mode=0&max_pow=100&scdl_per_day=0&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0';
			else $url='http://192.168.2.'.$ip.'/aircon/set_demand_control?type=1&en_demand=1&mode=0&max_pow='.$maxpow.'&scdl_per_day=0&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0';
//			lg(__LINE__.': '.file_get_contents($url));
			file_get_contents($url);
		}
	}
}
function updatefromdomoticz() {
	global $d,$db,$domoticzurl;
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
				if (isset($d[$name]['s'])&&$status!=$d[$name]['s']) {
					lg('(UPDDOMOTICZ)	'. $name.'	= '.$status,9);
					$query="UPDATE devices SET s=:status WHERE n=:name;";
					$stmt=$db->prepare($query);
					$stmt->execute(array(':status'=>$status, ':name'=>$name));
				}
			}
		}
	}

}
function hass($domain,$service,$entity) {
	lg('HASS '.$domain.' '.$service.' '.$entity,4);
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,'http://192.168.2.19:8123/api/services/'.$domain.'/'.$service);
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/json','Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJlYzFjYTVlOWY0MWE0ZWEwOTY0NjFmM2QzN2QxMjg0MiIsImlhdCI6MTcxMzU0OTY4MCwiZXhwIjoyMDI4OTA5NjgwfQ.TpYxRLRMEezo1Jx4buGYAE6UN0ku6tEuhExc3KetjPw'));
	curl_setopt($ch,CURLOPT_POSTFIELDS,'{"entity_id":"'.$entity.'"}');
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_FRESH_CONNECT,true);
	curl_setopt($ch,CURLOPT_TIMEOUT,5);
	$response=curl_exec($ch);
	if (strlen($response)>0) lg($response);
	curl_close($ch);
}
function hassinput($domain,$service,$entity,$input) {
	lg('HASSinput '.$domain.' '.$service.' '.$entity,4);
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,'http://192.168.2.19:8123/api/services/'.$domain.'/'.$service);
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/json','Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJlYzFjYTVlOWY0MWE0ZWEwOTY0NjFmM2QzN2QxMjg0MiIsImlhdCI6MTcxMzU0OTY4MCwiZXhwIjoyMDI4OTA5NjgwfQ.TpYxRLRMEezo1Jx4buGYAE6UN0ku6tEuhExc3KetjPw'));
	curl_setopt($ch,CURLOPT_POSTFIELDS,'{"entity_id":"'.$entity.'","source":"'.$input.'"}');
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_FRESH_CONNECT,true);
	curl_setopt($ch,CURLOPT_TIMEOUT,5);
	$response=curl_exec($ch);
	if (strlen($response)>0) lg($response);
	curl_close($ch);
}
function hassget() {
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,'http://192.168.2.19:8123/api/states');
	curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/json','Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJlYzFjYTVlOWY0MWE0ZWEwOTY0NjFmM2QzN2QxMjg0MiIsImlhdCI6MTcxMzU0OTY4MCwiZXhwIjoyMDI4OTA5NjgwfQ.TpYxRLRMEezo1Jx4buGYAE6UN0ku6tEuhExc3KetjPw'));
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_FRESH_CONNECT,true);
	curl_setopt($ch,CURLOPT_TIMEOUT,5);
	$response=curl_exec($ch);
	curl_close($ch);
	return $response;
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
function dbconnect($lg='') {
	global $dbname,$dbuser,$dbpass;
	return new PDO("mysql:host=127.0.0.1;dbname=$dbname;",$dbuser,$dbpass,array(PDO::ATTR_PERSISTENT=>true));
}
function fetchdata($t=0,$lg='') {
	global $db,$d;
	if(!isset($db)) $db=dbconnect(basename(__FILE__).':'.__LINE__.'-'.__FUNCTION__);
	if ($t==0) $stmt=$db->query("select n,i,s,t,m,dt,icon from devices;");
	else $stmt=$db->query("select n,i,s,t,m,dt,icon from devices WHERE t>=$t;");
	while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) $d[$row['n']] = $row;

	if ($t==0) lg('fetchdata ALL	'.$lg.(strlen($lg)<15?'		':'	').$stmt->rowCount().' rows');
	else lg('fetchdata '.time()-$t.'	'.$lg.(strlen($lg)<15?'		':'	').$stmt->rowCount().' rows',99);

	$d['dag']=mget('dag');
	$en=mget('en');
	$d['net']=$en['net'];
	$d['avg']=$en['avg'];
	$d['zon']=$en['zon'];
	return $d;
}
function fetchdataidx() {
	global $db;
	if(!isset($db)) $db=dbconnect(basename(__FILE__).':'.__LINE__.'-'.__FUNCTION__);
	$stmt=$db->query("select n,i,s,t,m,dt,icon from devices where i is not null;");
	while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) $d[$row['i']] = $row;
	$d['dag']=mget('dag');
	return $d;
}
function roundUpToAny($n,$x=5) {
	return round(($n+$x/2)/$x)*$x;
}
function roundDownToAny($n,$x=5) {
	return floor($n/$x) * $x;
}
function mset($key, $data, $ttl=0) {
	global $memcache;
	$memcache->set($key, $data);
}
function mget($key) {
	global $memcache;
	$data=$memcache->get($key);
	return $data;
}