<?php
if ($status=='On') {
	if ($d['mac']['s']!='On') sw('mac', 'Off', basename(__FILE__).':'.__LINE__, true);
	if ($d['bureellinks']['s']>0||$d['bureel2']['s']>0) {
		sl('bureellinks', 0, basename(__FILE__).':'.__LINE__, true);
	}
}