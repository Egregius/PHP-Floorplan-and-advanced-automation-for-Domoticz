<?php
if ($status=='On') {
	if($d['boseliving']->m=='NoScore') {
		$data=curl('http://192.168.2.2/ajax.php?bose=101');
		$data=json_decode($data,true);
		if(updatescore($data['cleantitle'],-10,$data['track_id'])) {
			if($d['spotify']->s!='Off') sw('spotify','Off',basename(__FILE__).':'.__LINE__,'cron2');
			if($d['boseliving']->m=='NoScore') Wiim('setPlayerCmd:next');
		}
	} else {
		if ($d['eettafel']->s==0) {
			sl('eettafel', 35, basename(__FILE__).':'.__LINE__);
		} else {
			$new=floor($d['eettafel']->s*0.75);
			if($new<10) $new=0;
			sl('eettafel', $new);
		}
	}
}
