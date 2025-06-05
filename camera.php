<?php
require '/var/www/config.php';
if (isset($_GET['token'])&&$_GET['token']==$cameratoken) {
	$memcache=new Memcache;
	$memcache->connect('192.168.2.21',11211) or die ("Could not connect");
	$user='camera';
	$mysqli=new mysqli('localhost', $dbuser, $dbpass, $dbname);
	$result = $mysqli->query("select n,s,t,m from devices WHERE n in ('weg', 'auto', 'poortrf', 'deurvoordeur', 'voordeur');") or trigger_error($mysqli->error." [$sql]");
	while ($row = $result->fetch_array()) {
		$d[$row['n']]['i'] = $row['i'];
		$d[$row['n']]['s'] = $row['s'];
		$d[$row['n']]['t'] = $row['t'];
		$d[$row['n']]['m'] = $row['m'];
	}
	$data=array();
	$data['w']=$d['weg']['s'];
	$data['p']=$d['poortrf']['s'];
	if ($d['auto']=='Off') $data['p']='Open';
	$data['d']=$d['deurvoordeur']['s'];
	$times[]=TIME-$d['deurvoordeur']['t'];
	$times[]=TIME-$d['poortrf']['t'];
	$times[]=TIME-$d['weg']['t'];

	$data['t']=min($times);
	if ($d['weg']['s']==0&&$data['t']>90&&!isset($_GET['eufy'])) {
		$memcache=new Memcache;
		$memcache->connect('192.168.2.21',11211) or die ("Could not connect");
		$prev=mget('bewegingvoordeur');
		if ($prev<$_SERVER['REQUEST_TIME']-600) {
			file_get_contents('http://127.0.0.1:8080/json.htm?type=command&param=switchlight&idx=749&switchcmd=Set%20Level&level=2');
			file_get_contents('http://127.0.0.1:8080/json.htm?type=command&param=switchlight&idx=747&switchcmd=Set%20Level&level=20&passcode=');
		}
		mset('bewegingvoordeur', $_SERVER['REQUEST_TIME']);
	}
	if (mget('dag')<2) {
		$data['z']=0;
		sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
	} else $data['z']=1;
	echo serialize($data);
} elseif (isset($_GET['token'])&&$_GET['token']==$cameratoken.'b') {
	$user='camera';
	$mysqli=new mysqli('localhost', $dbuser, $dbpass, $dbname);
	$result = $mysqli->query("select n,s from devices WHERE n ='weg';") or trigger_error($mysqli->error." [$sql]");
	while ($row = $result->fetch_array()) {
		$d[$row['n']]['s'] = $row['s'];
	}
	$data=array();
	$data['w']=$d['weg']['s'];
	echo serialize($data);
} else echo '403 Access denied';

function sw($name,$action='Toggle',$msg='',$force=false) {
	global $d,$user,$db;
	if (!isset($d)) $d=fetchdata(0, basename(__FILE__).':'.__LINE__);
	if (is_array($name)) {
		foreach ($name as $i) {
			if ($d[$i]['s']!=$action) {
				sw($i, $action, $msg);
				usleep(300000);
			}
		}
	} else {
		$msg='(SWITCH)'.str_pad($user, 13, ' ', STR_PAD_LEFT).' => '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' => '.$action.' ('.$msg.')';
		if ($d[$name]['s']!=$action||$force==true) {
			if ($d[$name]['dt']=='hsw'||$d[$name]['dt']=='sw') {
				lg('[hsw] '.$msg,4);
				if ($action=='On') hass('switch','turn_on','switch.'.$name);
				elseif ($action=='Off') hass('switch','turn_off','switch.'.$name);
				store($name, $action, $msg);
			} else {
				store($name, $action, $msg);
			}
		}
	}
}
function hass($domain,$service,$entity,$opts=null) {
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,'http://192.168.2.26:8123/api/services/'.$domain.'/'.$service);
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/json','Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJmMTQ1ZThmNjYyNTk0Mjk5OWM2ZTUyMWNhZWY3MTUxYSIsImlhdCI6MTc0ODQwMDM0OCwiZXhwIjoyMDYzNzYwMzQ4fQ.SDUxztRFwr9p7w29LQ-_fDa5l4KB1cOTrz_riHQCFlY'));
	if ($opts==null) $data='{"entity_id":"'.$entity.'"}';
	else $data='{"entity_id":"'.$entity.'",'.$opts.'}';
	curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
	curl_setopt($ch,CURLOPT_TIMEOUT,5);
	$response=curl_exec($ch);
	curl_close($ch);
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

function mset($key, $data, $ttl=0) {
	global $memcache;
	$memcache->set($key, $data);
}
function mget($key) {
	global $memcache;
	$data=$memcache->get($key);
	return $data;
}