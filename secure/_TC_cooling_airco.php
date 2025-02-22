<?php
$user=basename(__FILE__);
if ($d['daikin']['m']==1) {
	if ($d['brander']['s']!='Off') sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
	$bigdif=-100;
	foreach (array('living','kamer','alex') as $kamer) {
		if ($d[$kamer.'_set']['s']!='D') {
			${'dif'.$kamer}=number_format($d[$kamer.'_temp']['s']-$d[$kamer.'_set']['s'],1);
			if (${'dif'.$kamer}>$bigdif) $bigdif=${'dif'.$kamer};
		}
	}
	if ($bigdif>=2) $maxpow=100;
	elseif ($bigdif>=0.5) $maxpow=60;
	else $maxpow=40;
	$maxpow=floor($maxpow/5)*5;
	if ($d['daikin_kWh']['m']!='Auto') $maxpow=$d['daikin_kWh']['m'];
	elseif ($d['Weg']['s']>0) $maxpow=40;
	if ($maxpow<=40) {$maxpow=40;$spmode=-1;}
	elseif ($maxpow>=100) {$maxpow=100;$spmode=0;}
	else $spmode=-1;
	// KAMER
	unset($power);
	$Setkamer=33;
	if ($d['kamer_set']['m']==0) {
		if (
				($d['raamkamer']['s']=='Closed'||$d['RkamerR']['s']>=85)
			&&
				(past('raamkamer')>300||$time>strtotime('19:00'))
			&&
				(
					($d['deurkamer']['s']=='Closed'||($d['deurkamer']['s']=='Open'&&past('deurkamer')<300))
				||
					(
						($d['deuralex']['s']=='Closed'||($d['deuralex']['s']=='Open'&&past('deuralex')<300)||$d['raamalex']['s']=='Closed'||$d['Ralex']['s']>=85)
					&&
						($d['deurwaskamer']['s']=='Closed'||($d['deurwaskamer']['s']=='Open'&&past('deurwaskamer')<300)||$d['raamwaskamer']['s']=='Closed'||$d['Rwaskamer']['s']>=85)
					&& $d['raamhall']['s']=='Closed'
					)
				)
		) {
			$base=22;
			if ($time<strtotime('7:00')) $Setkamer=$base;
			elseif ($time>strtotime('18:30')) $Setkamer=$base;
			elseif ($time>strtotime('17:30')) $Setkamer=$base+0.5;
			elseif ($time>strtotime('16:30')) $Setkamer=$base+1;
			elseif ($time>strtotime('15:30')) $Setkamer=$base+1.5;
			elseif ($time>strtotime('14:30')) $Setkamer=$base+2;
			elseif ($time>strtotime('13:30')) $Setkamer=$base+2.5;
			elseif ($time>strtotime('12:30')) $Setkamer=$base+3;
			elseif ($time>strtotime('11:30')) $Setkamer=$base+3.5;
			elseif ($time>strtotime('10:30')) $Setkamer=$base+4;
			elseif ($time>strtotime('9:30')) $Setkamer=$base+4.5;
			elseif ($time>strtotime('8:30')) $Setkamer=$base+5;
			elseif ($time>strtotime('7:30')) $Setkamer=$base+5.5;
			if ($d['kamer_set']['s']=='D') {
				$power=1;
			} else {
				$dif=$d['kamer_temp']['s']-$d['kamer_set']['s'];
				if ($dif>=0) $power=1;
				elseif ($dif<-1.5) $power=0;
			}
		} else $power=0;
//		$Setkamer=21.5;
		if ($d['Weg']['s']>=3) $Setkamer=28;
		if ($d['kamer_set']['s']!=$Setkamer) {
			setpoint('kamer_set', $Setkamer, basename(__FILE__).':'.__LINE__);
			$d['kamer_set']['s']=$Setkamer;
		}
	} elseif ($d['kamer_set']['m']==1&&$d['kamer_set']['s']=='D') {
		$spmode=-1;
		$power=1;
	}
	
	
//	if (isset($power)) lg('kamer dif='.$dif.' power='.$power); else lg('kamer dif='.$dif);
	if ($d['kamer_set']['s']<32||$d['kamer_set']['s']=='D') {
		if ($d['daikin']['s']=='On'&&past('daikin')>120) {
			$rate='A';
			if ($d['kamer_set']['s']==1) $rate=3;
			elseif($d['kamer_set']['s']==2) $rate=4;
			elseif($d['kamer_set']['s']==3) $rate=5;
			elseif($d['kamer_set']['s']==4) $rate=6;
			elseif($d['kamer_set']['s']==5) $rate=7;
			if($d['kamer_set']['s']=='D') {
				$mode=2;
				$set='M';
			} else {
				$mode=3;
					$set=$d['kamer_set']['s']-1;
				if ($time<strtotime('8:30')||$time>strtotime('22:00')&&$set>10)$rate='B';
				$set=$set-($dif*2);
				$set=ceil($set * 2) / 2;
				if ($set>30) $set=30;
				elseif ($set<18) $set=18;
			}
			$daikin=json_decode($d['daikinkamer']['s']);
//			lg(print_r($daikin, true));
			if (isset($daikin)) {
				if (!isset($power)) $power=$daikin->power;
				if ($daikin->adv == '') {
					$powermode=0;
				} else if (strstr($daikin->adv, '/')) {
					$advs=explode("/", $daikin->adv);
					if ($advs[0]==2) $powermode=2;
					else if ($advs[0]==12) $powermode=1;
					else $powermode=0;
				} else {
					if ($daikin->adv==13)  $powermode=0; //Normal
					else if ($daikin->adv==12)  $powermode=1; // Eco
					else if ($daikin->adv==2)  $powermode=2; // Power
					else if ($daikin->adv=='')  $powermode=0;
				}
				if (($daikin->set!=$set||$daikin->power!=$power||$daikin->mode!=$mode||$daikin->fan!=$rate)&&$powermode<2) {
					$data=json_decode($d['kamer_set']['icon'], true);
					$data['power']=$power;
					$data['mode']=$mode;
					$data['fan']=$rate;
					$data['set']=$set;
					storeicon('kamer_set', json_encode($data));
					daikinset('kamer', $power, $mode, $set, basename(__FILE__).':'.__LINE__, $rate, $powermode, $maxpow);
				}
				unset($daikin);
			}
		} elseif (isset($power)&&$power==1&&$d['daikin']['s']=='Off') {
			if (past('daikin')>900) sw('daikin', 'On', basename(__FILE__).':'.__LINE__);
		}
	} elseif(past('raamkamer')>300&&past('deurkamer')>300) {
		$daikin=json_decode($d['daikinkamer']['s']);
		if ($daikin->power!=0||$daikin->mode!=3) {
			$data=json_decode($d['kamer_set']['icon'], true);
			$data['power']=0;
			$data['mode']=3;
			$data['fan']='A';
			$data['set']=33;
			$data['spmode']=$daikin->spmode;
			$data['maxpow']=$daikin->maxpow;
			storeicon('kamer_set', json_encode($data));
			daikinset('kamer', 0, 3, 10, basename(__FILE__).':'.__LINE__, 'A', -1, $maxpow);
		}
	}
	unset($power);
	// ALEX

	$Setalex=33;
	if ($d['alex_set']['m']==0) {
		if (
				($d['raamalex']['s']=='Closed'||$d['Ralex']['s']>=80)
			&&
				(past('raamalex')>300|| $time>strtotime('19:00'))
			&&
				(
					($d['deuralex']['s']=='Closed'||($d['deuralex']['s']=='Open'&&past('deuralex')<300))
				||
					(
						($d['deurkamer']['s']=='Closed'||($d['deurkamer']['s']=='Open'&&past('deurkamer')<300)||$d['raamkamer']['s']=='Closed'||$d['RkamerR']['s']>=80)
					&&
						($d['deurwaskamer']['s']=='Closed'||($d['deurwaskamer']['s']=='Open'&&past('deurwaskamer')<300)||$d['raamwaskamer']['s']=='Closed'||$d['Rwaskamer']['s']>=80)
					&& $d['raamhall']['s']=='Closed'
					)
				)
		) {
			$base=23;
			if ($time<strtotime('7:00')) $Setalex=$base;
			elseif ($time>strtotime('18:30')) $Setalex=$base;
			elseif ($time>strtotime('17:30')) $Setalex=$base+0.5;
			elseif ($time>strtotime('16:30')) $Setalex=$base+1;
			elseif ($time>strtotime('15:30')) $Setalex=$base+1.5;
			elseif ($time>strtotime('14:30')) $Setalex=$base+2;
			elseif ($time>strtotime('13:30')) $Setalex=$base+2.5;
			elseif ($time>strtotime('12:30')) $Setalex=$base+3;
			elseif ($time>strtotime('11:30')) $Setalex=$base+3.5;
			elseif ($time>strtotime('10:30')) $Setalex=$base+4;
			elseif ($time>strtotime('9:30')) $Setalex=$base+4.5;
			elseif ($time>strtotime('8:30')) $Setalex=$base+5;
			elseif ($time>strtotime('7:30')) $Setalex=$base+5.5;
			if ($d['alex_set']['s']=='D') {
				$power=1;
			} else {
				$dif=$d['alex_temp']['s']-$d['alex_set']['s'];
				if ($dif>=0) $power=1;
				elseif ($dif<-1.5) $power=0;
			}
		} else $power=0;
		if ($d['Weg']['s']>=3) $Setalex=28;
		
		if ($d['alex_set']['s']!=$Setalex) {
			setpoint('alex_set', $Setalex, basename(__FILE__).':'.__LINE__);
			$d['alex_set']['s']=$Setalex;
		}
	} elseif ($d['alex_set']['m']==1&&$d['alex_set']['s']=='D') {
		$power=1;
		$spmode=-1;
	}
	
	if ($d['alex_set']['s']<32||$d['alex_set']['s']=='D') {
		if ($d['daikin']['s']=='On'&&past('daikin')>120) {
			$rate='A';
			if ($d['alex_set']['s']==1) $rate=3;
			elseif($d['alex_set']['s']==2) $rate=4;
			elseif($d['alex_set']['s']==3) $rate=5;
			elseif($d['alex_set']['s']==4) $rate=6;
			elseif($d['alex_set']['s']==5) $rate=7;
			if($d['alex_set']['s']=='D') {
				$mode=2;
				$set='M';
			} else {
				$mode=3;
					$set=$d['alex_set']['s']-1;
				if ($time<strtotime('8:30')||$time>strtotime('19:30')&&$set>10)$rate='B';
				$set=$set-($dif*2);
				$set=ceil($set * 2) / 2;
				if ($set>30) $set=30;
				elseif ($set<18) $set=18;
			}
			$daikin=json_decode($d['daikinalex']['s']);
			if (isset($daikin)) {
				if (!isset($power)) $power=$daikin->power;
				if ($daikin->adv == '') {
					$powermode=0;
				} else if (strstr($daikin->adv, '/')) {
					$advs=explode("/", $daikin->adv);
					if ($advs[0]==2) $powermode=2;
					else if ($advs[0]==12) $powermode=1;
					else $powermode=0;
				} else {
					if ($daikin->adv==13)  $powermode=0; //Normal
					else if ($daikin->adv==12)  $powermode=1; // Eco
					else if ($daikin->adv==2)  $powermode=2; // Power
					else if ($daikin->adv=='')  $powermode=0;
				}
				if (($daikin->set!=$set||$daikin->power!=$power||$daikin->mode!=$mode||$daikin->fan!=$rate)&&$powermode<2) {
					$data=json_decode($d['alex_set']['icon'], true);
					$data['power']=$power;
					$data['mode']=$mode;
					$data['fan']=$rate;
					$data['set']=$set;
					storeicon('alex_set', json_encode($data));
					daikinset('alex', $power, $mode, $set, basename(__FILE__).':'.__LINE__, $rate, $maxpow);
				}
				unset($daikin);
			}
		} elseif (isset($power)&&$power==1&&$d['daikin']['s']=='Off') {
			if (past('daikin')>900) sw('daikin', 'On', basename(__FILE__).':'.__LINE__);
		}
	} elseif(past('raamalex')>300&&past('deuralex')>300) {
		$daikin=json_decode($d['daikinalex']['s']);
		if ($daikin->power!=0||$daikin->mode!=3) {
			$data=json_decode($d['alex_set']['icon'], true);
			$data['power']=0;
			$data['mode']=3;
			$data['fan']='A';
			$data['set']=33;
			$data['spmode']=$daikin->spmode;
			$data['maxpow']=$daikin->maxpow;
			storeicon('alex_set', json_encode($data));
			daikinset('alex', 0, 3, 10, basename(__FILE__).':'.__LINE__, 'A', -1, $maxpow);
		}
	}
	unset($power);
	// LIVING
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
			if ($d['living_set']['s']=='D') {
				$power=1;
			} else {
				$dif=$d['living_temp']['s']-$d['living_set']['s'];
				if ($dif>=0) $power=1;
				elseif ($dif<-1.5) $power=0;
			}
		} else $power=0;
