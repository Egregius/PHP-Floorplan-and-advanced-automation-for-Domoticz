<?php
if ($status=='On') {
	if ($d['inkom']['s']<100) {
		sl('inkom', 100);
	}
	mset('8inkom', time());
}