<?php
if ($status=='On') {
	if ($d['eettafel']['s']>0) sl('eettafel', 0, basename(__FILE__).':'.__LINE__);
	else {
		if ($d['time']<=strtotime('9:00')) sl('eettafel', 35, basename(__FILE__).':'.__LINE__);
		else sl('eettafel', 75, basename(__FILE__).':'.__LINE__);
	}
}