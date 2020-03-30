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
require 'functions.php';
$d=fetchdata();
if (isset($_REQUEST['fetch'])) {
    echo $d[$_REQUEST['fetch']]['s'];
} elseif (isset($_REQUEST['s'])) {
    echo $d[$_REQUEST['s']]['s'];
} if (isset($_REQUEST['m'])) {
	if ($_REQUEST['m']=='auto'&&$d['auto']['m']==0) echo 0;
    else echo $d[$_REQUEST['m']]['m'];
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
    store($_REQUEST['store'], $_REQUEST['value'], basename(__FILE__).':'.__LINE__);
} elseif (isset($_REQUEST['count'])) {
    $data=$d[$_REQUEST['count']]['s']+1;
    echo $data;
    store($_REQUEST['count'], $data, basename(__FILE__).':'.__LINE__);
} elseif (isset($_REQUEST['refresh'])) {
	$nodes=json_decode(
		file_get_contents(
			$domoticzurl.'/json.htm?type=openzwavenodes&idx='.$zwaveidx
		),
		true
	);
	lg(print_r($nodes, true));
/*	for ($x=0;$x<15;$x++) {
		zwaveCommand($_REQUEST['refresh'], 'Refresh');
		usleep(500000);
		lg('refresh '.$_REQUEST['refresh']);
	}
*/
}