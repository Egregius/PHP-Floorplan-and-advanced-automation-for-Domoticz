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
        if (TIME<strtotime('4:00')) $Setkamer=20;
        elseif (TIME>strtotime('10:00')) $Setkamer=20;
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
        if (TIME<strtotime('4:00')) $Setalex=20;
        elseif (TIME>strtotime('10:00')) $Setalex=20;
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
    if ($d['living_set']['s']!=$Setliving&&past('raamliving')>60&&past('deurinkom')>60&&past('deurgarage')>60) {
        store('living_set', $Setliving, basename(__FILE__).':'.__LINE__);
        $d['living_set']['s']=$Setliving;
    }
}
$bigdif=100;
foreach (array('living', 'kamer', 'alex') as $k) {
	${'dif'.$k}=number_format($d[$k.'_temp']['s']-$d[$k.'_set']['s'], 1);
    if (${'dif'.$k}<$bigdif) $bigdif=${'dif'.$k};
    $daikin=json_decode($d['daikin'.$k]['s']);
	if (${'dif'.$k}>=1.2) {$rate=7;$d[$k.'_set']['s']=$d[$k.'_set']['s']-1.5;}
	elseif (${'dif'.$k}>=0.9) {$rate=6;$d[$k.'_set']['s']=$d[$k.'_set']['s']-1;}
	elseif (${'dif'.$k}>=0.6) {$rate=5;$d[$k.'_set']['s']=$d[$k.'_set']['s']-1;}
	elseif (${'dif'.$k}>=0.3) {$rate=4;$d[$k.'_set']['s']=$d[$k.'_set']['s']-0.5;}
	elseif (${'dif'.$k}>=0) {$rate=3;$d[$k.'_set']['s']=$d[$k.'_set']['s'];}
	elseif (${'dif'.$k}>=-0.1) {$rate='B';$d[$k.'_set']['s']=$d[$k.'_set']['s'];}
	elseif (${'dif'.$k}>=-0.2) {$rate='B';$d[$k.'_set']['s']=$d[$k.'_set']['s']+0.5;}
	elseif (${'dif'.$k}>=-0.3) {$rate='B';$d[$k.'_set']['s']=$d[$k.'_set']['s']+1;}
	else {$rate='B';$d[$k.'_set']['s']=32;}
	if ($k=='kamer'||$k=='alex') {
		if (TIME>=strtotime('8:00')&&TIME<strtotime('18:00')) {
			if ($d[$k.'_set']['s']<$d[$k.'_temp']['s']) $d[$k.'_set']['s']=floor(($d[$k.'_temp']['s']-0.1)*2)/2;
			if ($rate!='B'&&$rate>4) $rate=4;
		}
	}
	if ($d[$k.'_set']['s']<18) $d[$k.'_set']['s']=18;
	if ($k=='kamer'&&$d['Weg']['s']==1) $rate='B';
	if ($k=='alex'&&(TIME>strtotime('20:00')||TIME<strtotime('08:00'))) $rate='B';
	if ($d[$k.'_set']['s']<25) {	
			if ($daikin->stemp!=$d[$k.'_set']['s']||$daikin->pow!=1||$daikin->mode!=3||$daikin->f_rate!=$rate) {
				daikinset($k, 1, 3, $d[$k.'_set']['s'], basename(__FILE__).':'.__LINE__, $rate);
				storemode('daikin'.$k, 3);
				storeicon($k.'_set', $d[$k.'_set']['s'].'-'.$rate);
			}
	} else {
		if ($daikin->pow!=0||$daikin->mode!=3) {
			daikinset($k, 0, 3, $d[$k.'_set']['s'], basename(__FILE__).':'.__LINE__);
			storemode('daikin'.$k, 0);
			storeicon($k.'_set', 'Off');
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
	if (TIME>=strtotime('6:00')&&TIME<strtotime('10:00')) {
		$dow=date("w");
		if($dow==0||$dow==6) {
			if ($d['RkamerL']['s']>0&&TIME>=strtotime('7:00')) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
			if ($d['RkamerR']['s']>0&&TIME>=strtotime('7:00')) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Rtobi']['s']>0&&TIME>=strtotime('9:00')) sl('Rtobi', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Ralex']['s']>0&&TIME>=strtotime('9:00')) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
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
		if ($dag==true&&$zon==0&&$d['Weg']['s']!=1) {
			foreach ($beneden as $i) {
				if ($d[$i]['s']>0) sl($i, 0, basename(__FILE__).':'.__LINE__);
			}
			if ($d['Rliving']['s']>0) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		} elseif ($dag==true&&$zon>0&&$d['Weg']['s']!=1) {
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

	elseif (TIME>=strtotime('22:00')||TIME<strtotime('6:00')) {
		if ($d['Weg']['s']>0) {
			foreach ($benedenall as $i) {
				if ($d[$i]['s']<88) sl($i, 88, basename(__FILE__).':'.__LINE__);
			}
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