<?php
require_once '/var/www/html/secure/functions.php';
if (isset($_REQUEST['token'])) {
	$d=fetchdata();
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
	if ($_REQUEST['token']==$doorbellmotiontoken) {
		sw('bureel', 'On', basename(__FILE__).':'.__LINE__);
		if ($dag<2) {
			sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
		}
		if ($d['deurvoordeur']['s']=='Closed') {
			shell_exec('/usr/bin/wget -O /dev/null -o /dev/null "http://192.168.2.11/telegram.php?beweging" > /dev/null 2>/dev/null &');
//			shell_exec('/usr/bin/wget -O /dev/null -o /dev/null "http://192.168.2.11/fifo_command.php?cmd=record%20on%205%2055" > /dev/null 2>/dev/null &');
		}
	} elseif ($_REQUEST['token']==$doorbelltoken) {
		$d=fetchdata();
		$zonop=($d['civil_twilight']['s']+$d['Sun']['s'])/2;
		$zononder=($d['civil_twilight']['m']+$d['Sun']['m'])/2;
		sw('bureel', 'On', basename(__FILE__).':'.__LINE__);
		sw('lamp kast', 'On', basename(__FILE__).':'.__LINE__);
		if ($dag<2) {
			sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
		}

		if ($_SERVER['REQUEST_TIME']>apcu_fetch('ring_ding')+30) {
			apcu_store('ring_ding', $_SERVER['REQUEST_TIME']);
			telegram('DEURBEL', false, 1);
			sw('bureel', 'On', basename(__FILE__).':'.__LINE__);
			if ($d['deurvoordeur']['s']=='Closed') {
				shell_exec('/usr/bin/wget -O /dev/null -o /dev/null "http://192.168.2.11/telegram.php?deurbel" > /dev/null 2>/dev/null &');
				shell_exec('/usr/bin/wget -O /dev/null -o /dev/null "http://192.168.2.11/fifo_command.php?cmd=record%20on%205%2055" > /dev/null 2>/dev/null &');
			}
			if ($d['Weg']['s']==0) {
				rgb('Xlight', 50, 100);
				sl('Xbel', 10, basename(__FILE__).':'.__LINE__);
				if ($d['bose102']['s']=='On'||$d['bose103']['s']=='On'||$d['bose104']['s']=='On'||$d['bose105']['s']=='On'||$d['bose106']['s']=='On'||$d['bose107']['s']=='On') shell_exec('curl -s "http://127.0.0.1/secure/pass2php/belknopbose101.php" > /dev/null 2>/dev/null &');
				if ($d['lgtv']['s']=='On') shell_exec('python3 /var/www/html/secure/lgtv.py -c send-message -a "Deurbel" 192.168.2.6 > /dev/null 2>/dev/null &');
			}
		}
	}
}
//telegram(print_r($_REQUEST, true), true, 1);
