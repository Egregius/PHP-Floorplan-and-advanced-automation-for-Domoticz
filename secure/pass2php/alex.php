<?php
if ($status>0) {
	if ($d['auto']['s']=='On'&&$d['raamalex']['s']=='Closed'&&$d['time']>=strtotime('7:30')&&$d['time']<strtotime('10:00')&&$d['dag']['s']>0) {
		if ($d['ralex']['s']>0) {
			sl('ralex', 0, basename(__FILE__).':'.__LINE__);
			if ($d['rliving']['s']>0&&$d['weg']['s']==0&&$d['time']<=strtotime('9:00')) sl('rliving', 0, basename(__FILE__).':'.__LINE__);
			if ($d['rwaskamer']['s']>0) sl('rwaskamer', 0, basename(__FILE__).':'.__LINE__);
			sleep(10);
			sl('alex', 0, basename(__FILE__).':'.__LINE__);
		}
	}
}