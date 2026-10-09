<?php
if ($status=='On') {
	if($d['boseliving']->m=='NoScore') {
		$data=curl('http://192.168.2.2/ajax.php?bose=101');
		$data=json_decode($data,true);
		if(($data['genre']=='EDM'&&$data['score']>=120)||($data['genre']=='POP'&&$data['score']>=195)) {
			if(updatescore($data['cleantitle'],null,$data['track_id'])) {
				if($d['spotify']->s!='Off') sw('spotify','Off',basename(__FILE__).':'.__LINE__,'cron2');
				Wiim('setPlayerCmd:next');
				usleep(1300000);
				Wiim('setPlayerCmd:seek:45');
			}
		}
	} else {
		if (past('pirliving')<300||past('lgtv')<300) {
			sw('zetel', 'On', basename(__FILE__).':'.__LINE__, true);
		}
	}
}
