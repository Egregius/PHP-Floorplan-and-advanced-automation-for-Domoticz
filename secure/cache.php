<?php
require 'functions.php';
if (isset($_REQUEST['fetch'])) {
    $d=fetchdata(0, basename(__FILE__).':'.__LINE__.print_r($_SERVER, true));
    echo $d[$_REQUEST['fetch']]['s'];
} elseif (isset($_REQUEST['s'])) {
    $d=fetchdata(0, basename(__FILE__).':'.__LINE__.print_r($_SERVER, true));
    echo $d[$_REQUEST['s']]['s'];
} elseif (isset($_REQUEST['m'])) {
	$d=fetchdata(0, basename(__FILE__).':'.__LINE__.print_r($_SERVER, true));
	if ($_REQUEST['m']=='auto'&&$d['auto']['m']==0) echo 0;
    else echo $d[$_REQUEST['m']]['m'];
} elseif (isset($_REQUEST['store'])&&isset($_REQUEST['value'])) {
	$d=fetchdata(0, basename(__FILE__).':'.__LINE__.print_r($_SERVER, true));
	echo $_REQUEST['store'], $_REQUEST['value'];
    store($_REQUEST['store'], $_REQUEST['value'], basename(__FILE__).':'.__LINE__);
} elseif (isset($_REQUEST['count'])) {
    $d=fetchdata(0, basename(__FILE__).':'.__LINE__.print_r($_SERVER, true));
    $data=$d[$_REQUEST['count']]['s']+1;
    echo $data;
    store($_REQUEST['count'], $data, basename(__FILE__).':'.__LINE__);
} elseif (isset($_REQUEST['zon'])) {
	$en=mget('en');
	echo -$en['zon'];
}