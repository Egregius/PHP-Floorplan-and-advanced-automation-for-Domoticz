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

function updatescore($cleantitle, $score_change, $track_id) {
	$ch = curl_init('https://secure.egregius.be/spotify/actions.php');
	$data = [
		'cleantitle' => $cleantitle,
		'score_change' => $score_change,
		'track_id' => $track_id
	];
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	$response = curl_exec($ch);
	echo $response;
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if ($httpCode === 200 && $response !== false) {
		$responseData = json_decode($response, true);
		return true;
	}
	return false;
}