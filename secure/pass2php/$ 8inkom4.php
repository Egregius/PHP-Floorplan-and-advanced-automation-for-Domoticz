<?php
if ($status=='On') {
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
	if ($d['bose105']['m']=='Online') {
		waarschuwing(' Let op . Bose badkamer aan');
		exit;
	}
	if ($d['bose106']['m']=='Online') {
		waarschuwing(' Let op . Bose buiten');
		exit;
	}
	if ($d['bose106']['m']=='Online') {
		waarschuwing(' Let op . Bose buiten');
		exit;
	}
	boseplayinfo(' Alles ok . Vertrek maar', 60);
	usleep(380000);
	bosevolume(60, 101);
	usleep(3000000);
	bosekey("POWER", 0, 101);
	huisslapen();
	store('Weg', 2, basename(__FILE__).':'.__LINE__);
}
