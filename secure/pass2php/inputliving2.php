<?php
if ($status=='On') {
	if ($d['eettafel']['s']>0) sl('eettafel', 0);
	else {
		if ($time<=strtotime('9:00')) sl('eettafel', 35);
		else sl('eettafel', 75);
}