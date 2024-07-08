<?php
$past=time()-mget('powermeter');
if ($status<100&&$d['powermeter']['s']=='On'&&$past>3590) {
	sw('powermeter', 'Off', basename(__FILE__).':'.__LINE__);
//	if (past('Weg')>100) alert('auto','Auto opgeladen, '.$status.'W '.strftime("%k:%M", $past),600);
}
