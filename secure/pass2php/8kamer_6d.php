<?php
if ($status=='On') {
	$item='rkamerr';
	if ($d[$item]['s']<100) sl($item, 100, basename(__FILE__).':'.__LINE__);
}