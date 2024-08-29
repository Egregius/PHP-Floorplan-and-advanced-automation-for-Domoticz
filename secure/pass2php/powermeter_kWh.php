<?php
$past=time()-mget('powermeter');
if ($status<100&&$d['powermeter']['s']=='On'&&$past>3590) {
	sw('powermeter', 'Off', basename(__FILE__).':'.__LINE__);
}
