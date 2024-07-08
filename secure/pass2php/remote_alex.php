<?php
if ($status=='On') {
	if ($d['alex']['s']==0) sl('alex', 1, basename(__FILE__).':'.__LINE__);
	else sl('alex', 5, basename(__FILE__).':'.__LINE__);
} else {
	if ($d['alex']['s']>1) sl('alex', 1, basename(__FILE__).':'.__LINE__);
	else sw('alex', 'Off', basename(__FILE__).':'.__LINE__);
}
