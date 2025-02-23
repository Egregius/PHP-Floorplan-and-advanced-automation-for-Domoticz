<?php
if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
sw('badkamervuur1', 'Off', basename(__FILE__).':'.__LINE__);
exec('curl -s http://192.168.2.20/secure/runsync.php?sync=weegschaal &');