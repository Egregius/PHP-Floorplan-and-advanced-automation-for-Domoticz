<?php
$user=basename(__FILE__);
//lg($user);
foreach (array('buiten', 'living', 'badkamer', 'kamer', 'waskamer', 'alex', 'zolder') as $i) {
	if ($d[$i.'_temp']['m']==1&&past($i.'_temp')>21600) storemode($i.'_temp', 0, basename(__FILE__).':'.__LINE__);
}
//updatefromdomoticz();