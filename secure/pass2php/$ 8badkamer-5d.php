<?php
if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
if ($d['waskamervuur2']['s']=='On') sw('waskamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
if ($d['waskamervuur1']['s']=='On') sw('waskamervuur1', 'Off', basename(__FILE__).':'.__LINE__);