<?php
require '/var/www/config.php';
$dow=date("w");if($dow==0||$dow==6)$weekend=true; else $weekend=false;
$db=dbconnect();
$memcache=new Memcache;
$memcache->connect('192.168.2.21',11211) or die ("Could not connect");
date_default_timezone_set('Europe/Brussels');

function t() {
	global $dow,$t;
	$dow=date("w");
	if ($dow==0||$dow==6) return strtotime('7:45');
	else return strtotime('7:00');
}
function fliving() {
	global $d,$time,$t;
	if ($d['media']['s']=='Off'&&$d['bureel1']['s']=='Off'&&$d['lampkast']['s']!='On'&&$d['eettafel']['s']==0&&$d['zithoek']['s']==0) {
		if (($d['zon']==0&&$d['dag']['s']<-3)||($d['rkeukenl']['s']>80&&$d['rkeukenr']['s']>80&&$d['rbureel']['s']>80&&$d['rliving']['s']>80)) {
			$am=strtotime('10:00');
			if ($d['eettafel']['s']==0&&$time<$am) {
				if ($d['bureel1']['s']<20) sl('bureel1', 20, basename(__FILE__).':'.__LINE__);
				if ($d['bureel2']['s']<20) sl('bureel2', 20, basename(__FILE__).':'.__LINE__);
			}
			if ($d['wasbak']['s']==0&&$time<$am) sl('wasbak', 10, basename(__FILE__).':'.__LINE__);
		}
	}
}
function fgarage() {
	global $d;
	if ($d['zon']<100&&$d['garage']['s']!='On'&&$d['garageled']['s']!='On') sw('garageled', 'On', basename(__FILE__).':'.__LINE__);
}
function fkeuken() {
	global $d;
	if (1==2) {
		if ($d['wasbak']['s']<12) sl('wasbak', 12, basename(__FILE__).':'.__LINE__);
		if ($d['snijplank']['s']<12) sl('snijplank', 12, basename(__FILE__).':'.__LINE__);
	} else {
		if ($d['wasbak']['s']<10&&$d['snijplank']['s']==0&&($d['dag']['s']<-3||$d['rkeukenl']['s']>80)) sl('wasbak', 10, basename(__FILE__).':'.__LINE__);
	}
//	hass('input_button','press','input_button.wakeipad');
}
function finkom($force=false) {
	global $d,$time;
	if (($d['dag']['s']<-4&&$d['weg']['s']==0)||$force==true) {
		if ($d['inkom']['s']<30&&$d['dag']['s']<-2) sl('inkom', 30, basename(__FILE__).':'.__LINE__);
		if ($d['deuralex']['s']=='Open'&&$d['deurkamer']['s']=='Open'&&$time>=strtotime('19:45')&&$time<=strtotime('20:30')) sl('hall', 30, basename(__FILE__).':'.__LINE__);
	}
}
function fhall() {
	global $d,$t,$time;
	if ($d['dag']['s']<-4&&$time<=strtotime('20:45')&&($time>=$t+1800||$d['ralex']['s']==0||$d['deuralex']['s']=='Open'||past('deuralex')<900)) {
		if ($d['hall']['s']<30&&$d['weg']['s']==0&&$d['dag']['s']<-3) {
			sl('hall', 30, basename(__FILE__).':'.__LINE__);
		}
	} else finkom();
	if ($d['weg']['s']==0&&$d['rkamerl']['s']>70&&$d['rkamerr']['s']>70&&$time>=strtotime('21:30')&&$time<=strtotime('22:30')&&$d['kamer']['s']==0&&$d['deurkamer']['s']=='Open'&&past('kamer')>7200) sl('kamer', 1, basename(__FILE__).':'.__LINE__);
}
function huisslapen($weg=false) {
	global $d;
	sl(array('hall','inkom','eettafel','zithoek','bureel','wasbak','snijplank','terras'), 0, basename(__FILE__).':'.__LINE__);
	sw(array('lampkast','kristal','garageled','garage','pirgarage','pirkeuken','pirliving','pirinkom','pirhall','bureel','tuin','zolderg','wc','grohered','kookplaat','steenterras','tuintafel','kerstboom','bosekeuken','boseliving','mac','ipaddock','zetel'), 'Off', basename(__FILE__).':'.__LINE__);
	foreach (array('living_set','alex_set','kamer_set','badkamer_set'/*,'eettafel','zithoek'*/,'luifel') as $i) {
		if ($d[$i]['m']!=0&&$d[$i]['s']!='D') storemode($i, 0, basename(__FILE__).':'.__LINE__);
	}
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
function waarschuwing($msg) {
	telegram($msg, false, 1);
	hassopts('xiaomi_aqara', 'play_ringtone', '', ['gw_mac' => '34ce008d3f60','ringtone_id' => 2,'ringtone_vol' => 60]);
	sw('sirene', 'On', basename(__FILE__).':'.__LINE__);
	store('sirene', 'On', basename(__FILE__).':'.__LINE__);
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
function sl($name,$level,$msg='',$force=false,$temp=0) {
	global $d,$user;
	if (!isset($d)) $d=fetchdata(0, basename(__FILE__).':'.__LINE__);
	if (is_array($name)) {
		foreach ($name as $i) {
			if ($d[$i]['s']!=$level) {
				sl($i, $level, $msg, $force);
			}
		}
	} else {
		lg('(SETLEVEL)	'.str_pad($user, 13, ' ', STR_PAD_LEFT).' => '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' => '.$level.' ('.$msg.')',4);
		if ($temp>0||$d[$name]['s']!=$level||$force==true) {
			if ($temp>0||$d[$name]['dt']=='hd') {
//				lg('[hsw] '.$name.'>'.$level.' '.$msg,4);
				if ($temp==0) {
					if ($d['dag']['s']>12) $temp=3400;
					elseif ($d['dag']['s']>8) $temp=3200;
					elseif ($d['dag']['s']>6) $temp=3000;
					elseif ($d['dag']['s']>2) $temp=2850;
					else $temp=2750;
				}				
				if ($level>0&&$temp==0) hassopts('light','turn_on','light.'.$name,array("brightness_pct"=>$level/*,"color_temp_kelvin"=>$temp*/));
				elseif ($level>0&&$temp>0) hassopts('light','turn_on','light.'.$name,array("brightness_pct"=>$level,"color_temp_kelvin"=>$temp));
				elseif ($level==0) hass('light','turn_off','light.'.$name);
	//			store($name, $level, $msg);
			} elseif ($d[$name]['dt']=='d') {
				if ($level>0) hassopts('light','turn_on','light.'.$name,array("brightness"=>$level*2.55));
				elseif ($level==0) hass('light','turn_off','light.'.$name);
	//			store($name, $level, $msg);
			} elseif ($d[$name]['dt']=='r') {
//				lg(basename(__FILE__).':'.__LINE__);
				hassopts('cover','set_cover_position','cover.'.$name,array("position"=>$level));
//				store($name, $level, $msg);
			} elseif ($d[$name]['dt']=='luifel') {
//				lg(basename(__FILE__).':'.__LINE__);
				hassopts('cover','set_cover_position','cover.'.$name,array("position"=>$level));
//				store($name, $level, $msg);
			}
		}
	}
}
function resetsecurity() {
	global $d;
	if ($d['sirene']['s']!='Off') {
		sw('sirene', 'Off', basename(__FILE__).':'.__LINE__,true);
//		store('sirene', 'Off', basename(__FILE__).':'.__LINE__);
	}
//	foreach (array('SDbadkamer','SDkamer','SDalex','SDwaskamer','SDzolder','SDliving') as $i) {
//		if ($d[$i]['s']!='Off') {
//			store($i, 'Off', basename(__FILE__).':'.__LINE__);
//		}
//	}
}
function sw($name,$action='Toggle',$msg='',$force=false) {
	global $d,$user,$db;
	if (!isset($d)) $d=fetchdata(0, basename(__FILE__).':'.__LINE__);
	if (is_array($name)) {
		foreach ($name as $i) {
			if ($d[$i]['s']!=$action) {
				sw($i, $action, $msg, $force);
				usleep(300000);
			}
		}
	} else {
		$msg='(SWITCH)'.str_pad($user, 13, ' ', STR_PAD_LEFT).' => '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' => '.$action.' ('.$msg.')';
		if ($d[$name]['s']!=$action||$force==true) {
			if ($d[$name]['dt']=='hsw') {
				if ($action=='Toggle') {
					if ($d[$name]['s']=='On') $action='Off';
					else $action='On';
				}
				lg('[hsw] '.$msg,4);
				if ($action=='On') hass('switch','turn_on','switch.'.$name);
				elseif ($action=='Off') hass('switch','turn_off','switch.'.$name);
//				store($name, $action, $msg);
			} else {
				store($name, $action, $msg);
			}
		}
	}
}
function setpoint($name, $value,$msg='') {
	global $d,$user,$db;
//	if (!isset($d[$name]['i'])) $d=fetchdata(0, basename(__FILE__).':'.__LINE__);
	$msg='(SETPOINT)'.str_pad($user, 13, ' ', STR_PAD_LEFT).' => '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' => '.$value.' ('.$msg.')';
	store($name, $value, $msg);
}
function store($name='',$status='',$msg='',$update=null) {
	global $db, $user;
	$time=time();
	if ($update>0) $db->query("UPDATE devices SET s='$status',t='$time' WHERE n='$name'");
	else $db->query("INSERT INTO devices (n,s,t) VALUES ('$name','$status','$time') ON DUPLICATE KEY UPDATE s='$status',t='$time';");
	if ($name=='') lg('(STORE) '.str_pad($user??'', 9, ' ', STR_PAD_LEFT).' => '.str_pad($idx??'', 13, ' ', STR_PAD_RIGHT).' => '.$status.(strlen($msg>0)?'	('.$msg.')':''),10);
	else {
		if (endswith($name, '_temp')) return;
		elseif(endswith($name, '_kWh')) return;
		elseif(endswith($name, '_hum')) return;
		else lg('(STORE) '.str_pad($user??'', 9, ' ', STR_PAD_LEFT).' => '.str_pad($name??'', 13, ' ', STR_PAD_RIGHT).' => '.$status.(strlen($msg>0)?'	('.$msg.')':''),10);
	}
}
function storemode($name,$mode,$msg='',$update=null) {
	global $db, $user, $time;
	$time=time();
	if ($update>0) $db->query("UPDATE devices SET m='$mode',t='$time' WHERE n='$name'");
	else $db->query("INSERT INTO devices (n,m,t) VALUES ('$name','$mode','$time') ON DUPLICATE KEY UPDATE m='$mode',t='$time';");
	lg('(STOREMODE) '.str_pad($user, 9, ' ', STR_PAD_LEFT).' => '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' => '.$mode.(strlen($msg>0)?'	('.$msg.')':''),10);
}
function storeicon($name,$icon,$msg='',$update=null) {
	global $d, $db, $user, $time;
	if (!isset($d[$name]['icon'])||(isset($d[$name]['icon'])&&$d[$name]['icon']!=$icon)) {
		if (isset($d['time'])) $time=$d['time'];
		else $time=time();
		if ($update>0) $db->query("UPDATE devices SET icon='$icon',t='$time' WHERE n='$name'");
		else $db->query("INSERT INTO devices (n,icon,t) VALUES ('$name','$icon','$time') ON DUPLICATE KEY UPDATE icon='$icon',t='$time';");
		if (endswith($name, '_temp')) return;
		lg('(STOREICON)	'.$user.'	=> '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' => '.$icon.(strlen($msg>0)?'	('.$msg.')':''),10);
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
//	if ($d['weg']['s']<=1) {

//		foreach (array(/*'ralex',*/'rkamerl','rkeukenl','rkamerr','rwaskamer','rliving','rkeukenr','rbureel') as $i) {
//			if ($d[$i]['s']>0) sl($i, 1, basename(__FILE__).':'.__LINE__);
//		}
//		if ($d['zon']<200) {
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
	lg('Telegram sent: '.$msg);
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
	static $inLg = false;
	if ($inLg) return; // voorkomt recursie

	$inLg = true;
	global $d;
	if (isset($d['auto']['m'])) {
		$loglevel = $d['auto']['m'];
	} else $loglevel = 0;

	if ($level <= $loglevel) {
		$fp = fopen('/temp/domoticz.log', "a+");
		$time = microtime(true);
		$dFormat = "Y-m-d H:i:s";
		$mSecs = $time - floor($time);
		$mSecs = substr(number_format($mSecs, 3), 1);
		fwrite($fp, sprintf("%s%s %s\n", date($dFormat), $mSecs, $msg));
		fclose($fp);
	}

	$inLg = false;
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
		if ($vol>=50) bosebass(-5, $ip);
		elseif ($vol>=40) bosebass(-6, $ip);
		elseif ($vol>=30) bosebass(-7, $ip);
		elseif ($vol>=20) bosebass(-8, $ip);
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
	return $preset;
}
function bosezone($ip,$forced=false,$vol='') {
	global $d,$time,$dow,$weekend;
	$time=time();
	$t=t();
	$playlist=boseplaylist();
	$map = [
		'EDM-1' => 'PRESET_1',
		'EDM-2' => 'PRESET_2',
		'EDM-3' => 'PRESET_3',
		'MIX-1' => 'PRESET_4',
		'MIX-2' => 'PRESET_5',
		'MIX-3' => 'PRESET_6',
	];
	if (($d['weg']['s']<=1&&$d['bose101']['m']==1)||$forced===true) {
		if ($d['weg']['s']==0&&($d['lgtv']['s']!='On'||$forced===true)&&$d['bose101']['s']=='Off'&&$time<strtotime('21:00')&&$d['boseliving']['s']=='On'&&past('boseliving')>60) {
			sw('bose101', 'On', basename(__FILE__).':'.__LINE__);
			bosekey($map[$playlist], 750000, 101, basename(__FILE__).':'.__LINE__);
			lg('Bose zone time='.$time.'|'.$t+1800);
			if ($d['lgtv']['s']=='On'&&$d['eettafel']['s']==0) bosevolume(0, 101, basename(__FILE__).':'.__LINE__);
			elseif ($time<$t+1800) bosevolume(8, 101, basename(__FILE__).':'.__LINE__);
			else bosevolume(20, 101, basename(__FILE__).':'.__LINE__);
		}
		if ($ip>101) {
			if ($d['bose'.$ip]['s']=='Off') sw('bose'.$ip, 'On', basename(__FILE__).':'.__LINE__);
			$map = [
				102 => '<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.102">304511BC3CA5</member></zone>',
				103 => '<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.103">C4F312F65070</member></zone>',
				104 => '<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.104">C4F312DCE637</member></zone>',
				105 => '<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.105">587A628BB5C0</member></zone>',
				106 => '<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.106">C4F312F89670</member></zone>',
				107 => '<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.107">B0D5CC065C20</member></zone>',
			];
			if ($d['bose101']['s']=='Off'&&$d['bose'.$ip]['s']=='Off') {
				sw('bose101', 'On', basename(__FILE__).':'.__LINE__);
				bosekey($map[$playlist], 750000, 101, basename(__FILE__).':'.__LINE__);
				if ($d['lgtv']['s']=='On'&&$d['eettafel']['s']==0) bosevolume(0, 101, basename(__FILE__).':'.__LINE__);
				elseif ($time<strtotime('7:00')) bosevolume(14, 101, basename(__FILE__).':'.__LINE__);
				else bosevolume(22, 101, basename(__FILE__).':'.__LINE__);
				usleep(100000);
				bosepost('setZone', $map[$ip], 101);
				if ($vol=='') {
					if ($time>strtotime('6:00')&&$time<strtotime('20:00')) bosevolume(22, $ip, basename(__FILE__).':'.__LINE__);
					else bosevolume(22, $ip, basename(__FILE__).':'.__LINE__);
				} else bosevolume($vol, $ip, basename(__FILE__).':'.__LINE__);
			} elseif ($d['bose'.$ip]['s']=='Off') {
				bosepost('setZone',  $map[$ip], 101);
				store('bose'.$ip, 'On');
				if ($vol=='') {
					if ($time>strtotime('6:00')&&$time<strtotime('21:00')) bosevolume(22, $ip, basename(__FILE__).':'.__LINE__);
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
		telegram($msg.' om '.date("G:i:s", $time), false, 3);
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
		$maxpow=$d['daikin_kwh']['icon'];
	} else {
		if ($maxpow!=$d['daikin_kwh']['icon']) storeicon('daikin_kwh', $maxpow);
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
function hasstoken() {
	global $user;
	switch ($user) {
		case 'cron10': return 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiI2YmQ0NDZjNTgyZTY0NDU5YTkxNmE4ZThmZDVhNWFjNCIsImlhdCI6MTc0OTMxODM1NywiZXhwIjoyMDY0Njc4MzU3fQ.S6oPTz8PrEChIU2Ogx4qFgcCBLzKy8tLeFKA_NfDbH8';
		case 'cron60': return 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJiZTc4NDhiOGNhMmY0OTIyODQxOGJkMDAyYmJkMDM0YyIsImlhdCI6MTc0OTMxOTM3NSwiZXhwIjoyMDY0Njc5Mzc1fQ.gn-THiHH1yf_CugxLoqNvbeftRxW_CsLJ2lPWt5c2Ro';
		case 'cron120': return 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiIxYTJjOWFhZTY0MGU0YzQ5OTNmNjE0YWIxN2ZlMzQwZCIsImlhdCI6MTc1MDE1MTk0MywiZXhwIjoyMDY1NTExOTQzfQ.zJCD7rfqxahxaHnyYBGjKtJG_a0KYp7VRNUtJXTpLB0';
		case 'cron180': return 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiI4YjU2NzRmMzg0YWQ0MjAyYjJkYzdhMzM3MzhiNjRjZiIsImlhdCI6MTc1MDE1MjAwOCwiZXhwIjoyMDY1NTEyMDA4fQ.IExPw1wFOJkeULKXEMi9mtwbaRahuuNnwQQhSdBA0Co';
		case 'cron240': return 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiI1ODUzNjYwODdlZDM0ZTA5ODhlNDY4MmUxNDRjMDdlMiIsImlhdCI6MTc1MDE1MjIwOSwiZXhwIjoyMDY1NTEyMjA5fQ.NEzqm8yxL_zOKczCEHJb5NuWWopjEPJAqKqtuxw5KuE';
		case 'cron300': return 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiIzNTE5NjQ1Zjk5NzY0MDcxYjIyODU3Mzg2YmQ3NWIzYiIsImlhdCI6MTc1MDE1MjI1NSwiZXhwIjoyMDY1NTEyMjU1fQ.eMWEEwlxDQL-t4xhpqwenJ1xZh8Ct44vQ1f5_5RB-UU';
		case 'cron450': return 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJhYzVhMjNjMmMxYTg0MjQ3OThhMDMyNWM5ZmJhMGJhYiIsImlhdCI6MTc1MDE1MjMwNCwiZXhwIjoyMDY1NTEyMzA0fQ.QLYZJix2wURu9EA-QuWmfPEEKqagBfopgX2rMoALzoc';
		case 'cron3600': return 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiI2MjliNzVmZTY3ZDc0MWI0YmM3NDc2ZDA5ODQzNTEyOCIsImlhdCI6MTc1MDE1MjM2NCwiZXhwIjoyMDY1NTEyMzY0fQ.76X_fwqF1JVeZKN6Vrv-H7DrzGQ2NJnIQbIr7yCHCrI';
		case 'heating': return 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJjNDk4ZDU1MTA2ZWI0MWJkYWE3ZWZjNTMwMmEyYzg3NiIsImlhdCI6MTc1MDE1MjQxMCwiZXhwIjoyMDY1NTEyNDEwfQ.kKqGJU4ALE6_HMQ5c4kwtcW8IeOVhhBc4Spg3lmheJs';
		case 'Guy': return 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJmMTQ1ZThmNjYyNTk0Mjk5OWM2ZTUyMWNhZWY3MTUxYSIsImlhdCI6MTc0ODQwMDM0OCwiZXhwIjoyMDYzNzYwMzQ4fQ.SDUxztRFwr9p7w29LQ-_fDa5l4KB1cOTrz_riHQCFlY';
		case 'Kirby': return 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiIxNGM2YmJhY2EwMzY0NzYwOTI4Y2VhMjdjZDVjOWEwNCIsImlhdCI6MTc1MDE1MjQ2MSwiZXhwIjoyMDY1NTEyNDYxfQ.IrQG72soNQcprvzDwKajkuQnmG-kULIiBS35sKLDxsI';
		default: return 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJjODYzYTllZGY2OGI0ZTc4YjFkOGFkOWQ4YzM3MDRhMiIsImlhdCI6MTc1MDE1MjUwOCwiZXhwIjoyMDY1NTEyNTA4fQ.U-t5m66b9sx7QCWVXEStmt6AIcSN0zbSHHKnR13zEu0';
	}
}
function hass($domain, $service, $entity = '', $target = []) {
	lg('HASS '.$domain.' '.$service.' '.$entity,4);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://192.168.2.26:8123/api/services/' . $domain . '/' . $service);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		'Content-Type: application/json',
		'Authorization: Bearer ' . hasstoken()
	]);

	$data = [];
	if ($entity != '') $data['entity_id'] = $entity;
	if (!empty($target)) $data['target'] = $target;

	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);

	$response = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	if ($httpcode != 200) {
		lg("HASS API ERROR $httpcode: $response", 1);
	}
	curl_close($ch);
}
function hassopts($domain, $service, $entity = '', $data = []) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://192.168.2.26:8123/api/services/'.$domain.'/'.$service);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		'Content-Type: application/json',
		'Authorization: Bearer ' . hasstoken()
	]);
	if (!empty($entity)) {
		$data['entity_id'] = $entity;
	}
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_exec($ch);
	curl_close($ch);
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
function hassrepublishEntityState($entityId) {
    $ha_url = 'http://192.168.2.26:8123';
    $token = 'Bearer '.hasstoken();
    $base_topic = 'homeassistant';

    // 1. Status ophalen
    $ch = curl_init("$ha_url/api/states/$entityId");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: $token"]);
    $result = curl_exec($ch);
    curl_close($ch);
	echo $result;
    $data = json_decode($result, true);
    if (!isset($data['state'])) {
        echo "Ongeldige entity of fout in API\n";
        return;
    }

    $state = $data['state'];
    $parts = explode('.', $entityId); // bv: switch.airco_living
    if (count($parts) != 2) {
        echo "Ongeldige entity_id structuur\n";
        return;
    }

    list($domain, $object_id) = $parts;
    $topic = "$base_topic/$domain/$object_id/state";

    // 2. Publish via HA API (mqtt.publish)
    $payload = [
        'topic' => $topic,
        'payload' => $state,
        'retain' => true
    ];

    $ch = curl_init("$ha_url/api/services/mqtt/publish");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: $token",
        "Content-Type: application/json"
    ]);
    $result = curl_exec($ch);
    curl_close($ch);

    echo "Status van $entityId opnieuw gepubliceerd als '$state' op $topic\n";
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
	global $db, $dbname, $dbuser, $dbpass;
	static $db = null;

	try {
		if ($db === null) {
			$db = new PDO("mysql:host=127.0.0.1;dbname=$dbname;", $dbuser, $dbpass, [
				PDO::ATTR_PERSISTENT => true,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			]);
		} else {
			$db->query('SELECT 1');
		}
	} catch (PDOException $e) {
		if ($e->getCode() == 2006) { // MySQL server has gone away
			lg('dbconnect	'.__LINE__.'	|Verbinding verbroken, opnieuw verbinden');
			$db = null;
			return dbconnect(); // opnieuw proberen
		} else {
			lg('dbconnect	'.__LINE__.'	|PDO fout: '.$e->getMessage());
			throw $e;
			exit;
		}
	}
	return $db;
}
function fetchdata($t=0,$lg='') {
	global $d;
	$db=dbconnect();
	if ($t==0) $stmt=$db->query("select n,s,t,m,dt,icon from devices;");
	else $stmt=$db->query("select n,s,t,m,dt,icon from devices WHERE t>=$t;");
	while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
		if(!is_null($row['s']))$d[$row['n']]['s']=$row['s'];
		if(!is_null($row['t']))$d[$row['n']]['t']=$row['t'];
		if(!is_null($row['m']))$d[$row['n']]['m']=$row['m'];
		if(!is_null($row['dt']))$d[$row['n']]['dt']=$row['dt'];
		if(!is_null($row['icon']))$d[$row['n']]['icon']=$row['icon'];
	}

	if ($t==0) lg('fetchdata ALL	'.$lg.(strlen($lg)<15?'		':'	').$stmt->rowCount().' rows');
	else lg('fetchdata '.time()-$t.'	'.$lg.(strlen($lg)<15?'		':'	').$stmt->rowCount().' rows',99);
	$en=mget('en');
	if (isset($en['net'])) {
		$d['net']=$en['net'];
		$d['avg']=$en['avg'];
		$d['zon']=-$en['zon'];
	} else {
		$d['net']=0;
		$d['avg']=0;
		$d['zon']=0;
	}
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
function isPDOConnectionAlive($pdo) {
    try {
        $pdo->query("SELECT 1");
        return true;
    } catch (PDOException $e) {
        return false;
    }
}
function isoToLocalTimestamp(string $isoTime): int {
    // ISO tijd is in UTC, zet om naar timestamp
    $utc = new DateTime($isoTime, new DateTimeZone("UTC"));
    $utc->setTimezone(new DateTimeZone(date_default_timezone_get()));
    return $utc->getTimestamp();
}