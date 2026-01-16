<?php
if ($status=='On') {
	if ($d['waskamer']->s<14) sl('waskamer', 14, basename(__FILE__).':'.__LINE__, true);
	else sl('waskamer', 30, basename(__FILE__).':'.__LINE__, true);
}