//		$Setliving=22;
		if ($d['living_set']['s']!=$Setliving&&past('raamliving')>60&&($d['deurinkom']['s']=='Closed'||past('deurinkom')>60)&&($d['deurgarage']['s']=='Closed'||past('deurgarage')>60)) {
			setpoint('living_set', $Setliving, basename(__FILE__).':'.__LINE__);
			$d['living_set']['s']=$Setliving;
		}
	} elseif ($d['living_set']['m']==1&&$d['living_set']['s']=='D') {
		$power=1;
	}
	
//	if (isset($power)) lg('living dif='.$dif.' power='.$power); else lg('living dif='.$dif);
	if ($d['living_set']['s']<32||$d['living_set']['s']=='D') {
		if ($d['daikin']['s']=='On'&&past('daikin')>120) {
			$rate='A';
			if ($d['living_set']['s']==1) $rate=3;
			elseif($d['living_set']['s']==2) $rate=4;
			elseif($d['living_set']['s']==3) $rate=5;
			elseif($d['living_set']['s']==4) $rate=6;
			elseif($d['living_set']['s']==5) $rate=7;
			if ($d['eettafel']['s']>0) $rate='B';
			elseif ($d['eettafel']['s']>0) $rate='B';
		if($d['living_set']['s']=='D') {
				$mode=2;
				$set='M';
				$spmode=-1;
			} else {
				$mode=3;
				$set=$d['living_set']['s']-0;
				$set=$set-($dif*2);
				$set=ceil($set * 2) / 2;
				if ($set>30) $set=30;
				elseif ($set<18) $set=18;
			}
			$daikin=json_decode($d['daikinliving']['s']);
			if(isset($daikin)) {
				if (!isset($power)) $power=$daikin->power;
				if ($daikin->adv == '') {
					$powermode=0;
				} else if (strstr($daikin->adv, '/')) {
					$advs=explode("/", $daikin->adv);
					if ($advs[0]==2) $powermode=2;
					else if ($advs[0]==12) $powermode=1;
					else $powermode=0;
				} else {
					if ($daikin->adv==13)  $powermode=0; //Normal
					else if ($daikin->adv==12)  $powermode=1; // Eco
					else if ($daikin->adv==2)  $powermode=2; // Power
					else if ($daikin->adv=='')  $powermode=0;
				}
				if (($daikin->set!=$set||$daikin->power!=$power||$daikin->mode!=$mode||$daikin->fan!=$rate)&&$powermode<2) {
					$data=json_decode($d['living_set']['icon'], true);
					$data['power']=$power;
					$data['mode']=$mode;
					$data['fan']=$rate;
					$data['set']=$set;
					storeicon('living_set', json_encode($data));
					daikinset('living', $power, $mode, $set, basename(__FILE__).':'.__LINE__, $rate, $spmode, $maxpow);
				}
				unset($daikin);
			}
		} elseif (isset($power)&&$power==1&&$d['daikin']['s']=='Off') {
			if (past('daikin')>900) sw('daikin', 'On', basename(__FILE__).':'.__LINE__);
		}
	} elseif (past('raamliving')>300&&past('raamkeuken')>300&&past('deurgarage')>300) {
		$daikin=json_decode($d['daikinliving']['s']);
		if ($daikin->power!=0||$daikin->mode!=3) {
			$data=json_decode($d['living_set']['icon'], true);
			$data['power']=0;
			$data['mode']=3;
			$data['fan']='A';
			$data['set']=33;
			$data['spmode']=$daikin->spmode;
			$data['maxpow']=$daikin->maxpow;
			storeicon('living_set', json_encode($data));
			daikinset('living', 0, 3, 10, basename(__FILE__).':'.__LINE__, 'A', $spmode, $maxpow);
		}
	}
}
$boven=array('Rwaskamer','Ralex','RkamerL','RkamerR');
$beneden=array('Rbureel','RkeukenL','RkeukenR');

