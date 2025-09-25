<?php
$user='cron450';

if ($d['regenpomp']['s']=='Off'&&past('regenpomp')>1700&&mget('buien')>$time-14400) sw('regenpomp', 'On', basename(__FILE__).':'.__LINE__);

republishmqtt();