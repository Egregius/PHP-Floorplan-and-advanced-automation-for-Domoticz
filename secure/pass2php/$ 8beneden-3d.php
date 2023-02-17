<?php
if ($d['eettafel']['s']<100) {
	sl('eettafel', 100);
	if ($d['eettafel']['m']>0) storemode('eettafel', 0, basename(__FILE__).':'.__LINE__);
}