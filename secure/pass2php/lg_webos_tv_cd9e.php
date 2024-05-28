<?php
if ($status!=$d[$device]['s']) store($device,$status, basename(__FILE__).':'.__LINE__);//lg(print_r($topic,true));
if ($status=='On') {
	$time=time();
	if ($time>=strtotime('18:30')) sl('Rliving', 25, basename(__FILE__).':'.__LINE__);
}