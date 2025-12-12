<?php
if ($status=='Open'&&$d['auto']['s']=='On') {
	$last=getCache('wc');
	if ($d['time']>$last+5&&$d['wc']['s']!='On') {
		zwave('wc','binary',1,'ON');
		sw('wc', 'On', basename(__FILE__).':'.__LINE__);
	}
	finkom();
}
if ($d['weg']['s']>0) {
	if ($status=='Open') sirene('Deur WC open');
	else sirene('Deur WC dicht');
}
