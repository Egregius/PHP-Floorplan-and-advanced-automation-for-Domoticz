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
	if ($d['weg']['s']==0) {/* ----------------------------------------- THUIS ----------------------------------------------------*/
//		if ($d['zon']==0&&$d['tuintafel']['s']=='Off'&&$d['rliving']['s']<50) sw('tuintafel', 'On', basename(__FILE__).':'.__LINE__);
//		elseif (($d['zon']>0||$d['rliving']['s']>50)&&$d['tuintafel']['s']=='On') sw('tuintafel', 'Off', basename(__FILE__).':'.__LINE__);
		if ($d['pirliving']['s']=='Off') {
			$uit=6300;
			if (past('pirliving')>$uit) {
				foreach (array('bureellinks','bureelrechts','zithoek','eettafel') as $i) if ($d[$i]['s']>0&&past($i)>$uit) sl($i, 0, basename(__FILE__).':'.__LINE__);
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
		if ($d['living_set']['s']!='D'&&$d['living_temp']['s']>22&&$d['living_temp']['s']>$d['living_set']['s']+1&&$d['brander']['s']=='On') alert('livingtemp', 'Te warm in living, '.$d['living_temp']['s'].' °C. Controleer verwarming', 3600, false);
		if ($time>=strtotime('16:00')) {
			if ($d['raamalex']['s']=='Open'&&$d['alex_temp']['s']<12) alert('raamalex', 'Raam Alex dicht doen, '.$d['alex_temp']['s'].' °C.', 1799,	false);
			if ($d['raamkamer']['s']=='Open'&&$d['alex_temp']['s']<10) alert('raamkamer', 'Raam kamer dicht doen, '.$d['kamer_temp']['s'].' °C.', 1799,	false);
		}
		if ($d['heating']['s']>0) { //Heating
			if ($d['buiten_temp']['s']<$d['kamer_temp']['s']
				&&$d['buiten_temp']['s']<$d['waskamer_temp']['s']
				&&$d['buiten_temp']['s']<$d['alex_temp']['s']
				&&($d['raamkamer']['s']=='Open'
				||$d['raamwaskamer']['s']=='Open'
				||$d['raamalex']['s']=='Open')
				&&($d['kamer_temp']['s']<10
				||$d['waskamer_temp']['s']<10
				||$d['alex_temp']['s']<10)
			) {
				alert(
					'ramenboven',
					'Ramen boven dicht doen, te koud buiten.
					Buiten = '.round($d['buiten_temp']['s'], 1).',
					kamer = '.$d['kamer_temp']['s'].',
					waskamer = '.$d['waskamer_temp']['s'].',
					Alex = '.$d['alex_temp']['s'],
					3600,
					false,
					2,
				);
			}
		} elseif ($d['heating']['s']<0) { //Cooling
			if (($d['buiten_temp']['s']>$d['kamer_temp']['s']
				&&$d['buiten_temp']['s']>$d['waskamer_temp']['s']
				&&$d['buiten_temp']['s']>$d['alex_temp']['s'])
				&&$d['buiten_temp']['s']>=18
				&&($d['kamer_temp']['s']>=18
				||$d['waskamer_temp']['s']>=18
				||$d['alex_temp']['s']>=18)
				&&($d['raamkamer']['s']=='Open'
				||($d['raamwaskamer']['s']=='Open'&&($d['deurkamer']['s']=='Open'||$d['deuralex']['s']=='Open'))
				||$d['raamalex']['s']=='Open')
			) {
				alert(
					'ramenboven',
					'Ramen boven dicht doen, te warm buiten.
					Buiten = '.round($d['buiten_temp']['s'], 1).',
					kamer = '.$d['kamer_temp']['s'].',
					waskamer = '.$d['waskamer_temp']['s'].',
					Alex = '.$d['alex_temp']['s'],
					3600,
					false,
					2,
				);
			}
		}
		if ($d['wasdroger']['s']=='On') {
			if (past('wasdroger_kWh')>600) {
				if ($d['wasdroger_kWh']['s']<10) {
					alert('wasdrogervol','Wasdroger vol',60,false,2);
					sw('wasdroger', 'Off', basename(__FILE__).':'.__LINE__);
				}
			}
		}
	} elseif ($d['weg']['s']>=2) {/* ----------------------------------- WEG ------------------------------------------------------*/
		$uit=600;
		foreach (array('pirhall') as $i) {
			if ($d[$i]['s']!='Off') {
				if (past($i)>$uit) {
					ud($i, 0, 'Off');
					lg($i.' uitgeschakeld omdat we weg zijn');
				}
			}
		}
		foreach (array('kamer',/*'waskamer',*/'alex','lichtbadkamer') as $i) {
			if ($d[$i]['s']>0) {
				if (past($i)>$uit) {
					if ($d[$i]['s']>0) {
						sl($i, 0, basename(__FILE__).':'.__LINE__);
						lg($i.' uitgeschakeld omdat we weg zijn');
					}
				}
			}
		}
	} elseif ($d['weg']['s']>=1) {/* ----------------------------------- SLAPEN OF WEG --------------------------------------------*/
		$uit=600;
		foreach (array('lampkast','kristal','garage','tuin','voordeur','zolderg','mac','ipaddock','zetel') as $i) {
			if ($d[$i]['s']=='On') {
				if (past($i)>$uit) {
					sw($i, 'Off', basename(__FILE__).':'.__LINE__);
					lg($i.' uitgeschakeld omdat we slapen of weg zijn');
				}
			}
		}
		foreach (array('eettafel','zithoek','bureellinks','bureelrechts','wasbak','snijplank','hall','inkom','terras') as $i) {
			if ($d[$i]['s']>0) {
				if (past($i)>$uit) {
					sl($i, 0, basename(__FILE__).':'.__LINE__);
					lg($i.' uitgeschakeld omdat we slapen of weg zijn');
				}
			}
		}
	}

	/* -------------------------------------------- ALTIJD BIJ AUT0 ------------------------------------------*/
	
	if ($d['living_set']['m']!=0&&$d['eettafel']['s']==0&&past('living_set')>28800) storemode('living_set', 0, basename(__FILE__).':'.__LINE__);
	if ($d['badkamer_set']['m']!=0&&$d['lichtbadkamer']['s']==0&&past('badkamer_set')>7200) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
	foreach (array('kamer_set','alex_set') as $i) {
		if ($d[$i]['m']!=0&&past($i)>43200) storemode($i, 0, basename(__FILE__).':'.__LINE__);
	}
	if ($d['poortrf']['s']=='On'&&past('poortrf')>600&&past('pirgarage')>600&&past('deurgarage')>600) sw('poortrf', 'Off', basename(__FILE__).':'.__LINE__);

	if ($d['pirliving']['s']=='Off') {
		if ($d['dag']['s']>1) {
			if ($d['rbureel']['s']<40&&$d['rliving']['s']<40) {
				if ($time>strtotime('5:00')&&$time<strtotime('10:00')) {
					if ($d['lampkast']['s']=='On'&&$d['eettafel']['s']<=12) sw('lampkast', 'Off', basename(__FILE__).':'.__LINE__);
					if ($d['zithoek']['s']>0&&$d['zithoek']['s']<20&&$d['eettafel']['s']<=12) sl('zithoek', 0, basename(__FILE__).':'.__LINE__);
					if ($d['bureellinks']['s']>0&&$d['bureellinks']['s']<=24&&$d['eettafel']['s']<=12) sl('bureellinks', 0, basename(__FILE__).':'.__LINE__);
					if ($d['bureelrechts']['s']>0&&$d['bureelrechts']['s']<=24&&$d['eettafel']['s']<=12) sl('bureelrechts', 0, basename(__FILE__).':'.__LINE__);
					if ($d['eettafel']['s']>0&&$d['eettafel']['s']<=12) sl('eettafel', 0, basename(__FILE__).':'.__LINE__);
				} else {
					if ($d['lampkast']['s']=='On'&&$d['eettafel']['s']==0) sw('lampkast', 'Off', basename(__FILE__).':'.__LINE__);
					if ($d['zithoek']['s']>0&&$d['zithoek']['s']<20&&$d['eettafel']['s']==0) sl('zithoek', 0, basename(__FILE__).':'.__LINE__);
					if ($d['bureellinks']['s']>0&&$d['bureellinks']['s']<=24&&$d['eettafel']['s']==0) sl('bureellinks', 0, basename(__FILE__).':'.__LINE__);
					if ($d['bureelrechts']['s']>0&&$d['bureelrechts']['s']<=24&&$d['eettafel']['s']==0) sl('bureelrechts', 0, basename(__FILE__).':'.__LINE__);
				}
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
	
	
	if ($d['weg']['s']>=1&&$d['media']['s']=='On'&&past('weg')>900) sw('media', 'Off', basename(__FILE__).':'.__LINE__);
}
/* -------------------------------------------- ALTIJD ---------------------------------------------------*/
/*if ($d['wasmachine']['s']=='On') {
	if ($d['wasmachine_power']['s']<3.4&&past('wasmachine')>4200&&past('wasmachine_power')>120) {
		hassnotify('Wasmachine', 'klaar');
		sw('wasmachine', 'Off', basename(__FILE__).':'.__LINE__);
	}
}*/
if (
	  $d['daikinliving']['m']==0
	&&$d['daikinkamer']['m']==0
	&&$d['daikinalex']['m']==0
	&&$d['living_set']['s']!='D'
	&&$d['kamer_set']['s']!='D'
	&&$d['alex_set']['s']!='D'
	&&$d['daikin']['s']=='On'
	&&$d['daikin_kwh']['s']<20
	&&past('daikin_kwh')>900
	&&past('daikin')>3600
) sw('daikin', 'Off', basename(__FILE__).':'.__LINE__);
$stamp=date('Y-m-d H:i:s', $timeint-900);
$sql="SELECT AVG(buiten) AS buiten, AVG(living) AS living, AVG(badkamer) AS badkamer, AVG(kamer) AS kamer, AVG(waskamer) AS waskamer, AVG(alex) AS alex, AVG(zolder) AS zolder FROM `temp` WHERE stamp>='$stamp'";
$result=$db->query($sql);
while ($row = $result->fetch(PDO::FETCH_ASSOC)) $avg=$row;
foreach (array('buiten', 'living', 'badkamer', 'kamer', 'waskamer', 'alex', 'zolder') as $i) {
	$diff=$d[$i.'_temp']['s']-$avg[$i];
	if ($d[$i.'_temp']['icon']!=$diff) storeicon($i.'_temp', $diff, basename(__FILE__).':'.__LINE__);
//	if ($d[$i.'_temp']['m']==1&&past($i.'_temp')>21600) storemode($i.'_temp', 0, basename(__FILE__).':'.__LINE__);
}
