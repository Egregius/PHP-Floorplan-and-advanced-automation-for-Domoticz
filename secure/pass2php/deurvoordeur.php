<?php
/**
 * Pass2PHP
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($status=="Open"&&$d['auto']['s']=='On') {
	$zonop=($d['civil_twilight']['s']+$d['Sun']['s'])/2;
	$zononder=($d['civil_twilight']['m']+$d['Sun']['m'])/2;
	if ($d['voordeur']['s']=='Off'&&$d['zon']['s']==0&&(TIME<$zonop||TIME>$zononder)) sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
	elseif ($d['voordeur']['s']=='On'&&$d['zon']['s']>0) sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__);
	finkom();
	if ($d['Weg']['s']==0) {
		for ($k=1;$k<=60;$k++) {
			file_get_contents('http://192.168.2.12/fifo_command.php?cmd=motion_enable%20off');
			if ($http_response_header[0]=='HTTP/1.1 200 OK') {
				break;
			}
			sleep($k);
		}
		for ($k=1;$k<=60;$k++) {
			file_get_contents('http://192.168.2.11/fifo_command.php?cmd=motion_enable%20off');
			if ($http_response_header[0]=='HTTP/1.1 200 OK') {
				break;
			}
			sleep($k);
		}
	}
} else {
	for ($k=1;$k<=60;$k++) {
		file_get_contents('http://192.168.2.12/fifo_command.php?cmd=motion_enable%20on');
		if ($http_response_header[0]=='HTTP/1.1 200 OK') {
			break;
		}
		sleep($k);
	}
	for ($k=1;$k<=60;$k++) {
		file_get_contents('http://192.168.2.11/fifo_command.php?cmd=motion_enable%20on');
		if ($http_response_header[0]=='HTTP/1.1 200 OK') {
			break;
		}
		sleep($k);
	}
}
if ($d['Xlight']['s']>0) sw('Xlight', 'Off', basename(__FILE__).':'.__LINE__);
/*if ($status=='Open') sirene('Voordeur open');
else sirene('Voordeur dicht');*/

