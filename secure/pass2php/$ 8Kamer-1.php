<?php
if ($status=='On') {
	$item='RkamerL';
	if ($d[$item]['s']>0) {
		sl($item, 0, basename(__FILE__).':'.__LINE__);
	}
}
