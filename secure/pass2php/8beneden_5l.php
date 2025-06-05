<?php
if ($status=='On') {
	if ($d['eettafel']['s']>0) {
		sl('eettafel', floor($d['eettafel']['s']*0.95));
	}
}