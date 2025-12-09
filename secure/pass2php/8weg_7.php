<?php
if ($status=='On') {
	sw('poortrf', 'Off', basename(__FILE__).':'.__LINE__);
	setCache('8weg', $time);
}
