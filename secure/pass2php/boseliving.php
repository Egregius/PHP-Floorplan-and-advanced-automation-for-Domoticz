<?php
if($status=='Off') {
	$wiim=json_decode(Wiim('getMetaInfo'));
	lg($wiim->metaData->artist.' '.$wiim->metaData->title,'wiimtracks');
	Wiim('setPlayerCmd:stop');
//	Wiim('setPlayerCmd:clear_playlist');
	if($d['boseliving']->m!=0) storemode('boseliving',0,basename(__FILE__).':'.__LINE__);
} elseif($status=='On') {
//	Wiim('setPlayerCmd:resume');
//	$preset=wiimplaylist();
//	Wiim("MCUKeyShortClick:$preset");
	if($d['boseliving']->m!=1) storemode('boseliving',1,basename(__FILE__).':'.__LINE__);
}