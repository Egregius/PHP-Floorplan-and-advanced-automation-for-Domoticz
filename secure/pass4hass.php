<?php
if (isset($_POST['d'],$_POST['a'])&&$_SERVER['REMOTE_ADDR']=='192.168.2.19'&&$_SERVER['HTTP_CONTENT_TYPE']=='application/x-www-form-urlencoded') {
	require '/var/www/html/secure/functions.php';
	$d=fetchdata();
	dag();
	$time=time();
	$device=$_POST['d'];
	$action=$_POST['a'];
	if ($device=='eufy') {
		if ($action=='motion') {
			if ($d['bureel']['s']=='Off') sw('bureel', 'On', basename(__FILE__).':'.__LINE__);
			if ($dag<2) sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
			if ($d['deurvoordeur']['s']=='Closed') shell_exec('/usr/bin/wget -O /dev/null -o /dev/null "http://192.168.2.11/telegram.php?beweging" > /dev/null 2>/dev/null &');
		} elseif ($action=='doorbell') {
			if ($d['bureel']['s']=='Off'&&$d['Weg']['s']==0) sw('bureel', 'On', basename(__FILE__).':'.__LINE__);
			if ($d['lamp kast']['s']=='Off'&&$d['Weg']['s']==0) {
				sw('lamp kast', 'On', basename(__FILE__).':'.__LINE__);
				$d['lamp kast']['s']='On';
			} else sw('lamp kast', 'Off', basename(__FILE__).':'.__LINE__);
			if ($dag<2) sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
			if ($_SERVER['REQUEST_TIME']>mget('ring_ding')+30) {
				mset('ring_ding', $time);
				telegram('DEURBEL', false, 1);
				if ($d['Weg']['s']==0) sw('bureel', 'On', basename(__FILE__).':'.__LINE__);
				if ($d['deurvoordeur']['s']=='Closed') {
					shell_exec('/usr/bin/wget -O /dev/null -o /dev/null "http://192.168.2.11/telegram.php?deurbel" > /dev/null 2>/dev/null &');
					shell_exec('/usr/bin/wget -O /dev/null -o /dev/null "http://192.168.2.11/fifo_command.php?cmd=record%20on%205%2055" > /dev/null 2>/dev/null &');
				}
				if ($d['Weg']['s']==0) {
					sl('Xbel', 10, basename(__FILE__).':'.__LINE__);
					if ($d['bose102']['s']=='On'||$d['bose103']['s']=='On'||$d['bose104']['s']=='On'||$d['bose105']['s']=='On'||$d['bose106']['s']=='On'||$d['bose107']['s']=='On') shell_exec('curl -s "http://127.0.0.1/secure/pass2php/belknopbose101.php" > /dev/null 2>/dev/null &');
				}
			}
			if ($d['lamp kast']['s']=='On') {
				sleep(2);
				sw('lamp kast', 'On', basename(__FILE__).':'.__LINE__);
			}
		}
	} elseif ($device=='kodi') {
		if ($action=='paused') fkeuken();
		elseif ($action=='play') {
			if ($d['wasbak']['s']>0&&$time>strtotime('20:00')) {
				sl('wasbak', 0, basename(__FILE__).':'.__LINE__);
				sleep(1);
				sl('wasbak', 0, basename(__FILE__).':'.__LINE__);
			}
		}
	} elseif (str_starts_with($device,'plug')) {
		store($device, $action, 'From hass');
	} else telegram(print_r($_POST, true));
}