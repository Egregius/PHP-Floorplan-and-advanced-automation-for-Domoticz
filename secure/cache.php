<?php
/**
 * Pass2PHP
 * php version 7.3.3-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require '/var/www/config.php';
require 'functions.php';
if (isset($_REQUEST['fetch'])) {
    echo $d[$_REQUEST['fetch']]['s'];
} elseif (isset($_REQUEST['store'])&&isset($_REQUEST['value'])) {
    if ($_REQUEST['store']=='nas') {
        if ($d['lgtv']['s']=='On') {
            if ($_REQUEST['value']=='On') {
                shell_exec('python3 lgtv.py -c send-message -a "NAS Opgestart" '.$lgtvip.' > /dev/null 2>&1 &');
            } elseif ($_REQUEST['value']=='Off') {
                shell_exec('python3 lgtv.py -c send-message -a "NAS Uitgeschakeld" '.$lgtvip.' > /dev/null 2>&1 &');
            }
        }
    }
    store($_REQUEST['store'], $_REQUEST['value']);
} elseif (isset($_REQUEST['count'])) {
    $data=$d[$_REQUEST['count']]['s']+1;
    echo $data;
    store($_REQUEST['count'], $data);
}