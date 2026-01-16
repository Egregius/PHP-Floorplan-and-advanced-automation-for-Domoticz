<?php
if ($status=='On') {
	if ($d['snijplank']->s>0) {
		sl('snijplank', 0, basename(__FILE__).':'.__LINE__);
	}
}