<?php
if ($status=='On') {
	if ($d['zithoek']['s']>0) sl('zithoek', 0);
	else {
		if ($d['lgtv']['s']=='On') sl('zithoek', 5);
		else sl('zithoek', 75);
	}
}