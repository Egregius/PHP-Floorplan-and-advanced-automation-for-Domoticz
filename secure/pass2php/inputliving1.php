<?php
if ($d['bureellinks']['s']==0) {
	sl('bureellinks', 50, basename(__FILE__).':'.__LINE__);
	if ($d['time']<=strtotime('5:00')&&$d['time']<=strtotime('17:00')&&$d['mac']['s']=='Off') sw('mac', 'On', basename(__FILE__).':'.__LINE__);
} else sl('bureellinks', 0, basename(__FILE__).':'.__LINE__);