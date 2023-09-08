<?php
if (isset($_REQUEST['gpio'])) {
	require_once 'secure/functions.php';
	$d=fetchdata();
	$gpio=$_REQUEST['gpio'];
	if ($gpio==20) {
		store('gasvandaag', $d['gasvandaag']['s']+1, basename(__FILE__).':'.__LINE__);
		if ($d['brander']['s']=='On'&&$d['living_temp']['s']>$d['living_set']['s']+1&&$d['badkamer_temp']['s']>$d['badkamer_set']['s']+1&&past('brander')>298) sw('brander', 'Off',basename(__FILE__).':'.__LINE__);
	} elseif ($gpio==21) {
		store('watervandaag', $d['watervandaag']['s']+1, basename(__FILE__).':'.__LINE__);
	} elseif ($gpio==19) {
		if ($_REQUEST['action']=='on') store('poort', 'Closed', basename(__FILE__).':'.__LINE__);
		else {
			fgarage();
			store('poort', 'Open', basename(__FILE__).':'.__LINE__);
			if ($d['voordeur']['s']=='On'&&$d['zon']['s']>0) sw('voordeur', 'Off',basename(__FILE__).':'.__LINE__);
			if ($d['Weg']['s']>0&&$d['poortrf']['s']=='Off') sirene('Poort open');
			if ($d['Xlight']['s']>0) sw('Xlight', 'Off', basename(__FILE__).':'.__LINE__);
		}
	} else die('Unknown');
	echo 'ok';
}
