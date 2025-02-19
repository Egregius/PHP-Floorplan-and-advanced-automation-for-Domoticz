<?php
$d=fetchdata($time);
$t=t();

$user=basename(__FILE__);
//lg($user);
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
	if ($d['Weg']['s']==0){/* ----------------------------------------- THUIS ----------------------------------------------------*/
//		if ($d['zon']==0&&$d['tuintafel']['s']=='Off'&&$d['Rliving']['s']<50) sw('tuintafel', 'On', basename(__FILE__).':'.__LINE__);
//		elseif (($d['zon']>0||$d['Rliving']['s']>50)&&$d['tuintafel']['s']=='On') sw('tuintafel', 'Off', basename(__FILE__).':'.__LINE__);
		if ($d['pirliving']['s']=='Off') {
			$uit=6300;
			if (past('pirliving')>$uit) {
				foreach (array('bureel') as $i) if ($d[$i]['s']=='On'&&past($i)>$uit) sw($i, 'Off', basename(__FILE__).':'.__LINE__);
			}
			$uit=10800;
			if (past('pirliving')>$uit) {
				foreach (array('kristal','lamp kast') as $i) if ($d[$i]['s']=='On'&&past($i)>$uit) sw($i, 'Off', basename(__FILE__).':'.__LINE__);
			}
		}
		$avg=0;
		foreach (array('living_temp','kamer_temp','waskamer_temp','alex_temp','zolder_temp') as $i) $avg=$avg+$d[$i]['s'];
		$avg=$avg/5;
		foreach (array('living_temp','kamer_temp','waskamer_temp','alex_temp','zolder_temp') as $i) {
			if ($d[$i]['s']>$avg+5&&$d[$i]['s']>25) alert($i,'T '.$i.'='.$d[$i]['s'].'°C. AVG='.round($avg, 1).'°C',3600,false,true);
		}
		if ($d['lg_webos_tv_cd9e']['s']=='On') {
			if ($d['dag']<=1&&$d['kristal']['s']=='Off'&&past('kristal')>7200) sw('kristal', 'On', basename(__FILE__).':'.__LINE__);
		}
	}elseif ($d['Weg']['s']>=2) {/* ----------------------------------- WEG ------------------------------------------------------*/
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
	}elseif ($d['Weg']['s']>=1) {/* ----------------------------------- SLAPEN OF WEG --------------------------------------------*/
		$uit=600;
		foreach (array('pirgarage','pirkeuken','pirliving','pirinkom') as $i) {
			if ($d[$i]['s']!='Off') {
				ud($i, 0, 'Off');
				lg($i.' uitgeschakeld omdat we slapen of weg zijn');
			}
		}
		foreach (array('lamp kast','bureel','kristal','garage','tuin','voordeur','zolderg','langekast') as $i) {
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

	if (past('deurbadkamer')>1800&&past('lichtbadkamer')>600) {
		if ($d['lichtbadkamer']['s']>0) {
			$new=round($d['lichtbadkamer']['s'] * 0.85, 0);
			if ($new<10) $new=0;
			sl('lichtbadkamer', $new, basename(__FILE__).':'.__LINE__);
		}
	}
	if ($d['living_set']['m']!=0&&$d['eettafel']['m']==0&&past('living_set')>28800) storemode('living_set', 0, basename(__FILE__).':'.__LINE__);
	if ($d['badkamer_set']['m']!=0&&$d['lichtbadkamer']['m']==0&&past('badkamer_set')>7200) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
	foreach (array('kamer_set','alex_set') as $i) {
		if ($d[$i]['m']!=0&&past($i)>43200) storemode($i, 0, basename(__FILE__).':'.__LINE__);
	}
	if ($d['GroheRed']['s']=='On'&&$d['pirkeuken']['s']=='Off'&&past('pirkeuken')>900) {
		$past=past('GroheRed');
		if ($d['Grohered_kWh']['s']<50&&$past>180) {
			sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
			storemode('GroheRed', 0, basename(__FILE__).':'.__LINE__);
		} elseif ($past>1800) {
			sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
			storemode('GroheRed', 0, basename(__FILE__).':'.__LINE__);
		}
	}
	if ($d['poortrf']['s']=='On'&&past('poortrf')>600&&past('pirgarage')>600&&past('deurgarage')>600) sw('poortrf', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['dag']>3) {
		if ($d['Rbureel']['s']<40) {
			if ($d['lamp kast']['s']=='On'&&$d['eettafel']['s']==0) sw('lamp kast', 'Off', basename(__FILE__).':'.__LINE__);
			if ($d['bureel']['s']=='On') sw('bureel', 'Off', basename(__FILE__).':'.__LINE__);
			if ($d['kristal']['s']=='On') sw('kristal', 'Off', basename(__FILE__).':'.__LINE__);
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
		&&(($d['Weg']['s']>0||$d['lg_webos_tv_cd9e']['s']=='On')&&$d['eettafel']['s']==0)
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
		if ($d['tuintafel']['s']=='On') sw('tuintafel', 'Off', basename(__FILE__).':'.__LINE__);
		if ($d['steenterras']['s']=='On') sw('steenterras', 'Off', basename(__FILE__).':'.__LINE__);
		if ($d['terras']['s']>0) sl('terras', 0, basename(__FILE__).':'.__LINE__);
	}
	
	if ($d['kookplaat']['s']=='On'&&$d['wasbak']['s']==0&&$d['snijplank']['s']==0&&$d['pirkeuken']['s']=='Off'&&past('pirkeuken')>300) {
		if ($d['kookplaatpower_kWh']['s']<100&&past('kookplaatpower_kWh')>300) sw('kookplaat', 'Off', basename(__FILE__).':'.__LINE__);
	}
}

/* -------------------------------------------- ALTIJD ---------------------------------------------------*/

if ($d['luifel']['s']==0&&$d['ledluifel']['s']>0) sl('ledluifel', 0, basename(__FILE__).':'.__LINE__);

$data=json_decode(file_get_contents('http://127.0.0.1:8080/json.htm?type=command&param=getdevices&rid=1'));
if (isset($data->CivTwilightStart)) {
	$CivTwilightStart=strtotime($data->CivTwilightStart);
	$CivTwilightEnd=strtotime($data->CivTwilightEnd);
	$Sunrise=strtotime($data->Sunrise);
	$Sunset=strtotime($data->Sunset);
	$dag=0;
	if ($time>=$CivTwilightStart&&$time<=$CivTwilightEnd) {
		$dag=1;
		if ($time>=$Sunrise&&$time<=$Sunset) {
			if ($time>=$Sunrise+900&&$time<=$Sunset-900) $dag=4;
			else $dag=3;
		} else {
			$zonop=($CivTwilightStart+$Sunrise)/2;
			$zononder=($CivTwilightEnd+$Sunset)/2;
			if ($time>=$zonop&&$time<=$zononder) $dag=2;
		}
	}
	$prevdag=mget('dag');
	if ($dag!=$prevdag) mset('dag',$dag);
} else lg('Error fetching CivTwilightStart from domoticz');
