<?php
if ($status=='On') {
	if ($d['snijplank']['s']<28) {
//		sl('snijplank', 30, basename(__FILE__).':'.__LINE__);
		zwave('snijplank','multilevel',0,30);
	} else {
//		sl('snijplank', 100, basename(__FILE__).':'.__LINE__);
		zwave('snijplank','multilevel',0,100);
	}
}