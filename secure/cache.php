<?php
require 'functions.php';
if (isset($_REQUEST['zon'])) {
	$en=json_decode(getCache('en'),true);
	echo $en['z'];
} else {
	if (isset($_REQUEST['fetch'])) {
		$d=fetchdata();
		echo $d[$_REQUEST['fetch']]->s;
	} elseif (isset($_REQUEST['s'])) {
		$d=fetchdata();
		if ($_REQUEST['s']=='boseliving') {
			if ($d['boseliving']->m==1) $data['status']='Off';
			else {
				$data['status']=$d['boseliving']->s;
				if($d['boseliving']->s=='On') $data['shuffle']=true;
				else $data['shuffle']=false;
			}
			$data['Ontime']=past('boseliving');
			if($d['weg']->s>1) $data['shuffle']=true;
			echo json_encode($data);
		} else echo $d[$_REQUEST['s']]->s;
	} elseif (isset($_REQUEST->m)) {
		$d=fetchdata();
		if ($_REQUEST->m=='auto'&&$d['auto']->m==0) echo 0;
		else echo $d[$_REQUEST->m]->m;
	} elseif (isset($_REQUEST['store'])&&isset($_REQUEST['value'])) {
		$d=fetchdata();
		$d['time']=time();
		echo $_REQUEST['store'], $_REQUEST['value'];
		store($_REQUEST['store'], $_REQUEST['value'], basename(__FILE__).':'.__LINE__);
//		if ($_REQUEST['store']=='nas'&&$_REQUEST['value']=='On') {
//			hass('backup','create_automatic');
//		}
	} elseif (isset($_REQUEST['count'])) {
		$d=fetchdata();
		$data=$d[$_REQUEST['count']]->s+1;
		echo $data;
		store($_REQUEST['count'], $data, basename(__FILE__).':'.__LINE__);
	}
}