<?php
//if($status=='On') hassAddon('d5369777_music_assistant_beta','start');
//else
if($status=='Off') {
	if($d['music_assistant_beta']->s!='Off') sw('music_assistant_beta','Off',basename(__FILE__).':'.__LINE__);
	Wiim('setPlayerCmd:stop');
} elseif($status=='On') {
	$preset=wiimplaylist();
	Wiim('EQSetBand:{"EQBand":[{"index":0,"param_name":"band31hz","value":50},{"index":1,"param_name":"band63hz","value":50},{"index":2,"param_name":"band125hz","value":50},{"index":3,"param_name":"band250hz","value":50},{"index":4,"param_name":"band500hz","value":50},{"index":5,"param_name":"band1khz","value":50},{"index":6,"param_name":"band2khz","value":50},{"index":7,"param_name":"band4khz","value":50},{"index":8,"param_name":"band8khz","value":50},{"index":9,"param_name":"band16khz","value":50}]}');
	Wiim("MCUKeyShortClick:$preset");
	sleep(1);
	Wiim("setPlayerCmd:playindex:1");
}