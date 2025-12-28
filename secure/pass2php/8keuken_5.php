<?php
if ($status=='On') {
	sl('wasbak', 0, basename(__FILE__).':'.__LINE__,true);
	zwave('wasbak','multilevel',0,0);
}