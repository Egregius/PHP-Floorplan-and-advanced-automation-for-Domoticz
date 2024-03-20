<?php
if ($status=='On'&&$d['auto']=='On') {
	if ($d['dag']<2&&$d['voordeur']['s']=='Off') sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
	if ($d['deurvoordeur']['s']=='Closed'&&$d['poort']['s']=='Closed') {
		shell_exec('/usr/bin/wget -O /dev/null -o /dev/null "http://192.168.2.11/telegram.php?eufy" > /dev/null 2>/dev/null &');
	}
}