<?php
if ($status=='On') {
	if ($d['mac']['s']=='Off') sw('mac', 'On', basename(__FILE__).':'.__LINE__, true);
	if ($d['dag']['s']<1&&$d['bureellinks']['s']<24) {
		sl('bureellinks', 24, basename(__FILE__).':'.__LINE__, true);
	} elseif ($d['bureellinks']['s']>0) {
		sl('bureellinks', 0, basename(__FILE__).':'.__LINE__, true);
	}
}