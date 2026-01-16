<?php
if ($status=="Open"&&$d['auto']->s=='On') {
	finkom();
	fliving();
}
if ($d['weg']->s>0) {
	if ($status=='Open') sirene('Deur inkom open');
	else sirene('Deur inkom dicht');
}
