<?php
$past=time()-mget('powermeter');
if ($status<2200&&$d['powermeter']['s']=='On'&&$past>3600) {
	sw('powermeter', 'Off', basename(__FILE__).':'.__LINE__);
	alert('auto','Auto opgeladen, '.$status.'W '.past('powermeter'),600);
}
//if ($status<2220&&$d['powermeter']['s']=='On'&&past('powermeter')>3600) sw('powermeter', 'Off', basename(__FILE__).':'.__LINE__);
//telegram($status);