<?php
$time=time();
if ($status=='On') {
	sl('alex', 1, basename(__FILE__).':'.__LINE__);
	if ($time<=strtotime('7:00')||$time>=strtotime('20:00')) telegram('Alex licht aan', false, 2);
} else {
	sw('alex', 'Off', basename(__FILE__).':'.__LINE__);
	if ($time<=strtotime('7:00')||$time>=strtotime('20:00')) telegram('Alex licht uit', false, 2);
}
