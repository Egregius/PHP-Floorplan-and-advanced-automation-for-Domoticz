<?php
require 'functions.php';
if (isset($_REQUEST['zon'])) {
	$en=mget('en');
	echo -$en['zon'];
} else {
	$d=fetchdata(0, basename(__FILE__).':'.__LINE__);
	if (isset($_REQUEST['fetch'])) {
		echo $d[$_REQUEST['fetch']]['s'];
	} elseif (isset($_REQUEST['s'])) {
		echo $d[$_REQUEST['s']]['s'];
	} if (isset($_REQUEST['m'])) {
		if ($_REQUEST['m']=='auto'&&$d['auto']['m']==0) echo 0;
		else echo $d[$_REQUEST['m']]['m'];
	} elseif (isset($_REQUEST['store'])&&isset($_REQUEST['value'])) {
		echo $_REQUEST['store'], $_REQUEST['value'];
		store($_REQUEST['store'], $_REQUEST['value'], basename(__FILE__).':'.__LINE__);
		if ($_REQUEST['store']=='nas'&&$_REQUEST['value']=='On') {
			hass('backup','create_automatic');
		}
	} elseif (isset($_REQUEST['count'])) {
		$data=$d[$_REQUEST['count']]['s']+1;
		echo $data;
		store($_REQUEST['count'], $data, basename(__FILE__).':'.__LINE__);
	}
}