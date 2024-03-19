<?php
if ($status<2220&&$d['powermeter']['s']=='On'&&past('powermeter')>3600) {
	sw('powermeter', 'Off', basename(__FILE__).':'.__LINE__);
	alert('auto','Auto opgeladen',900);
}
//if ($status<2220&&$d['powermeter']['s']=='On'&&past('powermeter')>3600) sw('powermeter', 'Off', basename(__FILE__).':'.__LINE__);
//telegram($status);