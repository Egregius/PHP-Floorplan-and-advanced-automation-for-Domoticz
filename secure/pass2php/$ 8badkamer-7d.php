<?php
if ($d['badkamer_set']['s']!=15) store('badkamer_set', 15, basename(__FILE__).':'.__LINE__);
if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
if ($d['luchtdroger']['m']!='Auto') storemode('luchtdroger', 'Auto', basename(__FILE__).':'.__LINE__);
