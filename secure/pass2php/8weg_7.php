<?php
if ($status=='On') {
	sw('poort', 'Off', basename(__FILE__).':'.__LINE__);
	setCache('8weg', $time);
}
