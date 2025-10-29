<?php
if ($status!=$d['deurwc']['s']) {
	if ($status=='Open'&&$d['auto']['s']=='On') {
		$last=mget('wc');
		$time=time();
		if ($time>$last+5&&$d['wc']['s']!='On') {
			sw('wc', 'On', basename(__FILE__).':'.__LINE__);
		}
		finkom();
	}
	if ($d['Weg']['s']>0) {
		if ($status=='Open') sirene('Deur WC open');
		else sirene('Deur WC dicht');
	}
}