<?php
if ($status=="Open"&&$d['auto']['s']=='On') {
	if ($d['voordeur']['s']=='Off'&&$d['dag']==0) sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
	elseif ($d['voordeur']['s']=='On'&&$d['zon']>0) sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__);
	finkom();
	if ($d['Weg']['s']==0) {
		$ctx=stream_context_create(array('http'=>array('timeout'=>2)));
		for ($k=1;$k<=3;$k++) {
			file_get_contents('http://192.168.2.12/fifo_command.php?cmd=motion_enable%20off', false, $ctx);
			if ($http_response_header[0]=='HTTP/1.1 200 OK') {
				break;
			}
			sleep($k);
		}
		for ($k=1;$k<=3;$k++) {
			file_get_contents('http://192.168.2.11/fifo_command.php?cmd=motion_enable%20off', false, $ctx);
			if ($http_response_header[0]=='HTTP/1.1 200 OK') {
				break;
			}
			sleep($k);
		}
	}
	$time=time();
	if (mget('ring_ding')>$time-300) {
		if ($d['bureel']['s']!='Off') sw('bureel', 'Off', basename(__FILE__).':'.__LINE__);
		if ($d['lamp kast']['s']!='Off') sw('lamp kast', 'Off', basename(__FILE__).':'.__LINE__);
	}
} else {
	$ctx=stream_context_create(array('http'=>array('timeout'=>2)));
	for ($k=1;$k<=3;$k++) {
		file_get_contents('http://192.168.2.12/fifo_command.php?cmd=motion_enable%20on', false, $ctx);
		if ($http_response_header[0]=='HTTP/1.1 200 OK') {
			break;
		}
		sleep($k);
	}
	for ($k=1;$k<=3;$k++) {
		file_get_contents('http://192.168.2.11/fifo_command.php?cmd=motion_enable%20on', false, $ctx);
		if ($http_response_header[0]=='HTTP/1.1 200 OK') {
			break;
		}
		sleep($k);
	}
	if ($d['Weg']['s']==0) sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__);
}
/*if ($status=='Open') sirene('Voordeur open');
else sirene('Voordeur dicht');*/

