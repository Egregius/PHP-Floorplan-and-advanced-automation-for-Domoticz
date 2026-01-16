<?php
if ($status=='On') {
	$item='rwaskamer';
	if ($d['heating']->m>=0) sl($item, 100, basename(__FILE__).':'.__LINE__, true);
	else sl($item, 82, basename(__FILE__).':'.__LINE__, true);
}
