<?php
if ($status=='On') {
	sl('alex', 8, basename(__FILE__).':'.__LINE__);
	storemode('alex', 1, basename(__FILE__).':'.__LINE__);
	if ($d['Ralex']['s']<70) {
		sl('Ralex', 100);
	}
}
