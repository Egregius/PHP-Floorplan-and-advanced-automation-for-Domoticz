<?php
if ($status=='On') {
	if ($d['waskamer']['s']<12) sl('waskamer', 12, basename(__FILE__).':'.__LINE__, true);
	else sl('waskamer', 30, basename(__FILE__).':'.__LINE__, true);
}
