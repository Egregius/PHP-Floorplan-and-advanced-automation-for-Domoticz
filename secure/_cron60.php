<?php
$user='cron60';
//lg($user);
$stamp=sprintf("%s", date("Y-m-d H:i"));
foreach (array('buiten','living','badkamer','kamer','waskamer','alex','zolder') as $i) ${$i}=$d[$i.'_temp']['s'];
foreach (array('buiten','living','kamer','alex','badkamer') as $i) ${$i.'_hum'}=$d[$i.'_temp']['m'];
$query="INSERT IGNORE INTO temp (stamp,buiten,living,badkamer,kamer,waskamer,alex,zolder,living_hum,kamer_hum,alex_hum,badkamer_hum,buiten_hum)  VALUES ('$stamp','$buiten','$living','$badkamer','$kamer','$waskamer','$alex','$zolder','$living_hum','$kamer_hum','$alex_hum','$badkamer_hum','$buiten_hum');";

$db=dbconnect();
if (!$result = $db->query($query)) die('There was an error running the query ['.$query.' - '.$db->error.']');
foreach (array('living','badkamer','kamer','alex','zolder') as $i) $sum=@$sum+$d[$i.'_temp']['s'];
$avg=$sum/6;
foreach (array('living','badkamer','kamer','alex','zolder') as $i) {
	if ($d[$i.'_temp']['s']>($avg+5)&&$d[$i.'_temp']['s']>25) alert($i.'temp','OPGELET: '. $d[$i.'_temp']['s'].'° in '.$i,7200,false,2);
}
if ($d['auto']['s']=='On') {
	if ($d['weg']['s']==0){/* ----------------------------------------- THUIS ----------------------------------------------------*/
//		if ($d['zon']==0&&$d['tuintafel']['s']=='Off'&&$d['rliving']['s']<50) sw('tuintafel', 'On', basename(__FILE__).':'.__LINE__);
//		elseif (($d['zon']>0||$d['rliving']['s']>50)&&$d['tuintafel']['s']=='On') sw('tuintafel', 'Off', basename(__FILE__).':'.__LINE__);
		if ($d['pirliving']['s']=='Off') {
			$uit=6300;
			if (past('pirliving')>$uit) {
				foreach (array('bureel','zithoek','eettafel') as $i) if ($d[$i]['s']>0&&past($i)>$uit) sl($i, 0, basename(__FILE__).':'.__LINE__);
			}
			$uit=10800;
			if (past('pirliving')>$uit) {
				foreach (array('kristal','lampkast') as $i) if ($d[$i]['s']=='On'&&past($i)>$uit) sw($i, 'Off', basename(__FILE__).':'.__LINE__);
			}
		}
		$avg=0;
		foreach (array('living_temp','kamer_temp','waskamer_temp','alex_temp','zolder_temp') as $i) $avg=$avg+$d[$i]['s'];
		$avg=$avg/5;
		foreach (array('living_temp','kamer_temp','waskamer_temp','alex_temp','zolder_temp') as $i) {
			if ($d[$i]['s']>$avg+5&&$d[$i]['s']>25) alert($i,'T '.$i.'='.$d[$i]['s'].'°C. AVG='.round($avg, 1).'°C',3600,false,true);
		}
/*		if ($d['lgtv']['s']=='On') {
			if ($d['dag']['s']<0&&$d['kristal']['s']=='Off'&&past('kristal')>7200&&$d['buiten_temp']['s']<10) sw('kristal', 'On', basename(__FILE__).':'.__LINE__);
		}*/
	}elseif ($d['weg']['s']>=2) {/* ----------------------------------- WEG ------------------------------------------------------*/
		$uit=600;
		foreach (array('pirhall') as $i) {
			if ($d[$i]['s']!='Off') {
				if (past($i)>$uit) {
					ud($i, 0, 'Off');
					lg($i.' uitgeschakeld omdat we weg zijn');
				}
			}
		}
		foreach (array('kamer','waskamer','alex','lichtbadkamer') as $i) {
			if ($d[$i]['s']>0) {
				if (past($i)>$uit) {
					if ($d[$i]['s']>0) {
						sl($i, 0, basename(__FILE__).':'.__LINE__);
						lg($i.' uitgeschakeld omdat we weg zijn');
					}
				}
			}
		}
	}elseif ($d['weg']['s']>=1) {/* ----------------------------------- SLAPEN OF WEG --------------------------------------------*/
		$uit=600;
		foreach (array('lampkast','bureel','kristal','garage','tuin','voordeur','zolderg','mac','ipaddock','zetel') as $i) {
			if ($d[$i]['s']=='On') {
				if (past($i)>$uit) {
					sw($i, 'Off', basename(__FILE__).':'.__LINE__);
					lg($i.' uitgeschakeld omdat we slapen of weg zijn');
				}
			}
		}
		foreach (array('eettafel','zithoek','wasbak','snijplank','hall','inkom','terras') as $i) {
			if ($d[$i]['s']>0) {
				if (past($i)>$uit) {
					sl($i, 0, basename(__FILE__).':'.__LINE__);
					lg($i.' uitgeschakeld omdat we slapen of weg zijn');
				}
			}
		}
	}

	/* -------------------------------------------- ALTIJD BIJ AUT0 ------------------------------------------*/
	if ($d['voordeur']['s']=='On'&&$d['deurvoordeur']['s']=='Closed'&&past('voordeur')>170) sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['ipaddock']['s']=='On'&&$d['ipaddock_vermogen']['s']<2&&past('ipaddock')>=600) sw('ipaddock', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['living_set']['m']!=0&&$d['eettafel']['s']==0&&past('living_set')>28800) storemode('living_set', 0, basename(__FILE__).':'.__LINE__);
	if ($d['badkamer_set']['m']!=0&&$d['lichtbadkamer']['s']==0&&past('badkamer_set')>7200) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
	foreach (array('kamer_set','alex_set') as $i) {
		if ($d[$i]['m']!=0&&past($i)>43200) storemode($i, 0, basename(__FILE__).':'.__LINE__);
	}
	if ($d['poortrf']['s']=='On'&&past('poortrf')>600&&past('pirgarage')>600&&past('deurgarage')>600) sw('poortrf', 'Off', basename(__FILE__).':'.__LINE__);

	if ($d['pirliving']['s']=='Off') {
		if ($d['dag']['s']>1) {
			if ($d['rbureel']['s']<40&&$d['rliving']['s']<40) {
				if ($d['lampkast']['s']=='On'&&$d['eettafel']['s']==0) sw('lampkast', 'Off', basename(__FILE__).':'.__LINE__);
				if ($d['zithoek']['s']>0&&$d['zithoek']['s']<20&&$d['eettafel']['s']==0) sl('zithoek', 0, basename(__FILE__).':'.__LINE__);
				if ($d['bureel']['s']>0&&$d['bureel']['s']<=24&&$d['eettafel']['s']==0) sl('bureel', 0, basename(__FILE__).':'.__LINE__);
			}
		}
	}

	if ($d['wc']['s']=='On' && past('wc')>590 && past('deurwc')>590) sw('wc', 'Off', basename(__FILE__).':'.__LINE__);

	if ($d['rliving']['s']>60&&$d['achterdeur']['s']=='Closed') {
		if ($d['tuin']['s']=='On') sw('tuin', 'Off', basename(__FILE__).':'.__LINE__);
		if ($d['tuintafel']['s']=='On') sw('tuintafel', 'Off', basename(__FILE__).':'.__LINE__);
		if ($d['steenterras']['s']=='On') sw('steenterras', 'Off', basename(__FILE__).':'.__LINE__);
		if ($d['terras']['s']>0) sl('terras', 0, basename(__FILE__).':'.__LINE__);
	}
	
	if ($d['kookplaat']['s']=='On'&&$d['wasbak']['s']==0&&$d['snijplank']['s']==0) {
		if ($d['kookplaat_power']['s']<125&&past('kookplaat_power')>240) sw('kookplaat', 'Off', basename(__FILE__).':'.__LINE__);
	}
	if ($d['weg']['s']>=1&&$d['media']['s']=='On'&&past('weg')>900) sw('media', 'Off', basename(__FILE__).':'.__LINE__);
}
/* -------------------------------------------- ALTIJD ---------------------------------------------------*/
if ($d['wasmachine']['s']=='On') {
	if ($d['wasmachine_power']['s']<3.4&&past('wasmachine')>4200&&past('wasmachine_power')>120) {
		hassnotify('Wasmachine', 'klaar');
		sw('wasmachine', 'Off', basename(__FILE__).':'.__LINE__);
	}
}
