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
	store('weg', 2, basename(__FILE__).':'.__LINE__);
	hassopts('xiaomi_aqara', 'play_ringtone', '', ['gw_mac' => '34ce008d3f60','ringtone_id' => 8,'ringtone_vol' => 50]);
	huisslapen(true);
}
