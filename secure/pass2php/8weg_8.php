<?php
if ($status=='On') {
	sw('poortrf', 'On', basename(__FILE__).':'.__LINE__);
	mset('8weg', time());
}