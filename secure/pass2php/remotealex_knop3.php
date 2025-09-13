<?php
if ($status=='Remote_button_short_press') {
	lg($d['alex']['s']);
	if ($d['alex']['s']>45) sl('alex', 45, basename(__FILE__).':'.__LINE__, true);
	elseif ($d['alex']['s']>1) sl('alex', 1, basename(__FILE__).':'.__LINE__, true);
	else sl('alex', 0, basename(__FILE__).':'.__LINE__, true);
} elseif ($status=='Remote_button_double_press') {
	sl('alex', 0, basename(__FILE__).':'.__LINE__, true);
}
