<?php
$ctx=stream_context_create(array('http'=>array('timeout'=>1)));
if ($status=='On') {
	store('Weg', 0, basename(__FILE__).':'.__LINE__);
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
} else {
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
}
