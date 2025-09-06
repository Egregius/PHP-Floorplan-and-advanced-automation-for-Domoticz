<?php
if ($status=='On'&&$d['weg']['s']==0&&($d['time']>strtotime('21:00')||$d['time']<strtotime('4:00'))) {
	$x=0;
	if ($d['achterdeur']['s']!='Closed') {
		waarschuwing('Achterdeur open', 55);
		$x++;
	}
	if ($d['deurvoordeur']['s']!='Closed') {
		waarschuwing('Voordeur open', 55);
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
			waarschuwing('Let op. Bose '.$v, 55);
			$x++;
		}
	}
	if ($x>0) exit('');
	if ($d['kamer']['s']>5) {
		sl('kamer', 5, basename(__FILE__).':'.__LINE__);
	}
	huisslapen();
}