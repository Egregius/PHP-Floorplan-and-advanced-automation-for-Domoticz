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
require '/var/www/html/secure/functions.php';
$device=$_REQUEST['d'];
$status=$_REQUEST['s'];

if (endswith($device, '_Temperature')) die('Nothing to do');
elseif (endswith($device, '_Utility')) die('Nothing to do');
elseif ($device=='$ belknop') die('Nothing to do');

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
}
store($device, $status, 'Pass2PHP');
echo $device.'	'.$status.PHP_EOL;
@include '/var/www/html/secure/pass2php/'.$device.'.php';