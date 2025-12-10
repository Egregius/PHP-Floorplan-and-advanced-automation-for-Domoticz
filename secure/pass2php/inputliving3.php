<?php
if ($status=='On') {
	if ($d['zithoek']['s']>0) sl('zithoek', 0, basename(__FILE__).':'.__LINE__);
	else {
		if ($d['lgtv']['s']=='On') sl('zithoek', 5, basename(__FILE__).':'.__LINE__);
		else sl('zithoek', 75, basename(__FILE__).':'.__LINE__);
	}
}