<?php
$d=fetchdata();
$s=(int)strftime("%S", TIME);
$dow=date("w");
if($dow==0||$dow==6) $t=strtotime('7:30');
elseif($dow==2||$dow==5) $t=strtotime('6:45');
else $t=strtotime('7:00');

$dag=0;
if (TIME>=$d['civil_twilight']['s']&&TIME<=$d['civil_twilight']['m']) {
	$dag=1;
	if (TIME>=$d['Sun']['s']&&TIME<=$d['Sun']['m']) {
		if (TIME>=$d['Sun']['s']+900&&TIME<=$d['Sun']['m']-900) $dag=4;
		else $dag=3;
	} else {
		$zonop=($d['civil_twilight']['s']+$d['Sun']['s'])/2;
		$zononder=($d['civil_twilight']['m']+$d['Sun']['m'])/2;
		if (TIME>=$zonop&&TIME<=$zononder) $dag=2;
	}
}


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
		if ($d['Grohered_kWh']['s']<50&&past('GroheRed')>180) sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
		elseif (past('GroheRed')>1800) sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
	}
	if (($d['Weg']['s']>0||TIME<=strtotime('18:00'))&&$d['lgtv']['s']=='Off'&&$d['tv']['s']=='On'&&past('tv')>3600&&past('lgtv')>3600) sw('tv', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['poort']['s']=='Closed'&&past('poort')>120&&past('poortrf')>120&&$d['poortrf']['s']=='On'&&(TIME<strtotime('8:00')||TIME>strtotime('8:40'))	) sw('poortrf', 'Off', basename(__FILE__).':'.__LINE__);
	if ($dag==4) {
		if ($d['Rliving']['s']<50&&$d['Rbureel']['s']<50) {
			if ($d['lamp kast']['s']!='Off') sw('lamp kast', 'Off', basename(__FILE__).':'.__LINE__);
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
		&&past('bose101')>300
		&&past('bose102')>300
		&&past('bose103')>300
		&&past('bose104')>300
		&&past('bose105')>300
		&&past('bose106')>300
		&&past('bose107')>300
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

	if ($d['Rliving']['s']>60&&$d['achterdeur']['s']=='Closed') {
		if ($d['tuin']['s']=='On') sw('tuin', 'Off', basename(__FILE__).':'.__LINE__);
		if ($d['terras']['s']>0) sl('terras', 0, basename(__FILE__).':'.__LINE__);
	}
	if ($d['luifel']['s']==0&&$d['ledluifel']['s']>0) {
		sl('ledluifel', 0, basename(__FILE__).':'.__LINE__);
	}
	if ($d['kookplaat']['s']=='On'&&$d['wasbak']['s']==0&&$d['snijplank']['s']==0&&$d['pirkeuken']['s']=='Off'&&past('pirkeuken')>300) {
		if ($d['kookplaatpower_kWh']['s']<200&&past('kookplaatpower_kWh')>300) sw('kookplaat', 'Off', basename(__FILE__).':'.__LINE__);
	}
}
//lg ('Xlight = '.$d['Xlight']['s']);
if ($d['Xlight']['s']!=0&&past('Xlight')>60) sw('Xlight', 'Off', basename(__FILE__).':'.__LINE__);

	/* -------------------------------------------- ALTIJD ----------------------------*/
if (TIME<=strtotime('0:02')) {
	store('gasvandaag', 0, basename(__FILE__).':'.__LINE__);
	store('watervandaag', 0, basename(__FILE__).':'.__LINE__);
}
if ($d['regenpomp']['s']=='On'&&past('regenpomp')>50) sw('regenpomp', 'Off', basename(__FILE__).':'.__LINE__);

if ($d['water']['s']=='On'&&past('water')>$d['water']['m']) sw('water', 'Off');

//if ($d['bose101']['s']=='On'&&past('bose101')<300) bosekey('SHUFFLE_ON', 0, 101);
//elseif ($d['bose101']['s']=='Off'&&$d['bose103']['s']=='On'&&past('bose103')<300) bosekey('SHUFFLE_ON', 0, 103);
