<?php
require '/var/www/config.php';
$user='camera';
$mysqli=new mysqli('localhost', $dbuser, $dbpass, $dbname);
$result = $mysqli->query("select n,i,s,t,m from devices WHERE n in ('Weg', 'poortrf', 'deurvoordeur','civil_twilight', 'Sun', 'zon', 'voordeur');") or trigger_error($mysqli->error." [$sql]");
while ($row = $result->fetch_array()) {
	$d[$row['n']]['i'] = $row['i'];
	$d[$row['n']]['s'] = $row['s'];
	$d[$row['n']]['t'] = $row['t'];
	$d[$row['n']]['m'] = $row['m'];
}
$data=array();
$data['w']=$d['Weg']['s'];
$data['p']=$d['poortrf']['s'];
$data['d']=$d['deurvoordeur']['s'];
$times[]=TIME-$d['deurvoordeur']['t'];
$times[]=TIME-$d['poortrf']['t'];
$times[]=TIME-$d['Weg']['t'];

$data['t']=min($times);
$zonop=($d['civil_twilight']['s']+$d['Sun']['s'])/2;
$zononder=($d['civil_twilight']['m']+$d['Sun']['m'])/2;
if ($d['zon']['s']==0&&(TIME<$zonop||TIME>$zononder)) {
	$data['z']=0;
	sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
} else $data['z']=1;
echo serialize($data);

if ($d['Weg']['s']==0&&$_SERVER['REMOTE_ADDR']=='192.168.2.11') {
	rgb('Xlight', 50, 100);
	sl('Xbel', 10, basename(__FILE__).':'.__LINE__);
	if ($d['bose101']['s']=='On') shell_exec('curl -s "http://127.0.0.1/secure/pass2php/belknopbose101.php" > /dev/null 2>/dev/null &');
	if ($d['lgtv']['s']=='On') shell_exec('python3 /var/www/html/secure/lgtv.py -c send-message -a "Deurbel" 192.168.2.6 > /dev/null 2>/dev/null &');
}

function sw($name,$action='Toggle',$msg='') {
	global $user,$d,$domoticzurl;
	if (!isset($d)) $d=fetchdata();
	$msg=' (SWITCH)'.str_pad($user, 13, ' ', STR_PAD_LEFT).' => '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' => '.$action.' ('.$msg.')';
	if ($d[$name]['i']>0) {
		lg($msg);
		if ($d[$name]['s']!=$action) file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx='.$d[$name]['i'].'&switchcmd='.$action);
	}

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