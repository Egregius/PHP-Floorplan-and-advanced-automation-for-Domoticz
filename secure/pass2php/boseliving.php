<?php
if($status=='Off') {
	$wiim=json_decode(Wiim('getMetaInfo'));
	lg(wiim->metaData->artist.' '.wiim->metaData->title,'wiimtracks');
	Wiim('setPlayerCmd:stop');
	Wiim('setPlayerCmd:clear_playlist');
} elseif($status=='On') {
	$preset=wiimplaylist();
//	Wiim("setPlayerCmd:playindex:1");
//	sleep(1);
	Wiim("MCUKeyShortClick:$preset");
//	sleep(1);
//	Wiim("setPlayerCmd:playindex:1");
}