<?php
if ($status=='On') {
	if ($d['eettafel']['s']<100) {
		if ($d['eettafel']['s']==0) $d['eettafel']['s']=1;
		$new=ceil($d['eettafel']['s']*1.05);
		if ($new>100) $new=100;
		sl('eettafel', $new);
	}
}