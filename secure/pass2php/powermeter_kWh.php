<?php
$past=time()-mget('powermeter');
if ($status<1500&&$d['powermeter']['s']=='On'&&$past>3590) {
	sw('powermeter', 'Off', basename(__FILE__).':'.__LINE__);
	alert('auto','Auto opgeladen, '.$status.'W '.$past,600);
}
