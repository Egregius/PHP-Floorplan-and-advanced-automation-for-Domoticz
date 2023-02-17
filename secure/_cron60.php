<?php
$user='cron60  ';
$stamp=sprintf("%s", date("Y-m-d H:i"));
foreach (array('buiten','living','badkamer','kamer','waskamer','alex','zolder') as $i) ${$i}=$d[$i.'_temp']['s'];
foreach (array('buiten','living','kamer','alex','waskamer','badkamer') as $i) ${$i.'_hum'}=$d[$i.'_temp']['m'];
$query="INSERT IGNORE INTO temp (stamp,buiten,living,badkamer,kamer,waskamer,alex,zolder,living_hum,kamer_hum,alex_hum,waskamer_hum,badkamer_hum,buiten_hum)  VALUES ('$stamp','$buiten','$living','$badkamer','$kamer','$waskamer','$alex','$zolder','$living_hum','$kamer_hum','$alex_hum','$waskamer_hum','$badkamer_hum','$buiten_hum');";

if(isset($db)) $db=dbconnect();
if (!$result = $db->query($query)) die('There was an error running the query ['.$query.' - '.$db->error.']');
foreach (array('living','badkamer','kamer','waskamer','alex','zolder') as $i) $sum=@$sum+$d[$i.'_temp']['s'];
$avg=$sum/6;
foreach (array('living','badkamer','kamer','waskamer','alex','zolder') as $i) {
	if ($d[$i.'_temp']['s']>($avg+5)&&$d[$i.'_temp']['s']>25) alert($i.'temp','OPGELET: '. $d[$i.'_temp']['s'].'° in '.$i,7200,false,2);
}
if ($d['auto']['s']=='On') {
	/* -------------------------------------------- THUIS ----------------------------*/
	if ($d['Weg']['s']==0){

		if ($d['pirliving']['s']=='Off') {
			$uit=6300;
			if (past('pirliving')>$uit) {
				foreach (array('bureel') as $i) if ($d[$i]['s']!='Off'&&past($i)>$uit) sw($i, 'Off', basename(__FILE__).':'.__LINE__);
				foreach (array('eettafel','zithoek') as $i) if ($d[$i]['s']>0&&past($i)>$uit) storemode($i, 1, basename(__FILE__).':'.__LINE__);
			}
			$uit=10800;
			if (past('pirliving')>$uit) {
				foreach (array('kristal','lamp kast') as $i) if ($d[$i]['s']!='Off'&&past($i)>$uit) sw($i, 'Off', basename(__FILE__).':'.__LINE__);
			}
			$uit=14400;
			if (past('pirliving')>$uit) {
				if ($d['sony']['s']=='On'||$d['lgtv']['s']=='On') {
					ud('miniliving4l', 1, 'On');
					lg('miniliving4l pressed omdat er al 4 uur geen beweging is');
				}
			}
		}
		$avg=0;
		foreach (array('living_temp','kamer_temp','waskamer_temp','alex_temp','zolder_temp') as $i) $avg=$avg+$d[$i]['s'];
		$avg=$avg/5;
		foreach (array('living_temp','kamer_temp','waskamer_temp','alex_temp','zolder_temp') as $i) {
			if ($d[$i]['s']>$avg+5&&$d[$i]['s']>25) alert($i,'T '.$i.'='.$d[$i]['s'].'°C. AVG='.round($avg, 1).'°C',3600,false,true);
			if (past($i)>43150) alert($i,$i.' not updated since '.strftime("%k:%M:%S", $d[$i]['t']),7200);
		}

		
	}
	/* -------------------------------------------- SLAPEN OF WEG ---------------*/
	if ($d['Weg']['s']>=1) {
		if ($d['GroheRed']['s']=='On') sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
		$uit=600;
		foreach (array('pirgarage','pirkeuken','pirliving','pirinkom') as $i) {
			if ($d[$i]['s']!='Off') {
				ud($i, 0, 'Off');
				lg($i.' uitgeschakeld omdat we slapen of weg zijn');
			}
		}
		foreach (array('bureel','sony','kristal','garage','tuin','voordeur','zolderg','lamp kast','nvidia') as $i) {
			if ($d[$i]['s']!='Off') {
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
	/* -------------------------------------------- WEG ----------------------------*/
	if ($d['Weg']['s']>=2) {
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
	}

	/* -------------------------------------------- ALTIJD BIJ AUT0----------------------------*/
	if ($d['voordeur']['s']=='On'&&$d['deurvoordeur']['s']=='Closed'&&past('voordeur')>170) sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__);

	if (past('deurbadkamer')>1200&&past('lichtbadkamer')>600) {
		if ($d['lichtbadkamer']['s']>0) {
			$new=round($d['lichtbadkamer']['s'] * 0.85, 0);
			if ($new<10) $new=0;
			sl('lichtbadkamer', $new, basename(__FILE__).':'.__LINE__);
		}
	}
	foreach (array('living_set','badkamer_set') as $i) {
		if ($d[$i]['m']!=0&&past($i)>14400) storemode($i, 0, basename(__FILE__).':'.__LINE__);
	}
	foreach (array('kamer_set','alex_set') as $i) {
		if ($d[$i]['m']!=0&&past($i)>43200) storemode($i, 0, basename(__FILE__).':'.__LINE__);
	}
	if (TIME>=strtotime('10:00')&&TIME<strtotime('10:05')) {
		foreach (array('RkamerL','RkamerR','Rwaskamer','Ralex') as $i) {
			if ($d[$i]['m']!=0) storemode($i, 0, basename(__FILE__).':'.__LINE__);
		}
	}
	if ($d['GroheRed']['s']=='On'&&$d['pirkeuken']['s']=='Off'&&past('pirkeuken')>900) {
		$i=explode(';', $d['Grohered_kWh']['s']);
		if ($i[0]<50&&past('GroheRed')>180) sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
		elseif (past('GroheRed')>1800) sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
	}
	if ($d['luchtdroger']['s']=='On') {
		if ($d['lichtbadkamer']['s']==0&&$d['badkamer_set']['s']<17&&(TIME<=strtotime('4:00')||TIME>=strtotime('8:00'))) {
			$i=explode(';', $d['luchtdroger_kWh']['s']);
			if ($i[0]<100&&past('luchtdroger_kWh')>895) {
				sw('luchtdroger', 'Off', basename(__FILE__).':'.__LINE__);
				if ($d['heating']['s']>1&&$d['deurbadkamer']['s']=='Open') telegram('Deur badkamer dicht doen, luchtdroger klaar');
			}
		}
	} else {
		if ($d['Weg']['s']<=1&&TIME>=strtotime('5:00')&&TIME<=strtotime('7:00')) sw('luchtdroger', 'On', basename(__FILE__).':'.__LINE__);
	}
	if (($d['Weg']['s']>0||TIME<=strtotime('18:00'))&&$d['lgtv']['s']=='Off'&&$d['tv']['s']=='On'&&past('tv')>3600&&past('lgtv')>3600) sw('tv', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['poort']['s']=='Closed'&&past('poort')>120&&past('poortrf')>120&&$d['poortrf']['s']=='On'&&(TIME<strtotime('8:00')||TIME>strtotime('8:40'))	) sw('poortrf', 'Off', basename(__FILE__).':'.__LINE__);
	if (TIME>=$d['civil_twilight']['s']&&TIME<=$d['civil_twilight']['m']&&TIME<=strtotime('16:00')) {
		if ($d['Rliving']['s']<30&&$d['Rbureel']['s']<30&&($d['zon']['s']>0||($d['zon']['s']>20&&TIME>$d['Sun']['s']&&TIME<$d['Sun']['m']))) {
//			if ($d['lamp kast']['s']!='Off') sw('lamp kast', 'Off', basename(__FILE__).':'.__LINE__);
			if ($d['bureel']['s']!='Off') sw('bureel', 'Off', basename(__FILE__).':'.__LINE__);
			if ($d['kristal']['s']!='Off') sw('kristal', 'Off', basename(__FILE__).':'.__LINE__);
		}
	}
	if ($d['wc']['s']=='On' && past('wc')>590 && past('deurwc')>590) sw('wc', 'Off', basename(__FILE__).':'.__LINE__);
	//Bose
	if ($d['pirliving']['s']=='Off'
		&&$d['pirgarage']['s']=='Off'
		&&$d['bose101']['m']==1
		&&$d['bose101']['s']=='On'
		&&$d['bose102']['s']=='Off'
		&&$d['bose103']['s']=='Off'
		&&$d['bose104']['s']=='Off'
		&&$d['bose105']['s']=='Off'
		&&$d['bose106']['s']=='Off'
		&&$d['bose107']['s']=='Off'
		&&past('bose101')>180
		&&past('bose102')>90
		&&past('bose103')>90
		&&past('bose104')>90
		&&past('bose105')>90
		&&past('bose106')>90
		&&past('bose107')>90
		&&(($d['Weg']['s']>0||$d['sony']['s']=='On'||$d['lgtv']['s']=='On')&&$d['eettafel']['s']==0)
	) {
		$status=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.101:8090/now_playing"))),true);
		if (!empty($status)) {
			if (isset($status['@attributes']['source'])) {
				if ($status['@attributes']['source']!='STANDBY') {
					bosekey("POWER", 0, 101);
					if ($d['bose101']['s']!='Off') sw('bose101', 'Off', basename(__FILE__).':'.__LINE__);
					if ($d['bose102']['s']!='Off') sw('bose102', 'Off', basename(__FILE__).':'.__LINE__);
					if ($d['bose103']['s']!='Off') sw('bose103', 'Off', basename(__FILE__).':'.__LINE__);
					if ($d['bose104']['s']!='Off') sw('bose104', 'Off', basename(__FILE__).':'.__LINE__);
					if ($d['bose105']['s']!='Off') sw('bose105', 'Off', basename(__FILE__).':'.__LINE__);
					if ($d['bose106']['s']!='Off') sw('bose106', 'Off', basename(__FILE__).':'.__LINE__);
					if ($d['bose107']['s']!='Off') sw('bose107', 'Off', basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
	//End Bose

	if ($d['kamer']['s']>0&&$d['zon']['s']>200&&$d['RkamerL']['s']<=40&&$d['RkamerR']['s']<=40) {
		if (TIME>strtotime('6:00')&&TIME<strtotime('8:00')) sl('kamer', 0, basename(__FILE__).':'.__LINE__);
		elseif (past('kamer')>900) if ($d['kamer']['m']!=1) storemode('kamer', 1, basename(__FILE__).':'.__LINE__);
	}
	if ($d['waskamer']['s']>0&&$d['zon']['s']>200&&$d['Rwaskamer']['s']==0&&past('waskamer')>900	) {
		if ($d['waskamer']['m']!=1) storemode('waskamer', 1, basename(__FILE__).':'.__LINE__);
	}
	if ($d['alex']['s']>0&&$d['zon']['s']>200&&$d['Ralex']['s']==0&&past('alex')>900) {
		if ($d['alex']['m']!=1) storemode('alex', 1, basename(__FILE__).':'.__LINE__);
	}
	/*if ($d['eettafel']['s']>0
		&&$d['Rbureel']['s']==0
		&&$d['Rliving']['s']==0
		&&$d['zon']['s']>100
		&&past('eettafel')>7200
	) {
		if ($d['eettafel']['m']!=1) storemode('eettafel', 1, basename(__FILE__).':'.__LINE__);
	}*/
	if ($d['zithoek']['s']>0&&$d['Rbureel']['s']==0&&$d['Rliving']['s']==0&&$d['zon']['s']>100&&past('zithoek')>7200) {
		if ($d['zithoek']['m']!=1) storemode('zithoek', 1, basename(__FILE__).':'.__LINE__);
	}
	if ($d['Rliving']['s']>60&&$d['achterdeur']['s']=='Closed') {
		if ($d['tuin']['s']=='On') sw('tuin', 'Off', basename(__FILE__).':'.__LINE__);
		if ($d['terras']['s']>0) sl('terras', 0, basename(__FILE__).':'.__LINE__);
	}
	if ($d['luifel']['s']==0&&$d['ledluifel']['s']>0) {
		sl('ledluifel', 0, basename(__FILE__).':'.__LINE__);
	}
	if ($d['kookplaat']['s']=='On'&&$d['wasbak']['s']==0&&$d['pirkeuken']['s']=='Off'&&past('pirkeuken')>300) {
		$level=explode(';', $d['kookplaatpower_kWh']['s']);
		if ($level[0]<200&&past('kookplaatpower_kWh')>300) sw('kookplaat', 'Off', basename(__FILE__).':'.__LINE__);
	}
}
if (($d['Weg']['s']>0||$d['daikin']['s']=='Off')&&$d['Xlight']['s']!='Off') sw('Xlight', 'Off', basename(__FILE__).':'.__LINE__);

	/* -------------------------------------------- ALTIJD ----------------------------*/
if (TIME<=strtotime('0:03')) {
	store('gasvandaag', 0, basename(__FILE__).':'.__LINE__);
	store('watervandaag', 0, basename(__FILE__).':'.__LINE__);
}
if ($d['regenpomp']['s']=='On'&&past('regenpomp')>40) sw('regenpomp', 'Off', basename(__FILE__).':'.__LINE__);

if ($d['water']['s']=='On'&&past('water')>$d['water']['m']) sw('water', 'Off');

if ($d['bose101']['s']=='On'&&past('bose101')<300) bosekey('SHUFFLE_ON', 0, 101);
elseif ($d['bose101']['s']=='Off'&&$d['bose103']['s']=='On'&&past('bose103')<300) bosekey('SHUFFLE_ON', 0, 103);
