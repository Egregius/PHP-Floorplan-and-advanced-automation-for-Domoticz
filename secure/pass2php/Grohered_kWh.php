<?php
if ($status<100&&$d['GroheRed']['s']=='On'&&$d['net']>0) {
	if (past('GroheRed')>300) sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
	if ($xlight!=false) {
		file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx=744&switchcmd=Off&level=0&passcode=');
		$xlight=false;
	}
} elseif ($status>500) {
	if ($xlight!=true) {
		file_get_contents($domoticzurl.'/json.htm?type=command&param=setcolbrightnessvalue&idx=744&color={%22m%22:3,%22t%22:0,%22r%22:255,%22g%22:0,%22b%22:0,%22cw%22:0,%22ww%22:0}&brightness=1');
		$xlight=true;
	}
} elseif ($status<100&&$d['GroheRed']['s']=='On') {
	if ($xlight!=true) {
		file_get_contents($domoticzurl.'/json.htm?type=command&param=setcolbrightnessvalue&idx=744&color={%22m%22:3,%22t%22:0,%22r%22:0,%22g%22:255,%22b%22:8,%22cw%22:0,%22ww%22:0}&brightness=1');
		$xlight=true;
	}
}