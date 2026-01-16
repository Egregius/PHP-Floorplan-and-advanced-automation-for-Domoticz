<?php
if ($status=='On') {
	if ($d['inkom']->s>30) $level=$d['inkom']->s-10;
	else $level=0;
	sl('inkom', $level, basename(__FILE__).':'.__LINE__);
	setCache('8inkom', $time);
}