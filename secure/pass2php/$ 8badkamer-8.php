<?php
if ($d['lichtbadkamer']['s']>0) sl('lichtbadkamer', 0, basename(__FILE__).':'.__LINE__);
if ($d['Weg']['s']==1&&TIME>strtotime('6:00')&&TIME<strtotime('9:00')) huisthuis();
if ($d['badkamer_set']['s']!=16) store('badkamer_set', 16, basename(__FILE__).':'.__LINE__);
if ($d['badkamer_set']['m']!=0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
