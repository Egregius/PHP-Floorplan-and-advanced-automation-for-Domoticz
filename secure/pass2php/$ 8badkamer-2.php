<?php
if ($status=='On') {
	sl('lichtbadkamer', 35, basename(__FILE__).':'.__LINE__);
	store('deurbadkamer', $d['deurbadkamer']['s'], basename(__FILE__).':'.__LINE__);
	$dow=date("w");
	if($dow==0||$dow==6) $t=strtotime('7:30');
	elseif($dow==2) $t=strtotime('6:45');
	else $t=strtotime('7:00');
	if ($d['Weg']['s']==1&&$time>$t&&$time<$t+2700) huisthuis();
}
