<?php
if ($status=='On') {
	include('$ miniliving4l.php');
} else {
	if ($d['lgtv']['s']!='Off') {
		shell_exec('python3 secure/lgtv.py -c off '.$lgtvip);
		sw('lgtv', 'Off', basename(__FILE__).':'.__LINE__);
	}
}
