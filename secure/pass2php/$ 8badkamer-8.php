<?php
if ($d['lichtbadkamer']['s']>0) sw('lichtbadkamer', 'Off', basename(__FILE__).':'.__LINE__);
$time=time();
mset('lichtbadkamer', $time);

$t=t();
if ($d['Weg']['s']==1&&$time>$t&&$time<$t+2700) huisthuis();
if ($d['badkamer_set']['m']!=0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
if ($d['waskamervuur2']['s']=='On') sw('waskamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
if ($d['waskamervuur1']['s']=='On') sw('waskamervuur1', 'Off', basename(__FILE__).':'.__LINE__);
if ($d['Weg']['s']==1&&$time>=$t-3600&&$time<$t+3600) sw('langekast', 'On', basename(__FILE__).':'.__LINE__);