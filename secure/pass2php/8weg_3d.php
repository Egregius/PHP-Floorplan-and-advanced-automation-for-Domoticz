<?php
if ($status=='On') {
	sw('powermeter', 'Off', basename(__FILE__).':'.__LINE__,true);
	storemode('powermeter', 0, basename(__FILE__).':'.__LINE__);
}