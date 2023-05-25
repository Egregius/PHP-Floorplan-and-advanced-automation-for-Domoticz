<?php
if (!is_array($d)) $d=fetchdata();
if ($status=='Off') {
	if (past('$ remoteauto')<60) sw('poortrf', 'On');
	sw('voordeur', 'On');
	huisthuis();
	if ($d['zon']['s']>0) {
		$d['voordeur']['s']='On';
		sleep(2);
		sw('voordeur', 'Off');
	}
} else {
	if ($d['achterdeur']['s']!='Closed') {
		waarschuwing(' Let op . Achterdeur open');
		exit;
	}
	if ($d['deurvoordeur']['s']!='Closed') {
		waarschuwing(' Let op . Voordeur open');
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
	if ($d['bose105']['m']=='Online') {
		waarschuwing(' Let op . Bose badkamer');
		exit;
	}
	if ($d['bose106']['m']=='Online') {
		waarschuwing(' Let op . Bose buiten');
		exit;
	}
	if ($d['bose107']['m']=='Online') {
		waarschuwing(' Let op . Bose buiten');
		exit;
	}
	store('Weg', 2);
	sw('voordeur', 'On');
	$d['voordeur']['s']='On';
	sleep(2);
	sw('voordeur', 'Off');
	huisslapen();
}
