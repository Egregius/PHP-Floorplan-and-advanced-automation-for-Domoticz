<?php
if ($d['snijplank']['s']<10) sl('snijplank', 10, basename(__FILE__).':'.__LINE__);
elseif ($d['snijplank']['s']<30) sl('snijplank', 30, basename(__FILE__).':'.__LINE__);
else sl('snijplank', 100, basename(__FILE__).':'.__LINE__);
