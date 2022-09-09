<?php
if ($status=='On') {
	sl('alex', 1, basename(__FILE__).':'.__LINE__);
	telegram('Alex licht aan', false, 2);
} else {
	sl('alex', 0, basename(__FILE__).':'.__LINE__);
	telegram('Alex licht uit', false, 2);
}
