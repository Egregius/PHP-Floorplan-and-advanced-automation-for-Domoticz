<?php
if ($d['kamer']['s']>0) sl('kamer', floor($d['kamer']['s']*0.95));
if ($d['kamer']['m']>0) storemode('kamer', 0, basename(__FILE__).':'.__LINE__);