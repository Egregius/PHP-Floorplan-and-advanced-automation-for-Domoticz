<?php
if ($d['lichtbadkamer']['s']>0) sw('lichtbadkamer', 'Off', basename(__FILE__).':'.__LINE__);
$dow=date("w");
if($dow==0||$dow==6) $t=strtotime('7:30');
elseif($dow==2) $t=strtotime('6:45');
else $t=strtotime('7:00');
if ($d['Weg']['s']==1&&$time>$t&&$time<$t+2700) huisthuis();
if ($d['badkamer_set']['s']!=16) store('badkamer_set', 16, basename(__FILE__).':'.__LINE__);
if ($d['badkamer_set']['m']!=0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
