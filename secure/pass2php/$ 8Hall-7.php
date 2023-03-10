<?php
if ($status=='On') {
	if ($d['alex']['s']==0) sl('alex', 1, basename(__FILE__).':'.__LINE__);
	else sl('alex', 100, basename(__FILE__).':'.__LINE__);
	if ($d['Ralex']['s']<70&&TIME>strtotime('19:00')) {
		sl('Ralex', 100);
	}
}
