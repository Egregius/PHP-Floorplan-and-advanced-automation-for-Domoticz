<?php
/**
 * Pass2PHP
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
//lg(__FILE__.':'.$s);
$user='cron60  ';
$stamp=sprintf("%s", date("Y-m-d H:i"));
foreach (array('buiten','living','badkamer','kamer','speelkamer','alex','zolder') as $i) ${$i}=$d[$i.'_temp']['s'];
$query="INSERT IGNORE INTO `temp` (`stamp`,`buiten`,`living`,`badkamer`,`kamer`,`speelkamer`,`alex`,`zolder`) VALUES ('$stamp','$buiten','$living','$badkamer','$kamer','$speelkamer','$alex','$zolder');";
if(isset($db)) $db=dbconnect();
if (!$result = $db->query($query)) die('There was an error running the query ['.$query.' - '.$db->error.']');
foreach (array('living','badkamer','kamer','speelkamer','alex','zolder') as $i) $sum=@$sum+$d[$i.'_temp']['s'];
$avg=$sum/6;
foreach (array('living','badkamer','kamer','speelkamer','alex','zolder') as $i) {
	if ($d[$i.'_temp']['s']>($avg+5)&&$d[$i.'_temp']['s']>25) alert($i.'temp','OPGELET: '. $d[$i.'_temp']['s'].'° in '.$i,7200,false,2);
}
if ($d['auto']['s']=='On') {
	/* -------------------------------------------- THUIS ----------------------------*/
	if ($d['Weg']['s']==0){
		if ($d['pirkeuken']['s']=='Off') {
			$uit=235;
			if (past('pirkeuken')>$uit) {
				foreach (array('keuken') as $i) if ($d[$i]['s']!='Off'&&past($i)>$uit) sw($i, 'Off', basename(__FILE__).':'.__LINE__);
				foreach (array('wasbak') as $i) if ($d[$i]['s']>0&&past($i)>$uit) sl($i, $d[$i]['m'], basename(__FILE__).':'.__LINE__);
			}
		}
		if ($d['pirliving']['s']=='Off') {
			$uit=6300;
			if (past('pirliving')>$uit) {
				foreach (array('bureel') as $i) if ($d[$i]['s']!='Off'&&past($i)>$uit) sw($i, 'Off', basename(__FILE__).':'.__LINE__);
				foreach (array('eettafel','zithoek') as $i) if ($d[$i]['s']>0&&past($i)>$uit) storemode($i, 1, basename(__FILE__).':'.__LINE__);
			}
			$uit=10800;
			if (past('pirliving')>$uit) {
				foreach (array('tvled','kristal','jbl') as $i) if ($d[$i]['s']!='Off'&&past($i)>$uit) sw($i, 'Off', basename(__FILE__).':'.__LINE__);
			}
			$uit=14400;
			if (past('pirliving')>$uit) {
				if ($d['denon']['s']=='On'||$d['lgtv']['s']=='On') {
					ud('miniliving4l', 1, 'On');
					lg('miniliving4l pressed omdat er al 4 uur geen beweging is');
				}
			}
		}
		$avg=0;
		foreach (array('living_temp','kamer_temp','speelkamer_temp','alex_temp','zolder_temp') as $i) $avg=$avg+$d[$i]['s'];
		$avg=$avg/5;
		foreach (array('living_temp','kamer_temp','speelkamer_temp','alex_temp','zolder_temp') as $i) {
			if ($d[$i]['s']>$avg+5&&$d[$i]['s']>25) alert($i,'T '.$i.'='.$d[$i]['s'].'°C. AVG='.round($avg, 1).'°C',3600,false,true);
			if (past($i)>43150) alert($i,$i.' not updated since '.strftime("%k:%M:%S", $d[$i]['t']),7200);
		}
	}
	/* -------------------------------------------- THUIS OF SLAPEN --------------*/
	if ($d['Weg']['s']<=1) {
		foreach (array('eettafel','zithoek','speelkamer','kamer','alex','zolder') as $i) {
			if ($d[$i]['s']!=0) {
				if (past($i)>58) {
					if ($d[$i]['m']==1) {
						$level=floor($d[$i]['s']*0.95);
						if ($level<2) $level=0;
						if ($level==20) $level=19;
						sl($i, $level, basename(__FILE__).':'.__LINE__);
						if ($level==0) storemode($i, 0, basename(__FILE__).':'.__LINE__);
					} elseif ($d[$i]['m']==2) {
						$level=$d[$i]['s']+1;
						if ($level==20) $level=21;
						if ($level>100) $level=100;
						sl($i, $level, basename(__FILE__).':'.__LINE__);
					}
				}
			} elseif ($d[$i]['s']==0&&$i=='alex') {
				if ($d[$i]['m']==3) {
					if ($d['raamalex']['s']=='Open') storemode('alex', 0, basename(__FILE__).':'.__LINE__);
					else {
						if (past($item)>10800) {
							sl('alex', 2, basename(__FILE__).':'.__LINE__);
							storemode($item, 2, basename(__FILE__).':'.__LINE__);
						}
					}
				}
			}
		}
//		if ($d['Weg']['s']==0&&TIME>=strtotime('5:00')&&TIME<strtotime('20:00')) {
//			if ($d['luchtwasser']['s']=='Off') sw('luchtwasser', 'On', basename(__FILE__).':'.__LINE__);
//		} else {
//			if ($d['luchtwasser']['s']=='On') sw('luchtwasser', 'Off', basename(__FILE__).':'.__LINE__);
//		}
	}
	/* -------------------------------------------- SLAPEN --------------------------*/
	if ($d['Weg']['s']==1) {

	}
	/* -------------------------------------------- SLAPEN OF WEG ---------------*/
	if ($d['Weg']['s']>=1) {
		$uit=600;
		foreach (array('pirgarage','pirkeuken','pirliving','pirinkom') as $i) {
			if ($d[$i]['s']!='Off') {
				ud($i, 0, 'Off');
				lg($i.' uitgeschakeld omdat we slapen of weg zijn');
			}
		}
		foreach (array('bureel','denon','kristal','garage','tuin','voordeur','keuken','zolderg','dampkap','jbl') as $i) {
			if ($d[$i]['s']!='Off') {
				if (past($i)>$uit) {
					sw($i, 'Off', basename(__FILE__).':'.__LINE__);
					lg($i.' uitgeschakeld omdat we slapen of weg zijn');
				}
			}
		}
		foreach (array('eettafel','zithoek','wasbak','hall','inkom','terras') as $i) {
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
		foreach (array('pirgarage','pirkeuken','pirliving','pirinkom','pirhall') as $i) {
			if ($d[$i]['s']!='Off') {
				if (past($i)>$uit) {
					ud($i, 0, 'Off');
					lg($i.' uitgeschakeld omdat we weg zijn');
				}
			}
		}
		foreach (array('garage','denon','bureel','kristal','tuin','voordeur','keuken','badkamervuur2','badkamervuur1','zolderg') as $i) {
			if ($d[$i]['s']!='Off') {
				if (past($i)>$uit) {
					if ($d[$i]['s']!='Off') {
						sw($i, 'Off', basename(__FILE__).':'.__LINE__);
						lg($i.' uitgeschakeld omdat we weg zijn');
					}
				}
			}
		}
		foreach (array('eettafel','zithoek','wasbak','hall','inkom','kamer','speelkamer','alex','terras','lichtbadkamer') as $i) {
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
	foreach (array('living_set','badkamer_set','kamer_set','speelkamer_set','alex_set') as $i) {
		if ($d[$i]['m']!=0&&past($i)>43200) storemode($i, 0, basename(__FILE__).':'.__LINE__);
	}

	if (TIME>=strtotime('10:00')&&TIME<strtotime('10:05')) {
		foreach (array('RkamerL','RkamerR','Rspeelkamer','Ralex') as $i) {
			if ($d[$i]['m']!=0) storemode($i, 0, basename(__FILE__).':'.__LINE__);
		}
	}

	if (($d['Weg']['s']>0||TIME<=strtotime('18:00'))&&$d['lgtv']['s']=='Off'&&$d['tv']['s']=='On'&&past('tv')>3600&&past('lgtv')>3600) sw('tv', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['poort']['s']=='Closed'&&past('poort')>120&&past('poortrf')>120&&$d['poortrf']['s']=='On'&&(TIME<strtotime('8:00')||TIME>strtotime('8:40'))	) sw('poortrf', 'Off', basename(__FILE__).':'.__LINE__);
	if (TIME>=$d['civil_twilight']['s']&&TIME<=$d['civil_twilight']['m']) {
		if ($d['Rliving']['s']<30&&$d['Rbureel']['s']<30&&($d['zon']['s']>0||($d['zon']['s']>20&&TIME>$d['Sun']['s']&&TIME<$d['Sun']['m']))) {
			if ($d['jbl']['s']!='Off') sw('jbl', 'Off', basename(__FILE__).':'.__LINE__);
			if ($d['bureel']['s']!='Off') sw('bureel', 'Off', basename(__FILE__).':'.__LINE__);
			if ($d['kristal']['s']!='Off') sw('kristal', 'Off', basename(__FILE__).':'.__LINE__);
		}
	}
	if (
		(
			($d['garage']['s']=='On'&&past('garage')>180)
			||
			($d['pirgarage']['s']=='On'&&past('pirgarage')>180)
		)
		&&TIME>strtotime('7:00')
		&&TIME<strtotime('23:00')
		&&$d['poort']['s']=='Closed'
		&&$d['achterdeur']['s']=='Closed'
	) {
		if ($d['dampkap']['s']=='Off') {
			double('dampkap', 'On');
			storemode('dampkap', TIME+300);
		}
	} elseif (
		($d['garage']['s']=='Off'&&past('garage')>270&&$d['pirgarage']['s']=='Off'&&past('pirgarage')>270)
		||$d['poort']['s']=='Open'
		||$d['achterdeur']['s']=='Open'
	) {
		if ($d['dampkap']['s']=='On') {
			if (TIME>$d['dampkap']['m']) {
					lg('TIME='.TIME.' M='.$d['dampkap']['m']);
					double('dampkap', 'Off', basename(__FILE__).':'.__LINE__);
			}
		}
	}
	if ($d['wc']['s']=='On' && past('wc')>590 && past('deurwc')>590) sw('wc', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['denon']['s']=='On') {
		$denonmain=json_decode(json_encode(simplexml_load_string(@file_get_contents('http://192.168.2.5/goform/formMainZone_MainZoneXml.xml?_='.TIME,false,$ctx))),true);
		if (!empty($denonmain)) {
			if ($denonmain['InputFuncSelect']['value']!=$d['denon']['m']) storemode('denon', $denonmain['InputFuncSelect']['value'], basename(__FILE__).':'.__LINE__);
			if ($denonmain['ZonePower']['value']!=$d['denonpower']['s']) store('denonpower', $denonmain['ZonePower']['value'], basename(__FILE__).':'.__LINE__);
			$denonsec=json_decode(json_encode(simplexml_load_string(@file_get_contents('http://192.168.2.5/goform/formZone2_Zone2XmlStatusLite.xml?_='.TIME,false,$ctx))),true);
			if ($denonmain['ZonePower']['value']=='ON'&&$denonsec['Power']['value']=='OFF') denon('Z2ON');
			elseif ($denonmain['ZonePower']['value']=='OFF'&&$denonsec['Power']['value']=='ON') denon('Z2OFF');
		}
	}
		//Bose
	if ($d['pirliving']['s']=='Off'
		&&$d['pirgarage']['s']=='Off'
		&&$d['bose101']['m']==1
		&&$d['bose101']['s']=='On'
		&&$d['bose102']['s']=='Off'
		&&$d['bose103']['s']=='Off'
		&&$d['bose104']['s']=='Off'
		&&$d['bose105']['s']=='Off'
		&&past('bose101')>180
		&&past('bose102')>90
		&&past('bose103')>90
		&&past('bose104')>90
		&&past('bose105')>90
		&&(($d['Weg']['s']>0||$d['denonpower']['s']=='ON'||$d['denon']['s']=='On'||$d['lgtv']['s']=='On')&&$d['eettafel']['s']==0)
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
				}
			}
		}
	}

	if ($d['garage']['s']=='Off'&&$d['pirgarage']['s']=='Off'&&$d['bose101']['m']==1&&past('pirgarage')>90&&past('deurgarage')>90&&past('bose104')>90&&$d['poortrf']['s']=='Off'&&$d['deurgarage']['s']=='Closed'&&$d['bose104']['s']=='On') {
		$status=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.104:8090/now_playing"))),true);
		if (!empty($status)) {
			if (isset($status['@attributes']['source'])) {
				if ($status['@attributes']['source']!='STANDBY') {
					if ($d['bose104']['s']!='Off') {
						sw('bose104', 'Off', basename(__FILE__).':'.__LINE__);
						$d['bose104']['s']=='Off';
						bosekey("POWER", 0, 104);
					}
				}
			}
		}
	}
	foreach (array(101,102,103,104,105) as $i) {
		echo 'Checking bose'.$i.'<br>';
		$status=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.$i:8090/now_playing"))),true);
		if (!empty($status)) {
			if (isset($status['@attributes']['source'])) {
				if ($status['@attributes']['source']=='STANDBY') {
					if ($d['bose'.$i]['s']!='Off') store('bose'.$i, 'Off', basename(__FILE__).':'.__LINE__);
				} else {
					if ($d['bose'.$i]['s']!='On') {
						store('bose'.$i, 'On', basename(__FILE__).':'.__LINE__);
						bosekey('SHUFFLE_ON', 0, $i);
					}
				}
			}
		}
	}
	//End Bose

	if ($d['kamer']['s']>0&&$d['zon']['s']>200&&$d['RkamerL']['s']<=40&&$d['RkamerR']['s']<=40) {
		if (TIME>strtotime('6:00')&&TIME<strtotime('8:00')) sl('kamer', 0, basename(__FILE__).':'.__LINE__);
		elseif (past('kamer')>900) if ($d['kamer']['m']!=1) storemode('kamer', 1, basename(__FILE__).':'.__LINE__);
	}
	if ($d['speelkamer']['s']>0&&$d['zon']['s']>200&&$d['Rspeelkamer']['s']==0&&past('speelkamer')>900	) {
		if ($d['speelkamer']['m']!=1) storemode('speelkamer', 1, basename(__FILE__).':'.__LINE__);
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
	if ($d['zithoek']['s']>0	&&$d['Rbureel']['s']==0&&$d['Rliving']['s']==0&&$d['zon']['s']>100&&past('zithoek')>7200) {
		if ($d['zithoek']['m']!=1) storemode('zithoek', 1, basename(__FILE__).':'.__LINE__);
	}
	if ($d['Rliving']['s']>60&&$d['achterdeur']['s']=='Closed') {
		if ($d['tuin']['s']=='On') sw('tuin', 'Off', basename(__FILE__).':'.__LINE__);
		if ($d['terras']['s']>0) sl('terras', 0, basename(__FILE__).':'.__LINE__);
	}
	if ($d['luifel']['s']==0&&$d['ledluifel']['s']>0) {
		sl('ledluifel', 0, basename(__FILE__).':'.__LINE__);
	}
	if ($d['kookplaatpower']['s']=='On'&&$d['wasbak']['s']==0&&$d['pirkeuken']['s']=='Off') {
		$level=explode(';', $d['kookplaatpower_kWh']['s']);
		if ($level[0]<200&&past('kookplaatpower_kWh')>2400) sw('kookplaatpower', 'Off', basename(__FILE__).':'.__LINE__);
	}
}
if (($d['Weg']['s']>0||$d['daikin']['s']=='Off')&&$d['Xlight']['s']>0) sw('Xlight', 'Off', basename(__FILE__).':'.__LINE__);

	/* -------------------------------------------- ALTIJD ----------------------------*/
