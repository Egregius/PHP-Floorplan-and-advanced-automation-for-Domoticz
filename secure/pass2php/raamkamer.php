<?php
if ($status=='Open'&&$d['daikin']['m']==0&&$d['daikin']['s']=='On') {
	if ($d['heating']['s']<0) daikinset('kamer', 0, 3, 20, basename(__FILE__).':'.__LINE__, 'A', 50);
	else daikinset('kamer', 0, 4, 10, basename(__FILE__).':'.__LINE__, 'A', 50);
}
