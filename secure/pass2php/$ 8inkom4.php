<?php
if ($d['deurvoordeur']['s']=='Open'&&$status=='On') {
	if ($d['achterdeur']['s']!='Closed') {
		waarschuwing(' Let op . Achterdeur open');
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
	if ($d['bose103']['m']=='Online') {
		waarschuwing(' Let op! Bose kamer');
		exit;
	}
	if ($d['bose104']['m']=='Online') {
		waarschuwing(' Let op! Bose garage aan');
		exit;
	}
	if ($d['bose105']['m']=='Online') {
		waarschuwing(' Let op! Bose badkamer');
		exit;
	}
	if ($d['bose106']['m']=='Online') {
		waarschuwing(' Let op! Bose buiten20');
		exit;
	}
	if ($d['bose107']['m']=='Online') {
		waarschuwing(' Let op! Bose buiten10');
		exit;
	}
	sw('powermeter', 'Off', basename(__FILE__).':'.__LINE__,true);
	storemode('powermeter', 0, basename(__FILE__).':'.__LINE__);
	store('Weg', 2, basename(__FILE__).':'.__LINE__);
	file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx=749&switchcmd=Set%20Level&level=70');
	file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx=745&switchcmd=Set%20Level&level=90&passcode=');
	huisslapen(true);
	sleep(2);
	file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx=745&switchcmd=Set%20Level&level=0&passcode=');
}