if (TIME<=strtotime('0:03')) {
	store('gasvandaag', 0, basename(__FILE__).':'.__LINE__);
	store('watervandaag', 0, basename(__FILE__).':'.__LINE__);
}
if ($d['regenpomp']['s']=='On'&&past('regenpomp')>40) sw('regenpomp', 'Off', basename(__FILE__).':'.__LINE__);

/*if ($d['zon']['s']-$d['el']['s']<-200) $set=$d['diepvries']['m']-10;
else */$set=$d['diepvries']['m'];

if ($d['diepvries']['s']!='On'&&$d['diepvries_temp']['s']>$set&&past('diepvries')>1780) sw('diepvries', 'On', 'Zon: '.$d['zon']['s'].' El: '.$d['el']['s'].' '.'Set: '.$set.' - '.basename(__FILE__).':'.__LINE__);
elseif ($d['diepvries']['s']!='Off'&&$d['diepvries_temp']['s']<=$set &&past('diepvries')>280) sw('diepvries', 'Off', 'Zon: '.$d['zon']['s'].' El: '.$d['el']['s'].' '.'Set: '.$set.' - '.basename(__FILE__).':'.__LINE__);
elseif ($d['diepvries']['s']!='Off'&&past('diepvries')>14400) sw('diepvries', 'Off', 'Diepvries meer dan 4 uur aan. - '.basename(__FILE__).':'.__LINE__);

if ($d['water']['s']=='On'&&past('water')>$d['water']['m']) sw('water', 'Off');

if ($d['bose101']['s']=='On'&&past('bose101')<300) bosekey('SHUFFLE_ON', 0, 101);
elseif ($d['bose101']['s']=='Off'&&$d['bose103']['s']=='On'&&past('bose103')<300) bosekey('SHUFFLE_ON', 0, 103);
