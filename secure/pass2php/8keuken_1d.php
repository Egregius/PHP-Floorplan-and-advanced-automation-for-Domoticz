<?php
if ($status=='On') {
	if ($d['wasbak']->s<100) {
		sl('wasbak', 100, basename(__FILE__).':'.__LINE__, true);
	}
}