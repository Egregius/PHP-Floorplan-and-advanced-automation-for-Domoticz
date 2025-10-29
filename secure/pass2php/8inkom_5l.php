<?php
if ($status=='On') {
	if ($d['inkom']['s']>0) {
		sl('inkom', floor($d['inkom']['s']*0.95));
	}
	setCache('8inkom', time());
}