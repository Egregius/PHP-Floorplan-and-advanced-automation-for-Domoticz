<?php
/**
 * Pass2PHP
 * php version 7.3.5-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require '/var/www/config.php';
$fetch=true;
require 'functions.php';
$device=$_REQUEST['d'];
$status=$_REQUEST['s'];
$username='Domoticz';
if (endswith($device, '_Temperature')) {
    die('Nothing to do');
} elseif (endswith($device, '_Utility')) {
    die('Nothing to do');
}
if (in_array(
    $device, array(
            'eettafel',
            'zithoek',
            'kamer',
            'tobi',
            'alex',
            'terras',
            'lichtbadkamer',
            'Xvol',
            'Rliving',
            'Rbureel',
            'RkeukenL',
            'RkeukenR',
            'RkamerL',
            'RkamerR',
            'Rtobi',
            'Ralex',
            'luifel'
        )
)
) {
    if ($status=='Off'||$status=='Open') {
        store($device, 0);
    } elseif ($status=='On'||$status=='Closed') {
        store($device, 100);
    } else {
        $status=filter_var($status, FILTER_SANITIZE_NUMBER_INT);
        store($device, $status);
    }
} elseif (in_array($device, array('badkamer_temp'))) {
    $status=explode(';', $status);
    $status=$status[0];
    store($device, $status);
} elseif ($device=='achterdeur') {
    if ($status=='Open') {
        $status='Closed';
    } else {
        $status='Open';
    }
    store($device, $status);
} else {
    store($device, $status);
}
//lgsql('Domoticz', $device, $status);
//lg($device.' = '.$status);
@require 'pass2php/'.$device.'.php';