<?php
if ($d['heating']['s']>=0) {
	$s=(int)strftime("%S", TIME);
	$dow=date("w");
	if($dow==0||$dow==6) $t=strtotime('7:30');
	elseif($dow==2||$dow==5) $t=strtotime('6:45');
	else $t=strtotime('7:00');
	if (TIME<$t+900||TIME>strtotime('12:00')) {
		store('badkamer_set', 23, basename(__FILE__).':'.__LINE__);
		storemode('badkamer_set', 2, basename(__FILE__).':'.__LINE__);
	}
}
