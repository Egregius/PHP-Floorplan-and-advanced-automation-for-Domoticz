<?php
/**
 * Pass2PHP Temperature Control Airco cooling
 * php version 7.3
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
lg('_TC_aircocooling');
foreach	(array('zoldervuur1', 'zoldervuur2', 'brander', 'badkamervuur1', 'badkamervuur2') as $i) {
	if ($d[$i]['s']!='Off') sw($i, 'Off', basename(__FILE__).':'.__LINE__);
}

$Setkamer=33;
if ($d['kamer_set']['m']==0) {
    if (
			(
					$d['raamkamer']['s']=='Closed'
				||	$d['RkamerR']['s']>=80
				
			)
		&&	
			(
					past('raamkamer')>900
				||	TIME>strtotime('19:00')
			)
		&&	
			(
				(
						$d['deurkamer']['s']=='Closed'
					||
						(
								$d['deurkamer']['s']=='Open'
							&&	past('deurkamer')<300
						)
				)
			||
				(
					(
							$d['deuralex']['s']=='Closed'
						||	$d['raamalex']['s']=='Closed'
						||	$d['Ralex']['s']>=80
					)
				&&
					(
							$d['deurtobi']['s']=='Closed'
						||	$d['raamtobi']['s']=='Closed'
						||	$d['Rtobi']['s']>=80
					)
				&& $d['raamhall']['s']=='Closed'
				)
    		)
    ) {
        if (TIME<strtotime('6:30')) $Setkamer=20;
        elseif (TIME>strtotime('22:00')) $Setkamer=20;
        elseif (TIME>strtotime('20:00')) $Setkamer=20.5;
        elseif (TIME>strtotime('18:00')) $Setkamer=21;
        elseif (TIME>strtotime('16:00')) $Setkamer=21.5;
        elseif (TIME>strtotime('14:00')) $Setkamer=22;
        elseif (TIME>strtotime('12:00')) $Setkamer=22.5;
        elseif (TIME>strtotime('10:00')) $Setkamer=23;
        elseif (TIME>strtotime('8:00')) $Setkamer=23.5;
    }
    if ($d['kamer_set']['s']!=$Setkamer) {
        store('kamer_set', $Setkamer, basename(__FILE__).':'.__LINE__);
        $d['kamer_set']['s']=$Setkamer;
    }
}

$Setalex=33;
if ($d['alex_set']['m']==0) {
    if (
			(
					$d['raamalex']['s']=='Closed'
				||	$d['Ralex']['s']>=80
			)
		&&
			(
					past('raamalex')>900
				|| TIME>strtotime('19:00')
			)
		&&
			(
				(
						$d['deuralex']['s']=='Closed'
					||
						(
								$d['deuralex']['s']=='Open'
							&&	past('deuralex')<300
						)
				)
			||
				(
					(
							$d['deurkamer']['s']=='Closed'
						||	$d['raamkamer']['s']=='Closed'
						||	$d['RkamerR']['s']>=80
					)
				&&
					(
							$d['deurtobi']['s']=='Closed'
						||	$d['raamtobi']['s']=='Closed'
						||	$d['Rtobi']['s']>=80
					)
				&& $d['raamhall']['s']=='Closed'
				)
			)
    ) {
        if (TIME<strtotime('6:30')) $Setalex=21;
        elseif (TIME>strtotime('19:00')) $Setalex=21;
        elseif (TIME>strtotime('17:00')) $Setalex=21.5;
        elseif (TIME>strtotime('15:00')) $Setalex=22;
        elseif (TIME>strtotime('13:00')) $Setalex=22.5;
        elseif (TIME>strtotime('11:00')) $Setalex=23;
        elseif (TIME>strtotime('9:00')) $Setalex=23.5;
    }
    if ($d['alex_set']['s']!=$Setalex) {
        store('alex_set', $Setalex, basename(__FILE__).':'.__LINE__);
        $d['alex_set']['s']=$Setalex;
    }
}

$Setliving=33;
if ($d['living_set']['m']==0) {
    if (
    	($d['raamliving']['s']=='Closed'||($d['raamliving']['s']=='Open'&&past('raamliving')<300))
    	&&	($d['raamkeuken']['s']=='Closed'||($d['raamkeuken']['s']=='Open'&&past('raamkeuken')<300))
    	&&	($d['deurinkom']['s']=='Closed'||($d['deurvoordeur']['s']=='Closed'&&$d['deurinkom']['s']=='Open'))
    	&&	($d['deurgarage']['s']=='Closed'||($d['deurgarage']['s']=='Open'&&past('deurgarage')<300))
	) {
		$Setliving=23;
    }
    if ($d['living_set']['s']!=$Setliving&&past('raamliving')>60&&($d['deurinkom']['s']=='Closed'||past('deurinkom')>60)&&($d['deurgarage']['s']=='Closed'||past('deurgarage')>60)) {
        store('living_set', $Setliving, basename(__FILE__).':'.__LINE__);
        $d['living_set']['s']=$Setliving;
    }
}
$bigdif=100;
foreach (array('living', 'kamer', 'alex') as $k) {
	${'dif'.$k}=number_format($d[$k.'_temp']['s']-$d[$k.'_set']['s'], 1);
    if (${'dif'.$k}<$bigdif) $bigdif=${'dif'.$k};
    $daikin=json_decode($d['daikin'.$k]['s']);
	/*if (${'dif'.$k}>=0.8) {$rate=7;$d[$k.'_set']['s']=$d[$k.'_set']['s']-1.5;$power=1;}
	elseif (${'dif'.$k}>=0.6) {$rate=6;$d[$k.'_set']['s']=$d[$k.'_set']['s']-1;$power=1;}
	elseif (${'dif'.$k}>=0.4) {$rate=5;$d[$k.'_set']['s']=$d[$k.'_set']['s']-1;$power=1;}
	elseif (${'dif'.$k}>=0.2) {$rate=4;$d[$k.'_set']['s']=$d[$k.'_set']['s']-0.5;$power=1;}
	elseif (${'dif'.$k}>=0) {$rate=3;$d[$k.'_set']['s']=$d[$k.'_set']['s']-0.5;$power=1;}
	elseif (${'dif'.$k}>=-0.1) {$rate=3;$d[$k.'_set']['s']=$d[$k.'_set']['s'];$power=1;}
	elseif (${'dif'.$k}>=-0.2) {$rate=3;$d[$k.'_set']['s']=$d[$k.'_set']['s']+0.5;$power=1;}
	elseif (${'dif'.$k}>=-0.3) {$rate='B';$d[$k.'_set']['s']=$d[$k.'_set']['s']+1;$power=1;}
	elseif (${'dif'.$k}>=-0.4) {$rate='B';$d[$k.'_set']['s']=$d[$k.'_set']['s']+2;$power=1;}
	else {$rate='B';$d[$k.'_set']['s']=32;$power=0;}*/
	
	if (${'dif'.$k}>=-1) {
		$rate='A';
		$power=1;
	} else {
		$rate='B';
		$d[$k.'_set']['s']=32;
		$power=0;
	}
	
	if ($k=='kamer'||$k=='alex') {
		if (TIME>=strtotime('8:00')&&TIME<strtotime('17:00')&&$d[$k.'_set']['m']==0&&$d['buiten_temp']['s']<30) {
			if ($d[$k.'_set']['s']<$d[$k.'_temp']['s']) $d[$k.'_set']['s']=floor(($d[$k.'_temp']['s']-0.1)*2)/2;
//			if ($rate!='B'&&$rate>4) $rate=4;
//			elseif ($rate=='B') $rate=3;
		}
	}
	if ($daikin->adv == '') {
		$streamer=0;
		$powermode=0;
	} else if (strstr($daikin->adv, '/')) { 
		$advs=explode("/", $daikin->adv);
		if ($advs[1]==13) $streamer=1;
		else if ($adv[1]=='') $streamer=0;
		if ($advs[0]==2) $powermode=2;
		else if ($advs[0]==12) $powermode=1;
		else $powermode=0;
	} else {
		if ($daikin->adv==13) {
			$streamer=1;
			$powermode=0; //Normal
		} else if ($daikin->adv==12) {
			$streamer=0;
			$powermode=1; // Eco
		} else if ($daikin->adv==2) {
			$streamer=0;
			$powermode=2; // Power
		} else if ($daikin->adv=='') {
			$streamer=0;
			$powermode=0;
		}
	}
	if ($d[$k.'_set']['s']<18) $d[$k.'_set']['s']=18;
	
	if ($k=='living'&&$d['eettafel']['s']>0) $rate='B';
	if ($k=='kamer'&&$d['Weg']['s']==1) $rate='B';
	if ($k=='alex'&&(TIME>strtotime('20:00')||TIME<strtotime('08:00'))) $rate='B';
	//lg ($k.' = '.$powermode);
	
	if ($powermode<2) {
		if ($d[$k.'_set']['s']<30) {
			if ($daikin->stemp!=$d[$k.'_set']['s']||$daikin->pow!=$power||$daikin->mode!=3||$daikin->f_rate!=$rate) {
				daikinset($k, $power, 3, $d[$k.'_set']['s'], basename(__FILE__).':'.__LINE__, $rate);
				storemode('daikin'.$k, 3);
				if ($k=='living') $ip=111;
				elseif ($k=='kamer') $ip=112;
				elseif ($k=='alex') $ip=113;
				if (TIME>strtotime('8:00')||TIME<strtotime('19:00')) $setstreamer=1;
				else $setstreamer=0;
			
				$data=json_decode($d[$k.'_set']['icon'], true);
				$data=array();
				$data['power']=$power;
				$data['mode']=3;
				$data['fan']=$rate;
				$data['set']=$d[$k.'_set']['s'];
				if ($streamer!=$setstreamer) {
					sleep(1);
					file_get_contents('http://192.168.2.'.$ip.'/aircon/set_special_mode?en_streamer='.$setstreamer);
					$data['streamer']=$setstreamer;
				}
				storeicon($k.'_set', json_encode($data));
			}
		} else {
			if ($daikin->pow!=$power||$daikin->mode!=3) {
				daikinset($k, $power, 3, $d[$k.'_set']['s'], basename(__FILE__).':'.__LINE__);
				storemode('daikin'.$k, 0);
				$data=json_decode($d[$k.'_set']['icon'], true);
				$data['power']=$power;
				$data['mode']=3;
				$data['set']=$d[$k.'_set']['s'];
				storeicon($k.'_set', json_encode($data));
			}
		}
	}
}

