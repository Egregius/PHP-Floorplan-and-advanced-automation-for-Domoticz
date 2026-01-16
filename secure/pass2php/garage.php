<?php
if ($status=='Off') {
	lg('licht garage uit, zon='.$d['z']);
	if ($d['weg']->s==0&&$d['z']<100&&$d['garageled']->s=='Off') {
		sw('garageled', 'On', basename(__FILE__).':'.__LINE__);
	}
} elseif ($status=='On') {
	if ($d['garageled']->s=='On') {
		sw('garageled','Off', basename(__FILE__).':'.__LINE__);
	}
}
