<?php
lg('REMOTE AUTO = '.$status);
if ($status=='Off') {
	sw('voordeur', 'On', basename(__FILE__).':'.__LINE__, false);
	$last=mget('remoteauto');
	$time=time();
	lg('REMOTE AUTO past='.$last.'	time ='.$time);
	mset('remoteauto', $time);
	if ($last>$time-60&&$d['Weg']['s']==0) {
		lg('SWITCH POORT ON');
		sw('poortrf', 'On', basename(__FILE__).':'.__LINE__, true);
	}
	if ($d['Weg']['s']>0) huisthuis();
	if ($d['zon']>0) {
		sleep(2);
		sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__, true);
		if ($d['zon']>2000) {
			mset('powermeter',time());
			usleep(100000);
			sw('powermeter', 'On', basename(__FILE__).':'.__LINE__,true);
		}
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
