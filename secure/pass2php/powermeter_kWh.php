<?php
$time=time();
$past=$time-mget('powermeter');
if ($status<100&&$d['powermeter']['s']=='On'&&$past>3590) {
	sw('powermeter', 'Off', basename(__FILE__).':'.__LINE__);
	file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx=744&switchcmd=Off&level=0&passcode=');
} elseif ($status>500) {
	file_get_contents($domoticzurl.'/json.htm?type=command&param=setcolbrightnessvalue&idx=744&color={%22m%22:3,%22t%22:0,%22r%22:255,%22g%22:254,%22b%22:25,%22cw%22:0,%22ww%22:0}&brightness=2');
}
