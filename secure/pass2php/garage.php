<?php
if ($status=='Off') {
	$time=time();
	if ($d['Weg']['s']==0&&($d['zon']<100||$time<strtotime('8:30')||$time>strtotime('21:00'))&&$d['garageled']['s']=='Off') {
		sw('garageled', 'On', basename(__FILE__).':'.__LINE__);
	}
} elseif ($status=='On') {
	if ($d['garageled']['s']=='On') {
		sw('garageled','Off', basename(__FILE__).':'.__LINE__);
	}
}
