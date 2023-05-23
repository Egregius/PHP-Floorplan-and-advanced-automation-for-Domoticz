<?php
exit;
require '/var/www/html/secure/functions.php';
$device=$_REQUEST['d'];
$status=$_REQUEST['s'];

$d=fetchdata();
if (isset($d[$device])) {
	if ($d[$device]['dt']=='luifel') {
		if ($status=='Open') $status=100;
		elseif ($status=='Closed') $status=0;
	} elseif ($d[$device]['dt']=='dimmer'||$d[$device]['dt']=='rollers') {
		if ($status=='Off'||$status=='Open') $status=0;
		elseif ($status=='On'||$status=='Closed') $status=100;
		else $status=filter_var($status, FILTER_SANITIZE_NUMBER_INT);
		if ($device=='Xlight') {
			if (!is_int($status)) $status=101;
		}
	} elseif ($device=='achterdeur') {
		if ($status=='Open') $status='Closed';
		else $status='Open';
	} elseif ($device=='winst') {
		store($device, $status+$d['winst']['s'], 'Pass2PHP');
		exit;
	} elseif ($device=='sirene') {
		if ($status=='Group On') $status='On';
		else $status='Off';
	} 
} 
if ($device=='buiten_hum') { // 1
	$status=explode(';', $status);
	$temp=$status[0];
	$hum=$status[1]+3;
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
	$status=explode(';', $status);
	$status=$status[1]+3;
	if ($status>$d['kamer_temp']['m']+1) $status=$d['kamer_temp']['m']+1;
	elseif ($status<$d['kamer_temp']['m']-1) $status=$d['kamer_temp']['m']-1;
	/*if ($status!=$d['kamer_temp']['m']) */storemode('kamer_temp', $status);
	exit;
} elseif ($device=='alex_hum') { // 3
	$status=explode(';', $status);
	$status=$status[1]+5;
	if ($status>$d['alex_temp']['m']+1) $status=$d['alex_temp']['m']+1;
	elseif ($status<$d['alex_temp']['m']-1) $status=$d['alex_temp']['m']-1;
	if ($status!=$d['alex_temp']['m']) storemode('alex_temp', $status);
	exit;
} elseif ($device=='waskamer_hum') { // 4
	$status=explode(';', $status);
	$status=$status[1]+5;
	if ($status>$d['waskamer_temp']['m']+1) $status=$d['waskamer_temp']['m']+1;
	elseif ($status<$d['waskamer_temp']['m']-1) $status=$d['waskamer_temp']['m']-1;
	if ($status!=$d['waskamer_temp']['m']) storemode('waskamer_temp', $status);
	exit;
} elseif ($device=='badkamer_hum') { // 5
	$status=explode(';', $status);
	$status=$status[1]+7;
	if ($status>$d['badkamer_temp']['m']+1) $status=$d['badkamer_temp']['m']+1;
	elseif ($status<$d['badkamer_temp']['m']-1) $status=$d['badkamer_temp']['m']-1;
	if ($status>100) $status=100;
	if ($status!=$d['badkamer_temp']['m']) storemode('badkamer_temp', $status);
	exit;
} elseif ($device=='living_hum') { // 6
	$status=explode(';', $status);
	$status=$status[1]+5;
	if ($status>$d['living_temp']['m']+1) $status=$d['living_temp']['m']+1;
	elseif ($status<$d['living_temp']['m']-1) $status=$d['living_temp']['m']-1;
	if ($status!=$d['living_temp']['m']) storemode('living_temp', $status);
	exit;
}
if (file_exists('/var/www/html/secure/pass2php/'.$device.'.php')) {
	$dag=0;
	if (TIME>=$d['civil_twilight']['s']&&TIME<=$d['civil_twilight']['m']) {
		$dag=1;
		if (TIME>=$d['Sun']['s']&&TIME<=$d['Sun']['m']) {
			if (TIME>=$d['Sun']['s']+900&&TIME<=$d['Sun']['m']-900) $dag=4;
			else $dag=3;
		} else {
			$zonop=($d['civil_twilight']['s']+$d['Sun']['s'])/2;
			$zononder=($d['civil_twilight']['m']+$d['Sun']['m'])/2;
			if (TIME>=$zonop&&TIME<=$zononder) $dag=2;
		}
	}
	store($device, $status);
	include '/var/www/html/secure/pass2php/'.$device.'.php';
}
