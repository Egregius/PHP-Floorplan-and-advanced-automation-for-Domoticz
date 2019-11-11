<?php
if (isset($status)&&$status==0) {
	if ($d['Ralex']['m']>0) storemode('Ralex', 0, basename(__FILE__).':'.__LINE__);
	if ($d['picam2plug']['s']=='On') {
		file_get_contents('http://192.168.2.12/fifo_command.php?cmd=halt');
		sleep(1);
		file_get_contents('http://192.168.2.12/fifo_command.php?cmd=halt');
		sleep(10);
		sw('picam2plug', 'Off', basename(__FILE__).':'.__LINE__);
	}
    
}
if (isset($_REQUEST['status'])&&$_REQUEST['status']==0) {
	require '../functions.php';
	$d=fetchdata();
	if ($d['picam2plug']['s']=='On') {
		file_get_contents('http://192.168.2.12/fifo_command.php?cmd=halt');
		sleep(1);
		file_get_contents('http://192.168.2.12/fifo_command.php?cmd=halt');
		sleep(10);
		sw('picam2plug', 'Off', basename(__FILE__).':'.__LINE__);
	}
}