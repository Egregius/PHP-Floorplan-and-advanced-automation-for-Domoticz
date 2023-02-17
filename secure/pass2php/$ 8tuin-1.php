<?php
if ($d['luifel']['s']>30) $level=$d['luifel']['s']-10;
else $level=0;
sl('luifel', $level, basename(__FILE__).':'.__LINE__);
storemode('luifel', 1, basename(__FILE__).':'.__LINE__);
