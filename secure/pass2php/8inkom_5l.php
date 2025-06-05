<?php
if ($status=='On') {
	if ($d['inkom']['s']>0) {
		sl('inkom', floor($d['inkom']['s']*0.95));
	}
	mset('8inkom', time());
}