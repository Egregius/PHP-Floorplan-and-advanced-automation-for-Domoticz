<?php
if ($d['deurvoordeur']['s']=='Open'&&$status=='On') {
	if ($d['achterdeur']['s']!='Closed') {
		waarschuwing(' Let op . Achterdeur open');
		exit;
	}
	if ($d['poort']['s']!='Closed') {
		waarschuwing(' Let op . Poort open');
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
		waarschuwing(' Let op . Bose badkamer aan');
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
	file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx=749&switchcmd=Set%20Level&level=60');
	sl('Xring', 90, basename(__FILE__).':'.__LINE__);
	huisslapen();
	sleep(4);
	sl('Xring', 0, basename(__FILE__).':'.__LINE__);
}
mset('8inkom', time());
