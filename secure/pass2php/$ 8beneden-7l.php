<?php
if ($d['eettafel']['s']>0) {
	sl('eettafel', floor($d['eettafel']['s']*0.95));
}
if ($d['eettafel']['m']>0) storemode('eettafel', 0, basename(__FILE__).':'.__LINE__);