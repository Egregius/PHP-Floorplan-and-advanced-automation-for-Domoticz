<?php
if ($status=="Paused") {
	fkeuken();
} elseif ($status=="Playing") {
	if ($d['wasbak']['s']>0&&$time>strtotime('20:00')) {
		sl('wasbak', 0, basename(__FILE__).':'.__LINE__);
		sleep(1);
		sl('wasbak', 0, basename(__FILE__).':'.__LINE__);
	}
}
