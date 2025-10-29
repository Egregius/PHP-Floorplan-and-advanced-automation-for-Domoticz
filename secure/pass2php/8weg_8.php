<?php
if ($status=='On') {
	sw('poortrf', 'On', basename(__FILE__).':'.__LINE__);
	setCache('8weg', time());
}