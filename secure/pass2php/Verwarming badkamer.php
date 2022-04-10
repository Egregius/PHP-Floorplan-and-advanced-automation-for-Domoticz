<?php
lg($status);
if ($d['heating']['s']>=0) {
	if ($status<10) $status=10;
	elseif ($status>21) $status=21;
	if ($status>=10&&$status<=21) {
		store('badkamer_set', $status, basename(__FILE__).':'.__LINE__);
		storemode('badkamer_set', 2, basename(__FILE__).':'.__LINE__);
		if ($d['Verwarming badkamer']['s']!=$status) file_get_contents($domoticzurl.'/json.htm?type=command&param=setsetpoint&idx=3254&setpoint='.$status);
	}
}
lg($status);
