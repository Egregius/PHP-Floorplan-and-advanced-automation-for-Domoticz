<?php
if (past('$ miniliving1s')>2) {
	if ($d['sony']['s']=='On'&&$d['bose101']['s']=='On') {
		sw('bose101', 'Off');
		bosekey("POWER");
		foreach (array('bose102', 'bose103', 'bose104', 'bose105') as $i) {
			if ($d[$i]['s']=='On') {
				sw($i, 'Off');
				bosekey("POWER");
			}
		}
	} else {
		if ($d['bose101']['s']=='Off') {
			sw('bose101', 'On');
			bosekey("PRESET_5", 0, 101);
		} else {
			sw('bose101', 'Off');
			bosekey("POWER");
		}
	}
	store('Weg', 0, basename(__FILE__).':'.__LINE__);
}
