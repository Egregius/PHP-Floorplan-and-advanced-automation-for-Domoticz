<?php
if ($status=='On') {
	if ($d['achterdeur']['s']!='Closed') {
		waarschuwing(' Let op . Achterdeur open', 55);
		exit;
	}
	if ($d['raamliving']['s']!='Closed') {
		waarschuwing(' Let op . Raam Living open', 55);
		exit;
	}
	if ($d['raamhall']['s']!='Closed') {
		waarschuwing(' Let op . Raam hall open', 55);
		exit;
	}
	if ($d['raamkeuken']['s']!='Closed') {
		waarschuwing(' Let op . Raam keuken open', 55);
		exit;
	}
	if ($d['bureelspeelkamer']['s']=='On') {
		waarschuwing(' Let op . bureel speelkamer aan', 55);
		exit;
	}
	if ($d['bose105']['m']=='Online') {
		waarschuwing(' Let op . Bose buiten', 55);
		exit;
	}
	boseplayinfo(' Alles ok . Vertrek maar', 50);
	usleep(380000);
	bosevolume(55, 104);
	usleep(3000000);
	bosekey("POWER", 0, 104);
	huisslapen();
	store('Weg', 2, basename(__FILE__).':'.__LINE__);
}
