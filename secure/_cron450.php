<?php
$user='cron450';
//if ($d['wasbak']['s']==0) sl('wasbak', 0, basename(__FILE__).':'.__LINE__, true);

if ($d['regenpomp']['s']=='Off'&&past('regenpomp')>1700&&mget('buien')>$time-14400) sw('regenpomp', 'On', basename(__FILE__).':'.__LINE__);

//sync_devices_if_changed($db, $d);