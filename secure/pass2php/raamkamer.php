<?php
if ($status=='Open'&&$d['daikin']['m']==0&&$d['daikin']['s']=='On') {
	if ($heating<0) daikinset('kamer', 0, 3, 20, basename(__FILE__).':'.__LINE__, 'A', 40);
	else daikinset('kamer', 0, 4, 13, basename(__FILE__).':'.__LINE__, 'A', 40);
}
