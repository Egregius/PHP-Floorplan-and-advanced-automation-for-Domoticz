<?php
if ($status=='On') {
	if($d['boseliving']->m=='NoScore') {
		$data=curl('http://192.168.2.2/ajax.php?bose=101');
		$data=json_decode($data,true);
		if(updatescore($data['cleantitle'],+10,$data['track_id'])) {
			if($d['spotify']->s!='Off') sw('spotify','Off',basename(__FILE__).':'.__LINE__,'cron2');
			if($d['boseliving']->m=='NoScore') Wiim('setPlayerCmd:next');
		}
	} else {
		if ($d['eettafel']->s==0) {
			if ($d['time']<=strtotime('9:00')) sl('eettafel', 35, basename(__FILE__).':'.__LINE__);
			else sl('eettafel', 80, basename(__FILE__).':'.__LINE__);
		} else {
			$new=ceil($d['eettafel']->s*1.25);
			if ($new>100) $new=100;
			sl('eettafel', $new);
			$d['eettafel']->s=$new;
		}
	}
}
