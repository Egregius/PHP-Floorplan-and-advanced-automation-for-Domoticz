<?php
if ($status=='On') {
	sw('mac', 'Off', basename(__FILE__).':'.__LINE__, true);
	if ($d['bureel1']['s']>0||$d['bureel2']['s']>0) {
		sl('bureel', 0, basename(__FILE__).':'.__LINE__, true);
	}
}