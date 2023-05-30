<?php
if (!is_array($d)) $d=fetchdata();
if ($status=='Open'&&$d['auto']['s']=='On') {
	if (past('wc')>5&&$d['wc']['s']!='On') {
		sw('wc', 'On', basename(__FILE__).':'.__LINE__);
	}
    finkom();
}
if ($status=='Open') sirene('Deur WC open');
else sirene('Deur WC dicht');