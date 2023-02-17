<?php
if ($d['eettafel']['s']==0) {
	sl('eettafel', 16, basename(__FILE__).':'.__LINE__);
} else {
	$new=ceil($d['eettafel']['s']*1.08);
	if ($new>100) $new=100;
	sl('eettafel', $new);
}
if ($d['eettafel']['m']>0) storemode('eettafel', 0, basename(__FILE__).':'.__LINE__);