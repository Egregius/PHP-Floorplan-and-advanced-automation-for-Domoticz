<?php
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
	echo $_REQUEST['store'], $_REQUEST['value'];
    store($_REQUEST['store'], $_REQUEST['value'], basename(__FILE__).':'.__LINE__);
    if ($_REQUEST['store']=='imac') {
    	if ($_REQUEST['value']=='On') {
    		if ($d['studiodisplay']['s']!='On') {
//    			sw('studiodisplay', 'On', basename(__FILE__).':'.__LINE__);
    		}
    	} else {
    		if ($d['studiodisplay']['s']=='On') {
//    			sw('studiodisplay', 'Off', basename(__FILE__).':'.__LINE__);
    		}
    	}
    }
} elseif (isset($_REQUEST['count'])) {
    $data=$d[$_REQUEST['count']]['s']+1;
    echo $data;
    store($_REQUEST['count'], $data, basename(__FILE__).':'.__LINE__);
}