<?php
/**
 * Pass2PHP
 * php version 7.3
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
if ($status=='On') {
	store('Weg', 0, basename(__FILE__).':'.__LINE__);
for ($k=1;$k<=60;$k++) {
		file_get_contents('http://192.168.2.13/fifo_command.php?cmd=motion_enable%20off');
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
} else {
	for ($k=1;$k<=60;$k++) {
		file_get_contents('http://192.168.2.13/fifo_command.php?cmd=motion_enable%20on');
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
