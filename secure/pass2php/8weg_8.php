<?php
if ($status=='On') {
	sw('poort', 'On', basename(__FILE__).':'.__LINE__);
	setCache('8weg8', $time);
}