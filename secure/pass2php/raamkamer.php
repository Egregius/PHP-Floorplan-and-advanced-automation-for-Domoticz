<?php
if ($status=='Open'&&$d['daikin']['m']==0&&$d['daikin']['s']=='On') {
	if ($d['heating']['s']<0) daikinset('kamer', 0, 3, 20, basename(__FILE__).':'.__LINE__, 'A');
	else daikinset('kamer', 0, 4, 10, basename(__FILE__).':'.__LINE__, 'A');
	if ($d['kamer_set']['m']!=0) storemode('kamer_set', 0, basename(__FILE__).':'.__LINE__);
}
