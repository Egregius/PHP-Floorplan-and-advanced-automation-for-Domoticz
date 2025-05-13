<?php
if ($status=='On') {
	if ($d['alex']['s']==0) sl('alex', 1, basename(__FILE__).':'.__LINE__, true);
	else sl('alex', 8, basename(__FILE__).':'.__LINE__, true);
} else {
	sl('alex', 0, basename(__FILE__).':'.__LINE__, true);
}