foreach (array('kamer', 'tobi', 'alex') as $k) {
    if (round($d[$k.'Z']['s'], 1)>4) {
        ud($k.'Z', 0, '4.0', basename(__FILE__).':'.__LINE__);
    }
}
foreach (array('kamer', 'tobi', 'alex') as $k) {
    if (round($d[$k.'Z']['s'], 1)>4) {
        ud($k.'Z', 0, '4.0', basename(__FILE__).':'.__LINE__);
    }
}


$boven=array('Rtobi','Ralex','RkamerL','RkamerR');
$beneden=array('Rbureel','RkeukenL','RkeukenR');
$benedenall=array('Rliving','Rbureel','RkeukenL','RkeukenR');
if (TIME>=$d['civil_twilight']['s']&&TIME<=$d['civil_twilight']['m']) $dag=true; else $dag=false;
$zon=$d['zon']['s'];
$heating=$d['heating']['s'];
if ($d['auto']['s']=='On') {
	if (TIME>=strtotime('5:00')&&TIME<strtotime('10:00')) {
		$dow=date("w");
		if($dow==0||$dow==6) {
			if ($d['RkamerL']['s']>0&&TIME>=strtotime('7:00')) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
			if ($d['RkamerR']['s']>0&&TIME>=strtotime('7:00')) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Rtobi']['s']>0&&TIME>=strtotime('9:00')) sl('Rtobi', 0, basename(__FILE__).':'.__LINE__);
			//if ($d['Ralex']['s']>0&&TIME>=strtotime('9:00')) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
		} else {
			if ($d['RkamerL']['s']>0&&TIME>=strtotime('6:00')) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
			if ($d['RkamerR']['s']>0&&TIME>=strtotime('6:00')) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Rtobi']['s']>0&&TIME>=strtotime('8:00')) sl('Rtobi', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Ralex']['s']>0&&TIME>=strtotime('9:00')) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
		}
		if ($dag==true) {
			if ($d['Weg']['s']!=1&&$d['Rtobi']['s']>0&&($d['deurtobi']['s']=='Open'||$d['tobi']['s']>0)) sl('Rtobi', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Weg']['s']!=1&&$d['Ralex']['s']>0&&($d['deuralex']['s']=='Open'||$d['alex']['s']>0)) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
		}
		if ($dag==true&&$zon==0&&$d['Weg']['s']!=1&&$d['tv']['s']=='Off') {
			foreach ($beneden as $i) {
				if ($d[$i]['s']>0) sl($i, 0, basename(__FILE__).':'.__LINE__);
			}
			if ($d['Rliving']['s']>0&&$d['tv']['s']=='Off') sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		} elseif ($dag==true&&$zon>0&&$d['Weg']['s']!=1&&$d['tv']['s']=='Off') {
			foreach ($beneden as $i) {
				if ($d[$i]['s']>0) sl($i, 0, basename(__FILE__).':'.__LINE__);
			}
			if ($d['Rliving']['s']>0) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		}
	} 

	elseif (TIME>=strtotime('10:00')&&TIME<strtotime('15:00')) {
		if($zon>2000) {
			if ($d['raamtobi']['s']=='Closed'&&$d['Rtobi']['s']!=82) sl('Rtobi', 82, basename(__FILE__).':'.__LINE__);
			if ($d['raamalex']['s']=='Closed'&&$d['Ralex']['s']!=82) sl('Ralex', 82, basename(__FILE__).':'.__LINE__);
		}
	} 

	elseif (TIME>=strtotime('15:00')&&TIME<strtotime('22:00')) {
		if($zon>2000) {
			if ($d['raamtobi']['s']=='Closed'&&$d['Ralex']['s']!=82) sl('Rtobi', 82, basename(__FILE__).':'.__LINE__);
			if ($d['raamalex']['s']=='Closed'&&$d['Ralex']['s']!=82) sl('Ralex', 82, basename(__FILE__).':'.__LINE__);
			if ($d['Rbureel']['s']<50) sl('Rbureel', 50, basename(__FILE__).':'.__LINE__);
		}
	}

	elseif (TIME>=strtotime('22:00')||TIME<strtotime('5:00')) {
		if ($d['Weg']['s']>0) {
			foreach ($beneden as $i) {
				if ($d[$i]['s']<88) sl($i, 88, basename(__FILE__).':'.__LINE__);
			}
			if ($d['Rliving']['s']<86) sl('Rliving', 86, basename(__FILE__).':'.__LINE__);
			foreach ($boven as $i) {
				if ($i=='Rtobi') {
					if ($d['deurtobi']['s']=='Closed'&&$d[$i]['s']<82) sl($i, 82, basename(__FILE__).':'.__LINE__);
				} elseif ($i=='RkamerL') {
					if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
				} elseif ($i=='RkamerR') {
					if ($d['Weg']['s']==2&&$d[$i]['s']<82) sl($i, 82, basename(__FILE__).':'.__LINE__);
				} else {
					if ($d[$i]['s']<82) sl($i, 82, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
}