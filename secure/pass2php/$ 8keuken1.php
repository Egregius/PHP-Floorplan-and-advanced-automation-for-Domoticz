<?php
if ($d['wasbak']['s']<18) sl('wasbak', 20, basename(__FILE__).':'.__LINE__);
elseif ($d['wasbak']['s']<38) sl('wasbak', 40, basename(__FILE__).':'.__LINE__);
else sl('wasbak', 100, basename(__FILE__).':'.__LINE__);
