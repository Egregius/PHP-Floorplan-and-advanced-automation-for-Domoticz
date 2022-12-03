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
require '/var/www/html/secure/functions.php';
$device=$_REQUEST['d'];
$status=$_REQUEST['s'];

//if (endswith($device, '_Temperature')) exit;
//elseif (endswith($device, '_Utility')) exit;

$d=fetchdata();
if (isset($d[$device])) {
	if ($d[$device]['dt']=='dimmer'||$d[$device]['dt']=='rollers'||$d[$device]['dt']=='luifel') {
		if ($status=='Off'||$status=='Open') {
			$status=0;
		} elseif ($status=='On'||$status=='Closed') {
			$status=100;
		} else {
			$status=filter_var($status, FILTER_SANITIZE_NUMBER_INT);
		}
		if ($device=='Xlight') {
			lg($status);
			if (!is_int($status)) $status=101;
		}
	} elseif ($device=='achterdeur') {
		if ($status=='Open') {
			$status='Closed';
		} else {
			$status='Open';
		}
	} elseif ($device=='kWh_Meter') {
		$status=explode(';', $status);
		$status=$status[0];
		if ($status>5) $status=1;
		else $status=0;
		if ($status==$d['kWh_Meter']['s']) die('exit');
	} elseif ($device=='winst') {
		store($device, $status+$d['winst']['s'], 'Pass2PHP');
		exit;
	} elseif ($device=='sirene') {
		if ($status=='Group On') {
			$status='On';
		} else {
			$status='Off';
		}
	} 
} elseif ($device=='kamer_hum') {
	$status=explode(';', $status);
	storemode('kamer_temp', $status[1]+3);
	exit;
} elseif ($device=='alex_hum') {
	$status=explode(';', $status);
	storemode('alex_temp', $status[1]+3);
	exit;
} elseif ($device=='living_hum') {
	$status=explode(';', $status);
	storemode('living_temp', $status[1]+3);
	exit;
}
if (file_exists('/var/www/html/secure/pass2php/'.$device.'.php')) {
	store($device, $status, 'Pass2PHP');
	include '/var/www/html/secure/pass2php/'.$device.'.php';
} //else lg('			>>>	IGNORING	>>>	'.$device.' = '.$status);
