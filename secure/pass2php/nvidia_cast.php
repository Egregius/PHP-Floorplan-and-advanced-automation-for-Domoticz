<?php
if ($d['auto']['s']=='On') {
	if ($status=='Playing'&&$d['eettafel']['s']==0) {
		if ($d['wasbak']['s']>0) sl('wasbak', 0, basename(__FILE__).':'.__LINE__);
		if ($d['zithoek']['s']>0) sl('zithoek', 0, basename(__FILE__).':'.__LINE__);
		if ($d['snijplank']['s']>0) sl('snijplank', 0, basename(__FILE__).':'.__LINE__);
	}/* elseif ($status=='Paused'||$status=='Idle') {
		if (($d['zon']==0&&$d['dag']['s']<0)||($d['rkeukenl']['s']>80&&$d['rkeukenr']['s']>80&&$d['rbureel']['s']>80&&$d['rliving']['s']>80)) {
			//if ($d['wasbak']['s']==0) sl('wasbak', 4, basename(__FILE__).':'.__LINE__);
			if ($d['zithoek']['s']==0) {
				sl('zithoek4', 10, basename(__FILE__).':'.__LINE__);
				//sl('zithoek2', 10, basename(__FILE__).':'.__LINE__);
			}
		}
	}*/
}