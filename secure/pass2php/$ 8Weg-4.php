<?php
if ($status=='On') {
	if ($d['poort']['s']=='Open') {
		if ($d['achterdeur']['s']!='Closed') {
			waarschuwing(' Let op . Achterdeur open');
			exit;
		}
		if ($d['deurvoordeur']['s']!='Closed') {
			waarschuwing(' Let op . Raam Living open');
			exit;
		}
		if ($d['raamliving']['s']!='Closed') {
			waarschuwing(' Let op . Raam Living open');
			exit;
		}
		if ($d['raamhall']['s']!='Closed') {
			waarschuwing(' Let op . Raam hall open');
			exit;
		}
		if ($d['raamkeuken']['s']!='Closed') {
			waarschuwing(' Let op . Raam keuken open');
			exit;
		}
		if ($d['bose104']['m']=='Online') {
			waarschuwing(' Let op . Bose garage aan');
			exit;
		}
		if ($d['bose105']['m']=='Online') {
			waarschuwing(' Let op . Bose badkamer');
			exit;
		}
		if ($d['bose106']['m']=='Online') {
			waarschuwing(' Let op . Bose buiten20');
			exit;
		}
		if ($d['bose107']['m']=='Online') {
			waarschuwing(' Let op . Bose buiten10');
			exit;
		}
		store('Weg', 2, basename(__FILE__).':'.__LINE__);
		sl('Xring', 90, basename(__FILE__).':'.__LINE__);
		huisslapen();
		sleep(4);
		sl('Xring', 0, basename(__FILE__).':'.__LINE__);
	} else {
		sw('poortrf', 'On', basename(__FILE__).':'.__LINE__);
		if ($d['sirene']['s']!='Off') {
			sw('sirene', 'Off', basename(__FILE__).':'.__LINE__);
		}
	}
}
