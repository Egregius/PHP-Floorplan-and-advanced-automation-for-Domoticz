<?php
if ($status>0) {
	$time=time();
	if ($d['auto']['s']=='On'&&$d['raamalex']['s']=='Closed'&&$time>=strtotime('7:30')&&$time<strtotime('10:00')&&$d['dag']>0) {
		if ($d['Ralex']['s']>0) {
			sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Rliving']['s']>0&&$d['Weg']['s']==0) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Rwaskamer']['s']>0) sl('Rwaskamer', 0, basename(__FILE__).':'.__LINE__);
			sleep(10);
			sl('alex', 0, basename(__FILE__).':'.__LINE__);
		}
	}
}