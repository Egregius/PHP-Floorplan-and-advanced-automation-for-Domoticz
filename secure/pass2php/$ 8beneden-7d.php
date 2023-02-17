<?php
if ($d['eettafel']['s']>0) {
	sl('eettafel', 0);
	if ($d['eettafel']['m']>0) storemode('eettafel', 0, basename(__FILE__).':'.__LINE__);
}