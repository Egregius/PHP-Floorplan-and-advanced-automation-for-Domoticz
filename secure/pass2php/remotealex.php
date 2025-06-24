<?php
if ($status=='On') {
	lg($d['alex']['s']);
	if ($d['alex']['s']==0) sl('alex', 1, basename(__FILE__).':'.__LINE__, true);
	else sl('alex', 45, basename(__FILE__).':'.__LINE__, true);
} elseif ($status=='Off') {
	sl('alex', 0, basename(__FILE__).':'.__LINE__, true);
}
