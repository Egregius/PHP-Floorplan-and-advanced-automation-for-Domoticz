<?php
/**
 * Pass2PHP
 * php version 7.2.15
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require dirname(__DIR__) . '/secure/functions.php';
if (isset($_REQUEST['fetch'])) {
    echo $d[$_REQUEST['fetch']]['s'];
} elseif (isset($_REQUEST['store'])&&isset($_REQUEST['value'])) {
    if ($_REQUEST['store']=='nas') {
        if ($d['lgtv']['s']=='On') {
            if ($_REQUEST['value']=='On') {
                shell_exec('python3 lgtv.py -c send-message -a "NAS Opgestart" 192.168.2.27 > /dev/null 2>&1 &');
            } elseif ($_REQUEST['value']=='Off') {
                shell_exec('python3 lgtv.py -c send-message -a "NAS Uitgeschakeld" 192.168.2.27 > /dev/null 2>&1 &');
            }
        }
    }
    store($_REQUEST['store'], $_REQUEST['value']);
} elseif (isset($_REQUEST['count'])) {
    $data=$d[$_REQUEST['count']]['s']+1;
    echo $data;
    store($_REQUEST['count'], $data);
}