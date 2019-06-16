<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require '/var/www/config.php';
require 'functions.php';
$device=$_REQUEST['d'];
$status=$_REQUEST['s'];
$username='pass2php';
if (endswith($device, '_Temperature')) {
    die('Nothing to do');
} elseif (endswith($device, '_Utility')) {
    die('Nothing to do');
}
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
}
store($device, $status);
@require 'pass2php/'.$device.'.php';