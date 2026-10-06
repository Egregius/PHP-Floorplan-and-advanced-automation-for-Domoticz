<?php
if($status=='Off') {
	$wiim=json_decode(Wiim('getMetaInfo'));
	lg($wiim->metaData->artist.' '.$wiim->metaData->title,'wiimtracks');
	Wiim('setPlayerCmd:stop');
//	Wiim('setPlayerCmd:clear_playlist');
	if($d['boseliving']->m!='Off') storemode('boseliving','Off',basename(__FILE__).':'.__LINE__);
} elseif($status=='On') {
	$vandaag=date("Y-m-d");
	if(!isset($d['wiimplaylist'])||$d['wimmplaylist']!=$vandaag) {
		$preset=wiimplaylist();
		Wiim("MCUKeyShortClick:$preset");
		$d['wimmplaylist']=$vandaag;
	} else {
		Wiim('setPlayerCmd:resume');
	}
	$dow = date("w");
	if ($dow == 6 || $dow == 0) $pop=25;
	else $pop=0;
	if($d['boseliving']->m!=$pop) storemode('boseliving',$pop,basename(__FILE__).':'.__LINE__);
}