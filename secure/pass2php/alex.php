<?php
if ($status>0) {
	if ($d['auto']['s']=='On'&&$d['raamalex']['s']=='Closed'&&TIME>=strtotime('7:30')&&TIME<strtotime('10:00')) {
		if ($d['Ralex']['s']>0) {
			sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
			sleep(10);
			sl('alex', 0, basename(__FILE__).':'.__LINE__);
		}
	}
} elseif ($d['alex']['m']!=0) storemode('alex', 0, basename(__FILE__).':'.__LINE__);
