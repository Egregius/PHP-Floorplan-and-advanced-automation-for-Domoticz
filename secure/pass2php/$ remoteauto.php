<?php
//$d=fetchdata();
if ($status=='Off') {
	$last=mget('remoteauto');
	lg('REMOTE AUTO past='.$last);
	if ($last>$time-60) sw('poortrf', 'On');
	sw('voordeur', 'On', basename(__FILE__).':'.__LINE__, true);
	huisthuis();
	mset('remoteauto', $time);
	lg($d['zon']['s']);
	if ($d['zon']['s']>0) {
		sleep(2);
		sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__, true);
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
	sw('voordeur', 'On', basename(__FILE__).':'.__LINE__, true);
	sleep(2);
	sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__, true);
	huisslapen();
}
