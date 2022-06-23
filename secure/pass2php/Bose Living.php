<?php
if($status>80)$status=30;
if ($d['bose101']['s']=='Off'&&$status>0) {
	sw('bose101', 'On');
	bosekey("PRESET_5", 0, 101);
} elseif ($d['bose101']['s']=='On'&&$status==0) {
	sw('bose101', 'Off');
	bosekey("POWER");
} else bosevolume($status);
