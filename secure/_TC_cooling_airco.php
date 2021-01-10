<?php
/**
 * Pass2PHP Temperature Control Airco cooling
 * php version 7.3
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
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
		if (TIME<strtotime('6:30')) $Setkamer=20.5;
		elseif (TIME>strtotime('21:00')) $Setkamer=20.5;
		elseif (TIME>strtotime('20:00')) $Setkamer=21;
		elseif (TIME>strtotime('19:00')) $Setkamer=21.5;
		elseif (TIME>strtotime('18:00')) $Setkamer=22;
		elseif (TIME>strtotime('17:00')) $Setkamer=22.5;
		elseif (TIME>strtotime('16:00')) $Setkamer=23;
		elseif (TIME>strtotime('15:00')) $Setkamer=23.5;
		elseif (TIME>strtotime('14:00')) $Setkamer=24;
		elseif (TIME>strtotime('13:00')) $Setkamer=24.5;
		elseif (TIME>strtotime('12:00')) $Setkamer=25;
		elseif (TIME>strtotime('11:00')) $Setkamer=25.5;
		elseif (TIME>strtotime('10:00')) $Setkamer=26;
		elseif (TIME>strtotime('9:00')) $Setkamer=26.5;
		elseif (TIME>strtotime('8:00')) $Setkamer=27;
	}
	if ($d['Weg']['s']>=3) $Setkamer=28;
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
		if (TIME<strtotime('6:30')) $Setalex=21.5;
		elseif (TIME>strtotime('19:00')) $Setalex=21.5;
		elseif (TIME>strtotime('18:00')) $Setalex=22;
		elseif (TIME>strtotime('17:00')) $Setalex=22.5;
		elseif (TIME>strtotime('16:00')) $Setalex=23;
		elseif (TIME>strtotime('15:00')) $Setalex=23.5;
		elseif (TIME>strtotime('14:00')) $Setalex=24;
		elseif (TIME>strtotime('13:00')) $Setalex=24.5;
		elseif (TIME>strtotime('12:00')) $Setalex=25;
		elseif (TIME>strtotime('11:00')) $Setalex=25.5;
		elseif (TIME>strtotime('10:00')) $Setalex=26;
		elseif (TIME>strtotime('9:00')) $Setalex=26.5;
		elseif (TIME>strtotime('8:00')) $Setalex=27;
	}
	if ($d['Weg']['s']>=3) $Setalex=28;
	if ($d['alex_set']['s']!=$Setalex) {
		store('alex_set', $Setalex, basename(__FILE__).':'.__LINE__);
		$d['alex_set']['s']=$Setalex;
	}
}

$Setliving=33;
if ($d['living_set']['m']==0) {
	if (
		($d['raamliving']['s']=='Closed'||($d['raamliving']['s']=='Open'&&past('raamliving')<300))
		&&($d['raamkeuken']['s']=='Closed'||($d['raamkeuken']['s']=='Open'&&past('raamkeuken')<300))
		&&($d['deurinkom']['s']=='Closed'||($d['deurvoordeur']['s']=='Closed'&&$d['deurinkom']['s']=='Open'))
		&&($d['deurgarage']['s']=='Closed'||($d['deurgarage']['s']=='Open'&&past('deurgarage')<300))
		&& $d['Weg']['s']<=2
	) {
		if ($d['Weg']['s']>=3) $Setliving=28;
		elseif ($d['Weg']['s']>0) $Setliving=25;
		else $Setliving=24;
	}
	if ($d['living_set']['s']!=$Setliving&&past('raamliving')>60&&($d['deurinkom']['s']=='Closed'||past('deurinkom')>60)&&($d['deurgarage']['s']=='Closed'||past('deurgarage')>60)) {
		store('living_set', $Setliving, basename(__FILE__).':'.__LINE__);
		$d['living_set']['s']=$Setliving;
	}
}
foreach (array('living', 'kamer', 'alex') as $k) {
	$dif=$d[$k.'_temp']['s']-$d[$k.'_set']['s'];
	if ($dif>=0.8) $power=1;
	elseif ($dif<-0.5) $power=0;
	if ($d[$k.'_set']['s']<32) {
		if (isset($power)&&$d['daikin']['s']=='On'&&past('daikin')>120) {
			$rate='A';
			if ($k=='living') {
				$set=$d[$k.'_set']['s']-1.5;
			} elseif ($k=='kamer') {
				$set=$d[$k.'_set']['s']-3;
				if (TIME<strtotime('8:30')||TIME>strtotime('22:30'))$rate='B';
			} elseif ($k=='alex') {
				$set=$d[$k.'_set']['s']-3;
				if (TIME<strtotime('8:30')||TIME>strtotime('19:30'))$rate='B';
			}
			$set=ceil($set * 2) / 2;
			if ($set>25) $set=25;
			elseif ($set<10) $set=10;
			$daikin=json_decode($d['daikin'.$k]['s']);
			if ($daikin->set!=$set||$daikin->power!=$power||$daikin->mode!=3||$daikin->fan!=$rate) {
				$data=json_decode($d[$k.'_set']['icon'], true);
				$data['power']=$power;
				$data['mode']=3;
				$data['fan']=$rate;
				$data['set']=$set;
				storeicon($k.'_set', json_encode($data));
				daikinset($k, $power, 3, $set, basename(__FILE__).':'.__LINE__, $rate);
				storemode('daikin'.$k, 3);
			}
		} elseif (isset($power)&&$power==1&&$d['daikin']['s']=='Off') {
			if (past('daikin')>900) sw('daikin', 'On', basename(__FILE__).':'.__LINE__);
		}
	} else {
		$daikin=json_decode($d['daikin'.$k]['s']);
		if ($daikin->power!=0||$daikin->mode!=3) {
			$data=json_decode($d[$k.'_set']['icon'], true);
			$data['power']=$power;
			$data['mode']=3;
			$data['fan']=$rate;
			$data['set']=$set;
			storeicon($k.'_set', json_encode($data));
			daikinset($k, 0, 3, 10, basename(__FILE__).':'.__LINE__);
			storemode('daikin'.$k, 0);
		}
	}
}

$boven=array('Rtobi','Ralex','RkamerL','RkamerR');
$beneden=array('Rbureel','RkeukenL','RkeukenR');
$benedenall=array('Rliving','Rbureel','RkeukenL','RkeukenR');
if (TIME>=$d['civil_twilight']['s']&&TIME<=$d['civil_twilight']['m']) $dag=true; else $dag=false;
$zon=$d['zon']['s'];
$heating=$d['heating']['s'];
if ($d['auto']['s']=='On'&&$d['Weg']['s']<3) {
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
		if ($dag==true||$d['pirhall']['s']=='On') {
			if (($d['Weg']['s']!=1||$d['pirhall']['s']=='On')&&$d['Rtobi']['s']>0&&($d['deurtobi']['s']=='Open'||$d['tobi']['s']>0)) sl('Rtobi', 0, basename(__FILE__).':'.__LINE__);
			if (($d['Weg']['s']!=1||$d['pirhall']['s']=='On')&&$d['Ralex']['s']>0&&($d['deuralex']['s']=='Open'||$d['alex']['s']>0)) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
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
		if($zon>1500) {
			if ($d['raamtobi']['s']=='Closed'&&$d['Rtobi']['s']!=82) sl('Rtobi', 82, basename(__FILE__).':'.__LINE__);
			if ($d['raamalex']['s']=='Closed'&&$d['Ralex']['s']!=82) sl('Ralex', 82, basename(__FILE__).':'.__LINE__);
		}
	}

	elseif (TIME>=strtotime('15:00')&&TIME<strtotime('22:00')) {
		if($zon>1500) {
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
					if ($d['Weg']['s']>=2&&$d[$i]['s']<82) sl($i, 82, basename(__FILE__).':'.__LINE__);
				} else {
					if ($d[$i]['s']<82) sl($i, 82, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
} elseif ($d['auto']['s']=='On'&&$d['Weg']['s']==3) {
	include('_Rolluiken_Vakantie.php');
}
