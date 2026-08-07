<?php
if($status=='Off') {
	$wiim=json_decode(Wiim('getMetaInfo'));
	lg($wiim->metaData->artist.' '.$wiim->metaData->title,'wiimtracks');
	Wiim('setPlayerCmd:stop');
	Wiim('setPlayerCmd:clear_playlist');
} elseif($status=='On') {
	Wiim('setPlayerCmd:resume');
//	$preset=wiimplaylist();
//	Wiim("MCUKeyShortClick:$preset");
}