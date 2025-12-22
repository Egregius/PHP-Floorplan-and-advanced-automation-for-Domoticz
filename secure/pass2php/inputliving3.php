<?php
$data=apcu_fetch('inputliving3');
if ($data===false) {
	if ($status=='On') {
		if ($d['zithoek']['s']>0) sl('zithoek', 0);
		else {
			if ($d['lgtv']['s']=='On') sl('zithoek', 5);
			else sl('zithoek', 75);
		}
		apcu_store('inputliving3', 1, 1);
	}
}