if ($d['auto']['s']=='On') {
	if (($time>=$t||($d['Weg']['s']==0&&$d['dag']>0))&&$time<strtotime('8:30')) {
		if ($time>=$t) {
			if ($d['RkamerL']['s']>0) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
			if ($d['RkamerR']['s']>0) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Ralex']['s']==0&&$d['Rwaskamer']['s']>50) sl('Rwaskamer', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Ralex']['s']>0&&$time>=$t+1800&&($d['deuralex']['s']=='Open'||$d['alex']['s']>0)) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
		}
		if ($d['Media']['s']=='Off'&&$d['Rliving']['s']>0&&($d['Ralex']['s']<=1||$time>=strtotime('8:30'))) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		if ($d['dag']>0) {
			if ($d['Media']['s']=='Off') {
				foreach ($beneden as $i) {
					if ($d[$i]['s']>0&&past($i)>7200) sl($i, 0, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}

	elseif ($time>=strtotime('11:00')&&$time<strtotime('15:00')) {
		if($d['zon']['s']>2000) {
			if ($d['raamwaskamer']['s']=='Closed'&&$d['Rwaskamer']['s']<83&&past('Rwaskamer')>3600) sl('Rwaskamer', 83, basename(__FILE__).':'.__LINE__);
			if ($d['raamalex']['s']=='Closed'&&$d['Ralex']['s']<83&&past('Ralex')>3600) sl('Ralex', 83, basename(__FILE__).':'.__LINE__);
			if ($d['Weg']['s']>1&&$d['luifel']['s']==0&&$d['Rliving']['s']<86&&$d['living_temp']['s']>21) sl('Rliving', 86, basename(__FILE__).':'.__LINE__);
		}
	}

	elseif ($time>=strtotime('15:00')&&$time<strtotime('22:00')) {
		if($d['zon']['s']>1500) {
			if ($d['raamwaskamer']['s']=='Closed'&&$d['Rwaskamer']['s']<50) sl('Rwaskamer', 83, basename(__FILE__).':'.__LINE__);
			if ($d['raamalex']['s']=='Closed'&&$d['Ralex']['s']<50) sl('Ralex', 83, basename(__FILE__).':'.__LINE__);
			if ($d['Rbureel']['s']<30) sl('Rbureel', 30, basename(__FILE__).':'.__LINE__);
			if ($d['Weg']['s']>1&&$d['Rliving']['s']<86&&$d['living_temp']['s']>21&&$d['luifel']['s']<30&&past('Weg')>3600) sl('Rliving', 86, basename(__FILE__).':'.__LINE__);
		}
	}

	elseif ($time>=strtotime('22:00')||$time<strtotime('3:00')) {
		if ($d['Weg']['s']>0) {
			foreach ($beneden as $i) {
				if ($d[$i]['s']<70) sl($i, 90, basename(__FILE__).':'.__LINE__);
			}
			if ($d['Rliving']['s']<90) sl('Rliving', 90, basename(__FILE__).':'.__LINE__);
			foreach ($boven as $i) {
				if ($i=='Rwaskamer') {
					if ($d['deurwaskamer']['s']=='Closed'&&$d[$i]['s']<50) sl($i, 83, basename(__FILE__).':'.__LINE__);
				} elseif ($i=='RkamerL') {
					if ($d['Weg']['s']>=2&&$d[$i]['s']<50) sl($i, 83, basename(__FILE__).':'.__LINE__);
				} elseif ($i=='RkamerR') {
					if ($d['Weg']['s']>=2&&$d[$i]['s']<50) sl($i, 83, basename(__FILE__).':'.__LINE__);
				} else {
					if ($d[$i]['s']<50) sl($i, 83, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
}
require('_TC_badkamer.php');
