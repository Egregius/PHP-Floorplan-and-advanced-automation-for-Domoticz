<?php
if ($d['eettafel']['s']==0) {
	sl('eettafel', 8, basename(__FILE__).':'.__LINE__);
} else {
	sl('eettafel', floor($d['eettafel']['s']*0.94));
}
