<?php
if ($status=='On'&&$d['deurvoordeur']['s']=='Closed') {
	if ($d['dag']['s']<0) sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
	if ($d['time']>mget('ring_ding')+30) {
		if ($d['deurvoordeur']['s']=='Closed') {
			mset('ring_ding', $d['time']);
			telegram('DEURBEL', false, 1);
			shell_exec('/usr/bin/wget -O /dev/null -o /dev/null "http://192.168.2.11/telegram.php?deurbel" > /dev/null 2>/dev/null &');
			shell_exec('/usr/bin/wget -O /dev/null -o /dev/null "http://192.168.2.11/fifo_command.php?cmd=record%20on%205%2055" > /dev/null 2>/dev/null &');
			if ($d['weg']['s']==0) {
				sl('xbel', 10, basename(__FILE__).':'.__LINE__);
				if (($d['bose102']['s']=='On'||$d['bose103']['s']=='On'||$d['bose104']['s']=='On'||$d['bose105']['s']=='On'||$d['bose106']['s']=='On'||$d['bose107']['s']=='On')&&$d['pirliving']['s']=='Off') shell_exec('curl -s "http://127.0.0.1/secure/pass2php/belknopbose101.php" > /dev/null 2>/dev/null &');
				if ($d['bureel']['s']=='Off'&&$d['weg']['s']==0) sw('bureel', 'On', basename(__FILE__).':'.__LINE__);
				if ($d['lamp kast']['s']=='Off'&&$d['weg']['s']==0) {
					sw('lamp kast', 'On', basename(__FILE__).':'.__LINE__);
					$d['lamp kast']['s']='On';
				}
			}
		}
	}
}