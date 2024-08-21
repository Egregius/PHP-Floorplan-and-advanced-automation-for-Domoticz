<?php
if ($d['luifel']['s']<30) $level=30;
elseif ($d['luifel']['s']>=30&&$d['luifel']['s']<=90) $level=$d['luifel']['s']+15;
else $level=100;
sl('luifel', $level, basename(__FILE__).':'.__LINE__);
storemode('luifel', 1, basename(__FILE__).':'.__LINE__);

