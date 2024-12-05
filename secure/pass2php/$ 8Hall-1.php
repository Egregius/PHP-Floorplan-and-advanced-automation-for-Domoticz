<?php
if ($status=='On'&&$d['Weg']['s']==0&&($time>strtotime('21:00')||$time<strtotime('4:00'))) {
	if ($d['achterdeur']['s']!='Closed') {
		waarschuwing('Let op. Achterdeur open', 55);
		exit('');
	}
	if ($d['deurvoordeur']['s']!='Closed') {
		waarschuwing('Let op. Voordeur open', 55);
		exit('');
	}
	if ($d['raamliving']['s']!='Closed') {
		waarschuwing('Let op. Raam Living open', 55);
		exit('');
	}
	if ($d['bose105']['m']=='Online') {
		waarschuwing('Let op. Bose buiten', 55);
		exit('');
	}
	if ($d['Weg']['s']!=1) {
		store('Weg', 1, basename(__FILE__).':'.__LINE__);
	}
	if ($d['kamer']['s']>5) {
		sl('kamer', 5, basename(__FILE__).':'.__LINE__);
	}
	huisslapen();
}