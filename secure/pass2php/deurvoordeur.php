<?php
if (!is_array($d)) $d=fetchdata();
if ($status=="Open"&&$d['auto']['s']=='On') {
	if ($d['voordeur']['s']=='Off'&&$d['zon']['s']==0&&$d['dag']==0) sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
	elseif ($d['voordeur']['s']=='On'&&$d['zon']['s']>0) sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__);
	finkom();
	if ($d['Weg']['s']==0) {
		for ($k=1;$k<=5;$k++) {
			file_get_contents('http://192.168.2.12/fifo_command.php?cmd=motion_enable%20off');
			if ($http_response_header[0]=='HTTP/1.1 200 OK') {
				break;
			}
			sleep($k);
		}
		for ($k=1;$k<=5;$k++) {
			file_get_contents('http://192.168.2.11/fifo_command.php?cmd=motion_enable%20off');
			if ($http_response_header[0]=='HTTP/1.1 200 OK') {
				break;
			}
			sleep($k);
		}
	}
	if ($d['bureel']['s']!='Off') sw('bureel', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['lamp kast']['s']!='Off') sw('lamp kast', 'Off', basename(__FILE__).':'.__LINE__);
} else {
	for ($k=1;$k<=5;$k++) {
		file_get_contents('http://192.168.2.12/fifo_command.php?cmd=motion_enable%20on');
		if ($http_response_header[0]=='HTTP/1.1 200 OK') {
			break;
		}
		sleep($k);
	}
	for ($k=1;$k<=5;$k++) {
		file_get_contents('http://192.168.2.11/fifo_command.php?cmd=motion_enable%20on');
		if ($http_response_header[0]=='HTTP/1.1 200 OK') {
			break;
		}
		sleep($k);
	}
}
/*if ($status=='Open') sirene('Voordeur open');
else sirene('Voordeur dicht');*/

