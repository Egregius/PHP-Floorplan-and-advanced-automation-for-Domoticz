<?php
if ($d['zon']['s']==0) {
	sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
} else {
	if ($d['Rliving']['s']>32) {
		sl('Rliving', 32, basename(__FILE__).':'.__LINE__);
	} else {
		if ($d['Rliving']['s']>0) {
			sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		}
	}
}