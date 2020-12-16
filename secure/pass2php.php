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
apcu_inc('pass2php_raw');
require '/var/www/html/secure/functions.php';
$device=$_REQUEST['d'];
$status=$_REQUEST['s'];

if (endswith($device, '_Temperature')) die('Ignore these doubles');
elseif (endswith($device, '_Utility')) die('Ignore these doubles');
//elseif ($device=='$ belknop') die('Nothing to do');
apcu_inc('pass2php_net');
apcu_inc($device);

$d=fetchdata();
if ($d[$device]['dt']=='dimmer'||$d[$device]['dt']=='rollers'||$d[$device]['dt']=='luifel') {
	if ($status=='Off'||$status=='Open') {
		$status=0;
	} elseif ($status=='On'||$status=='Closed') {
		$status=100;
	} else {
		$status=filter_var($status, FILTER_SANITIZE_NUMBER_INT);
	}
} elseif (in_array($device, array('badkamer_temp'))) {
	$status=explode(';', $status);
	$status=$status[0];
} elseif ($device=='achterdeur') {
	if ($status=='Open') {
		$status='Closed';
	} else {
		$status='Open';
	}
} elseif ($device=='sirene') {
	if ($status=='Group On') {
		$status='On';
	} else {
		$status='Off';
	}
} elseif ($device=='ringdoorbell') {
	if (past('voordeur')<60) exit;
}
store($device, $status, 'Pass2PHP');
if(@include '/var/www/html/secure/pass2php/'.$device.'.php')apcu_inc('pass2php_effective');