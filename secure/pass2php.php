<?php
require '/var/www/html/secure/functions.php';
$device=$_REQUEST['d'];
$status=$_REQUEST['s'];
if ($device=='winst') {
	echo $status;
	store($device, $status, 'Pass2PHP');
	exit;
} else $d=fetchdata();
echo __LINE__.'<br>';
if (isset($d[$device])) {
	if ($d[$device]['dt']=='luifel') {
		if ($status=='Open') $status=100;
		elseif ($status=='Closed') $status=0;
	} elseif ($d[$device]['dt']=='dimmer'||$d[$device]['dt']=='rollers') {
		exit;
		if ($status=='Off'||$status=='Open') $status=0;
		elseif ($status=='On'||$status=='Closed') $status=100;
		else $status=filter_var($status, FILTER_SANITIZE_NUMBER_INT);
		if ($device=='Xlight') {
			if (!is_int($status)) $status=101;
		}
	} elseif ($device=='achterdeur') {
		if ($status=='Open') $status='Closed';
		else $status='Open';
	} elseif ($device=='sirene') {
		if ($status=='Group On') $status='On';
		else $status='Off';
	} 
} 
if ($device=='buiten_hum') { // 1
	exit;
	$status=explode(';', $status);
	$temp=$status[0];
	$hum=$status[1]+1;
	if ($hum>100) $hum=100;
	if ($status>$d['buiten_temp']['m']+1) $status=$d['buiten_temp']['m']+1;
	elseif ($status<$d['buiten_temp']['m']-1) $status=$d['buiten_temp']['m']-1;
	if($hum!=$d['buiten_temp']['m']) storemode('buiten_temp', $hum);
	if ($temp!=$d['minmaxtemp']['icon']) {
		if ($temp>$d['buiten_temp']['s']+1) $temp=$d['buiten_temp']['s']+1;
		elseif ($temp<$d['buiten_temp']['s']-1) $temp=$d['buiten_temp']['s']-1;
		storeicon('minmaxtemp', $temp);
	}
	exit;
} elseif ($device=='kamer_hum') { // 2
	exit;
	$status=explode(';', $status);
	$status=$status[1]-2;
	if ($status>$d['kamer_temp']['m']+1) $status=$d['kamer_temp']['m']+1;
	elseif ($status<$d['kamer_temp']['m']-1) $status=$d['kamer_temp']['m']-1;
	/*if ($status!=$d['kamer_temp']['m']) */storemode('kamer_temp', $status);
	exit;
} elseif ($device=='alex_hum') { // 3
	exit;
	$status=explode(';', $status);
	$status=$status[1];
	if ($status>$d['alex_temp']['m']+1) $status=$d['alex_temp']['m']+1;
	elseif ($status<$d['alex_temp']['m']-1) $status=$d['alex_temp']['m']-1;
	if ($status!=$d['alex_temp']['m']) storemode('alex_temp', $status);
	exit;
} elseif ($device=='waskamer_hum') { // 4
	exit;
	$status=explode(';', $status);
	$status=$status[1];
	if ($status>$d['waskamer_temp']['m']+1) $status=$d['waskamer_temp']['m']+1;
	elseif ($status<$d['waskamer_temp']['m']-1) $status=$d['waskamer_temp']['m']-1;
	if ($status!=$d['waskamer_temp']['m']) storemode('waskamer_temp', $status);
	exit;
} elseif ($device=='badkamer_hum') { // 5
	exit;
	$status=explode(';', $status);
	$status=$status[1]+1;
	if ($status>$d['badkamer_temp']['m']+1) $status=$d['badkamer_temp']['m']+1;
	elseif ($status<$d['badkamer_temp']['m']-1) $status=$d['badkamer_temp']['m']-1;
	if ($status>100) $status=100;
	if ($status!=$d['badkamer_temp']['m']) storemode('badkamer_temp', $status);
	exit;
} elseif ($device=='living_hum') { // 6
	exit;
	$status=explode(';', $status);
	$status=$status[1]-1;
	if ($status>$d['living_temp']['m']+1) $status=$d['living_temp']['m']+1;
	elseif ($status<$d['living_temp']['m']-1) $status=$d['living_temp']['m']-1;
	if ($status!=$d['living_temp']['m']) storemode('living_temp', $status);
	exit;
}
if (file_exists('/var/www/html/secure/pass2php/'.$device.'.php')) {
/*	$dag=0;
	if ($time>=$d['civil_twilight']['s']&&$time<=$d['civil_twilight']['m']) {
		$dag=1;
		if ($time>=$d['Sun']['s']&&$time<=$d['Sun']['m']) {
			if ($time>=$d['Sun']['s']+900&&$time<=$d['Sun']['m']-900) $dag=4;
			else $dag=3;
		} else {
			$zonop=($d['civil_twilight']['s']+$d['Sun']['s'])/2;
			$zononder=($d['civil_twilight']['m']+$d['Sun']['m'])/2;
			if ($time>=$zonop&&$time<=$zononder) $dag=2;
		}
	}*/
	store($device, $status);
	include '/var/www/html/secure/pass2php/'.$device.'.php';
}
