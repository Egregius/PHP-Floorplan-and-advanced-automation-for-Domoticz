<?php
if ($status=='Open'&&$d['auto']['s']=='On') {
	if (past('wc')>5&&$d['wc']['s']=='Off') {
		sw('wc', 'On');
	}
    finkom();
    fliving();
}
if ($status=='Open') sirene('Deur WC open');
else sirene('Deur WC dicht');