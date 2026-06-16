<?php
if($status=='Off') {
	Wiim('setPlayerCmd:stop');
} elseif($status=='On') {
	Wiim('setPlayerCmd:clear_playlist');
	$preset=wiimplaylist();
	Wiim("MCUKeyShortClick:$preset");
	sleep(1);
	Wiim("setPlayerCmd:playindex:1");
}