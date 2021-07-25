<?php
if ($status=='Off') {
	sw('werkbladL', 'Off', basename(__FILE__).':'.__LINE__);
	rgb('Xlight', 0, 100);
	sleep(2);
	rgb('Xlight', 0, 0);
} else {
	rgb('Xlight', 126, 100);
	sleep(2);
	rgb('Xlight', 126, 0);
}
