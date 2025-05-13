<?php
$time=time();
mset('lichtbadkamertijd', $time);
if ($d['lichtbadkamer']['s']>0) sw('lichtbadkamer', 'Off', basename(__FILE__).':'.__LINE__);

$t=t();
if ($d['Weg']['s']==1&&$time>$t-1800&&$time<$t+2700) huisthuis();
if ($d['badkamer_set']['m']!=0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
if ($d['badkamervuur2']['s']=='On') sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
if ($d['badkamervuur1']['s']=='On') sw('badkamervuur1', 'Off', basename(__FILE__).':'.__LINE__);
if ($d['Weg']['s']==1&&$time>=$t-3600&&$time<$t+3600) {
	sw('boseliving', 'On', basename(__FILE__).':'.__LINE__);
	sw('mac', 'On', basename(__FILE__).':'.__LINE__);
}
if (past('$ 8badkamer-8.php')>900) exec('curl -s http://192.168.2.20/secure/runsync.php?sync=weegschaal &');