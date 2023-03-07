<?php
if ($d['wasbak']['s']<10) sl('wasbak', 10, basename(__FILE__).':'.__LINE__);
elseif ($d['wasbak']['s']<28) sl('wasbak', 30, basename(__FILE__).':'.__LINE__);
else sl('wasbak', 100, basename(__FILE__).':'.__LINE__);
