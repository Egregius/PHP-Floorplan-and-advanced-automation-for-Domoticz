<?php
if ($d['deurvoordeur']['s']=='Open'&&$status=='On') {
	$x=0;
	if ($d['achterdeur']['s']!='Closed') {
		waarschuwing('Achterdeur open', 55);
		$x++;
	}
	if ($d['raamliving']['s']!='Closed') {
		waarschuwing('Raam Living open', 55);
		$x++;
	}
	if ($d['raamkeuken']['s']!='Closed') {
		waarschuwing('Raam keuken open');
		$x++;
	}
	$boses=array(
		102=>'102',
		103=>'Boven',
		104=>'Garage',
		105=>'10-Wit',
		106=>'Buiten20',
	);
	foreach ($boses as $k->$v) {
		if ($d['bose'.$k]['icon']=='Online') {
			waarschuwing('Bose '.$v, 55);
			$x++;
		}
	}
	if ($x>0) exit('');
	hassopts('xiaomi_aqara', 'play_ringtone', '', ['gw_mac' => '34ce008d3f60','ringtone_id' => 8,'ringtone_vol' => 50]);
	huisslapen(true);
	sl('zoldertrap', 0, basename(__FILE__).':'.__LINE__,true);
}
