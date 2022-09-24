<?php
if ($status=='On') {
	sl('alex', 1, basename(__FILE__).':'.__LINE__);
	if (TIME<=strtotime('7:00')||TIME>=strtotime('20:00')) telegram('Alex licht aan', false, 2);
} else {
	sl('alex', 0, basename(__FILE__).':'.__LINE__);
	if (TIME<=strtotime('7:00')||TIME>=strtotime('20:00')) telegram('Alex licht uit', false, 2);
}
