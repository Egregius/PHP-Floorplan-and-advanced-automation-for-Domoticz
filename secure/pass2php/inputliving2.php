<?php
if ($status=='On') {
	if ($d['eettafel']['s']>0) {
		if ($d['time']<=strtotime('9:00')&&$d['eettafel']['s']<35) sl('eettafel', 35, basename(__FILE__).':'.__LINE__); 
		elseif ($d['time']>=strtotime('17:30')&&$d['time']<=strtotime('19:00')&&$d['eettafel']['s']<35) sl('eettafel', 80, basename(__FILE__).':'.__LINE__); 
		else sl('eettafel', 0, basename(__FILE__).':'.__LINE__);
	} else {
		if ($d['time']<=strtotime('9:00')) sl('eettafel', 35, basename(__FILE__).':'.__LINE__);
		else sl('eettafel', 80, basename(__FILE__).':'.__LINE__);
	}
}