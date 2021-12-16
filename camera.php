<?php
/**
 * Pass2PHP
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
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
$data['Weg']=$d['Weg']['s'];
$data['poortrf']=$d['poortrf']['s'];
$data['deurvoordeur']=$d['deurvoordeur']['s'];
$data['tdeurvoordeur']=time()-$d['deurvoordeur']['t'];
if (TIME>=$d['civil_twilight']['s']&&TIME<=$d['civil_twilight']['m']) $data['zonop']=1;
else $data['zonop']=0;
echo serialize($data);
if ($d['zon']['s']==0&&(TIME<$zonop||TIME>$zononder)) {
	sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
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
			if ($d[$name]['s']!=$action||$name=='deurbel') file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx='.$d[$name]['i'].'&switchcmd='.$action);
		} else store($name, $action, $msg);
		if ($name=='denon') {
			if ($action=='Off') storemode('denon', 'UIT', basename(__FILE__).':'.__LINE__);
		}
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
