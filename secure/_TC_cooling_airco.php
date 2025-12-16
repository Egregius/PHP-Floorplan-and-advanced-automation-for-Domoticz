<?php
$user=basename(__FILE__);
if ($d['daikin']['m']==1) {
	if ($d['brander']['s']!='Off') sw('brander', 'Off', $user.':'.__LINE__);
	$bigdif=-100;
	$daikinDefaults = ['power'=>99,'mode'=>99,'set'=>99,'fan'=>99,'spmode'=>99];
	$daikin ??= new stdClass();
	foreach (array('living','kamer','alex') as $kamer) {
		$daikin->$k ??= (object)$daikinDefaults;
		if ($d[$kamer.'_set']['s']!='D') {
			${'dif'.$kamer}=number_format($d[$kamer.'_temp']['s']-$d[$kamer.'_set']['s'],1);
			if (${'dif'.$kamer}>$bigdif) $bigdif=${'dif'.$kamer};
		}
	}
	if ($bigdif>=2) $maxpow=100;
	elseif ($bigdif>=0.5) $maxpow=60;
	else $maxpow=40;
	$maxpow=floor($maxpow/5)*5;
	if ($d['weg']['s']>0) $maxpow=40;
	if ($maxpow<=40) {$maxpow=40;$spmode=-1;}
	elseif ($maxpow>=100) {$maxpow=100;$spmode=0;}
	else $spmode=-1;
	// KAMER
	$k='kamer';
	$Setkamer=33;
	unset($power);
	if ($d['kamer_set']['m']==0&&$d['kamer_set']['s']!='D') {
		if (
				($d['raamkamer']['s']=='Closed'||$d['rkamerr']['s']>=85)
			&&
				(past('raamkamer')>300||$time>strtotime('19:00'))
			&&
				(
					($d['deurkamer']['s']=='Closed'||($d['deurkamer']['s']=='Open'&&past('deurkamer')<300))
				||
					(
						($d['deuralex']['s']=='Closed'||($d['deuralex']['s']=='Open'&&past('deuralex')<300)||$d['raamalex']['s']=='Closed'||$d['ralex']['s']>=85)
					&&
						($d['deurwaskamer']['s']=='Closed'||($d['deurwaskamer']['s']=='Open'&&past('deurwaskamer')<300)||$d['raamwaskamer']['s']=='Closed'||$d['rwaskamer']['s']>=85)
					&& $d['raamhall']['s']=='Closed'
					)
				)
		) {
			$base=21.5;
			if ($time<strtotime('7:00')) $Setkamer=$base;
			elseif ($time>strtotime('21:30')) $Setkamer=$base;
			elseif ($time>strtotime('20:30')) $Setkamer=$base+0.5;
			elseif ($time>strtotime('19:30')) $Setkamer=$base+1;
			elseif ($time>strtotime('18:30')) $Setkamer=$base+1.5;
			elseif ($time>strtotime('17:30')) $Setkamer=$base+2;
			elseif ($time>strtotime('16:30')) $Setkamer=$base+2.5;
			elseif ($time>strtotime('15:30')) $Setkamer=$base+3;
			elseif ($time>strtotime('14:30')) $Setkamer=$base+3.5;
			elseif ($time>strtotime('13:30')) $Setkamer=$base+4;
			elseif ($time>strtotime('12:30')) $Setkamer=$base+4.5;
			elseif ($time>strtotime('11:30')) $Setkamer=$base+5;
			elseif ($time>strtotime('10:30')) $Setkamer=$base+5.5;
			if ($d['kamer_set']['s']=='D') {
				$power=1;
			} else {
				if ($difkamer>=0) $power=1;
				elseif ($difkamer<-1.5) $power=0;
			}
			if ($d['kamer_temp']['s']>=20&&$d['kamer_temp']['m']>=55&&$d['net']<-1000) {
				$power=1;
				store('kamer_set', 'D', $user.':'.__LINE__, ' Drogen activeren');
			}
		} else {
			$power=0;
		}
//		$Setkamer=21.5;
		if ($d['weg']['s']>=3) $Setkamer=28;
		if ($d['kamer_set']['s']!=$Setkamer) {
			setpoint('kamer_set', $Setkamer, $user.':'.__LINE__);
			$d['kamer_set']['s']=$Setkamer;
		}
	} elseif ($d['kamer_set']['m']==0&&$d['kamer_set']['s']=='D') {
		if (($d['kamer_temp']['s']<17||$d['kamer_temp']['m']<50||$d['net']>0||($d['raamkamer']['s']=='Open'&&past('raamkamer')>60))&&$d['kamer_set']['s']=='D') {
			$power=0;
			store('kamer_set', 33, $user.':'.__LINE__, ' Drogen uitschakelen');
		} else $power=1;
	} elseif ($d['kamer_set']['m']==1||$d['kamer_set']['s']=='D') {
		$spmode=-1;
		$power=1;
	}
	
	
//	if (isset($power)) lg('kamer dif='.$difkamer.' power='.$power); else lg('kamer dif='.$difkamer);
	if ($d['kamer_set']['s']<32||$d['kamer_set']['s']=='D') {
		if ($d['daikin']['s']=='On') {
			$fan='A';
			if ($d['kamer_set']['s']==1) $fan=3;
			elseif($d['kamer_set']['s']==2) $fan=4;
			elseif($d['kamer_set']['s']==3) $fan=5;
			elseif($d['kamer_set']['s']==4) $fan=6;
			elseif($d['kamer_set']['s']==5) {$fan=7;$d['kamer_set']['s']=18;}
			if($d['kamer_set']['s']=='D') {
				$mode=2;
				$set='M';
			} else {
				$mode=3;
				$set=$d['kamer_set']['s']-1;
				if ($time<strtotime('8:30')||$time>strtotime('22:00')&&$set>10)$fan='B';
				$set=$set-($difkamer*2);
				$set=ceil($set * 2) / 2;
				if ($set>30) $set=30;
				elseif ($set<18) $set=18;
			}
			if (!isset($power)) $power=$daikin->$k->power;
			if ($daikin->$k->adv == '') {
				$powermode=0;
			} else if (strstr($daikin->$k->adv, '/')) {
				$advs=explode("/", $daikin->$k->adv);
				if ($advs[0]==2) $powermode=2;
				else if ($advs[0]==12) $powermode=1;
				else $powermode=0;
			} else {
				if ($daikin->$k->adv==13)  $powermode=0; //Normal
				else if ($daikin->$k->adv==12)  $powermode=1; // Eco
				else if ($daikin->$k->adv==2)  $powermode=2; // Power
				else if ($daikin->$k->adv=='')  $powermode=0;
			}
			if (($daikin->$k->set!=$set||$daikin->$k->power!=$power||$daikin->$k->mode!=$mode||$daikin->$k->fan!=$fan)&&$powermode<2) {
				if(daikinset($k, $power, 4, $set, basename(__FILE__).':'.__LINE__, $fan, $spmode, $maxpow)) {
					$daikin->$k->power=$power;
					$daikin->$k->mode=4;
					$daikin->$k->fan=$fan;
					$daikin->$k->set=$set;
					$daikin->$k->spmode=$spmode;
					lg(print_r($daikin,true));
				}
			}
		} elseif (isset($power)&&$power==1&&$d['daikin']['s']=='Off') {
			if (past('daikin')>900) {
				sw('daikin', 'On', $user.':'.__LINE__);
			}
		}
	} elseif(past('raamkamer')>300&&past('deurkamer')>300) {
		$power=0;
		$mode=3;
		if ($daikin->$k->power!=$power||$daikin->$k->mode!=$mode) {
			if(daikinset($k, 0, $power, $set, basename(__FILE__).':'.__LINE__, $fan, $spmode, $maxpow)) {
				$daikin->$k->power=$power;
				$daikin->$k->mode=4;
				$daikin->$k->fan=$fan;
				$daikin->$k->set=$set;
				$daikin->$k->spmode=$spmode;
			}
		}
	}
	unset($power);
	// ALEX
	$k='alex';
	$Setalex=33;
	if ($d['alex_set']['m']==0&&$d['alex_set']['s']!='D') {
		if (
				($d['raamalex']['s']=='Closed'||$d['ralex']['s']>=80)
			&&
				(past('raamalex')>300|| $time>strtotime('19:00'))
			&&
				(
					($d['deuralex']['s']=='Closed'||($d['deuralex']['s']=='Open'&&past('deuralex')<300))
				||
					(
						($d['deurkamer']['s']=='Closed'||($d['deurkamer']['s']=='Open'&&past('deurkamer')<300)||$d['raamkamer']['s']=='Closed'||$d['rkamerr']['s']>=80)
					&&
						($d['deurwaskamer']['s']=='Closed'||($d['deurwaskamer']['s']=='Open'&&past('deurwaskamer')<300)||$d['raamwaskamer']['s']=='Closed'||$d['rwaskamer']['s']>=80)
					&& $d['raamhall']['s']=='Closed'
					)
				)
		) {
			$base=23;
			if ($time<strtotime('7:00')) $Setalex=$base;
			elseif ($time>strtotime('18:15')) $Setalex=$base;
			elseif ($time>strtotime('17:15')) $Setalex=$base+0.5;
			elseif ($time>strtotime('16:15')) $Setalex=$base+1;
			elseif ($time>strtotime('15:15')) $Setalex=$base+1.5;
			elseif ($time>strtotime('14:15')) $Setalex=$base+2;
			elseif ($time>strtotime('13:15')) $Setalex=$base+2.5;
			elseif ($time>strtotime('12:15')) $Setalex=$base+3;
			elseif ($time>strtotime('11:15')) $Setalex=$base+3.5;
			elseif ($time>strtotime('10:15')) $Setalex=$base+4;
			if ($d['alex_set']['s']=='D') {
				$power=1;
			} else {
				if ($difalex>=0) $power=1;
				elseif ($difalex<-1.5) $power=0;
			}
			if ($d['alex_temp']['s']>=20&&$d['alex_temp']['m']>=55&&$d['net']<-1000) {
				$power=1;
				store('alex_set', 'D', $user.':'.__LINE__, ' Drogen activeren');
			}
		} else $power=0;
		if ($d['weg']['s']>=3) $Setalex=28;
		
		if ($d['alex_set']['s']!=$Setalex) {
			setpoint('alex_set', $Setalex, $user.':'.__LINE__);
			$d['alex_set']['s']=$Setalex;
		}
	} elseif ($d['alex_set']['m']==0&&$d['alex_set']['s']=='D') {
		if (($d['alex_temp']['s']<17||$d['alex_temp']['m']<50||$d['net']>0||($d['raamalex']['s']=='Open'&&past('raamalex')>60))&&$d['alex_set']['s']=='D') {
			$power=0;
			store('alex_set', 33, $user.':'.__LINE__, ' Drogen uitschakelen');
		} else $power=1;
	} elseif ($d['alex_set']['m']==1||$d['alex_set']['s']=='D') {
		$power=1;
		$spmode=-1;
	}
	
	if ($d['alex_set']['s']<32||$d['alex_set']['s']=='D') {
		if ($d['daikin']['s']=='On') {
			$fan='A';
			if ($d['alex_set']['s']==1) $fan=3;
			elseif($d['alex_set']['s']==2) $fan=4;
			elseif($d['alex_set']['s']==3) $fan=5;
			elseif($d['alex_set']['s']==4) $fan=6;
			elseif($d['alex_set']['s']==5) {$fan=7;$d['alex_set']['s']=18;}
			if($d['alex_set']['s']=='D') {
				$mode=2;
				$set='M';
			} else {
				$mode=3;
					$set=$d['alex_set']['s']-1;
				if ($time<strtotime('8:30')||$time>strtotime('19:30')&&$set>10)$fan='B';
				$set=$set-($difalex*2);
				$set=ceil($set * 2) / 2;
				if ($set>30) $set=30;
				elseif ($set<18) $set=18;
			}
			if (!isset($power)) $power=$daikin->$k->power;
			if ($daikin->$k->adv == '') {
				$powermode=0;
			} else if (strstr($daikin->$k->adv, '/')) {
				$advs=explode("/", $daikin->$k->adv);
				if ($advs[0]==2) $powermode=2;
				else if ($advs[0]==12) $powermode=1;
				else $powermode=0;
			} else {
				if ($daikin->$k->adv==13)  $powermode=0; //Normal
				else if ($daikin->$k->adv==12)  $powermode=1; // Eco
				else if ($daikin->$k->adv==2)  $powermode=2; // Power
				else if ($daikin->$k->adv=='')  $powermode=0;
			}
			if (($daikin->$k->set!=$set||$daikin->$k->power!=$power||$daikin->$k->mode!=$mode||$daikin->$k->fan!=$fan)&&$powermode<2) {
				if(daikinset($k, $power, 4, $set, basename(__FILE__).':'.__LINE__, $fan, $spmode, $maxpow)) {
					$daikin->$k->power=$power;
					$daikin->$k->mode=4;
					$daikin->$k->fan=$fan;
					$daikin->$k->set=$set;
					$daikin->$k->spmode=$spmode;
					lg(print_r($daikin,true));
				}
			}
		} elseif (isset($power)&&$power==1&&$d['daikin']['s']=='Off') {
			if (past('daikin')>900) sw('daikin', 'On', $user.':'.__LINE__);
		}
	} elseif(past('raamalex')>300&&past('deuralex')>300) {
		$power=0;
		$mode=3;
		if ($daikin->$k->power!=$power||$daikin->$k->mode!=$mode) {
			if(daikinset($k, 0, $power, $set, basename(__FILE__).':'.__LINE__, $fan, $spmode, $maxpow)) {
				$daikin->$k->power=$power;
				$daikin->$k->mode=4;
				$daikin->$k->fan=$fan;
				$daikin->$k->set=$set;
				$daikin->$k->spmode=$spmode;
			}
		}
	}
	unset($power);
	// LIVING
	$k='living';
	$Setliving=33;
//	lg($user.':'.__LINE__);
	if ($d['living_set']['m']==0&&$d['living_set']['s']!='D') {
//		lg($user.':'.__LINE__);
		if (
			($d['raamliving']['s']=='Closed'||($d['raamliving']['s']=='Open'&&past('raamliving')<300))
			&&($d['raamkeuken']['s']=='Closed'||($d['raamkeuken']['s']=='Open'&&past('raamkeuken')<300))
			&&($d['deurinkom']['s']=='Closed'||($d['deurvoordeur']['s']=='Closed'&&$d['deurinkom']['s']=='Open'))
			&&($d['deurgarage']['s']=='Closed'||($d['deurgarage']['s']=='Open'&&past('deurgarage')<300))
		) {
//			lg($user.':'.__LINE__);
			if ($d['weg']['s']>=3) $Setliving=28;
			elseif ($d['weg']['s']>0) $Setliving=25;
			else $Setliving=24;
			if ($d['living_set']['s']=='D') {
				$power=1;
			} else {
				if ($difliving>=0) $power=1;
				elseif ($difliving<-1.5) $power=0;
			}
			if ($d['living_temp']['m']>=20&&$d['living_temp']['m']>=55&&$d['net']<-1000) {
				$power=1;
				store('living_set', 'D', $user.':'.__LINE__, ' Drogen activeren');
		}
		} else $power=0;
//		$Setliving=22;
		if ($d['living_set']['s']!='D'&&$d['living_set']['s']!=$Setliving&&($d['raamliving']['s']=='Closed'||past('raamliving')>60)&&($d['deurinkom']['s']=='Closed'||past('deurinkom')>60)&&($d['deurgarage']['s']=='Closed'||past('deurgarage')>60)) {
			setpoint('living_set', $Setliving, $user.':'.__LINE__);
			$d['living_set']['s']=$Setliving;
		}
		
	} elseif ($d['living_set']['m']==0&&$d['living_set']['s']=='D') {
//		lg($user.':'.__LINE__);
		if (($d['living_temp']['s']<19||$d['living_temp']['m']<50||$d['net']>0)&&$d['living_set']['s']=='D') {
			$power=0;
			store('living_set', 33, $user.':'.__LINE__, ' Drogen uitschakelen');
		} else $power=1;
	} elseif ($d['living_set']['m']==1||$d['living_set']['s']=='D') {
//		lg($user.':'.__LINE__);
		$power=1;
	}
	
//	if (isset($power)) lg('living dif='.$dif.' power='.$power); else lg('living dif='.$dif);
	if ($d['living_set']['s']<32||$d['living_set']['s']=='D') {
//		lg($user.':'.__LINE__);
		if ($d['daikin']['s']=='On') {
//			lg($user.':'.__LINE__);
			$fan='A';
			if ($d['living_set']['s']==1) $fan=3;
			elseif($d['living_set']['s']==2) $fan=4;
			elseif($d['living_set']['s']==3) $fan=5;
			elseif($d['living_set']['s']==4) $fan=6;
			elseif($d['living_set']['s']==5) {$fan=7;$spmode=1;}
			if ($d['eettafel']['s']>0) $fan='B';
			elseif ($d['eettafel']['s']>0) $fan='B';
			elseif ($d['lgtv']['s']=='On') $fan='B';
			if($d['living_set']['s']=='D') {
//				lg($user.':'.__LINE__);
				$mode=2;
				$set='M';
				$spmode=-1;
			} else {
//				lg($user.':'.__LINE__);
				$mode=3;
				$set=$d['living_set']['s']-0;
				$set=$set-($difliving*2);
				$set=ceil($set * 2) / 2;
				if ($set>30) $set=30;
				elseif ($set<18) $set=18;
			}
			if (!isset($power)) $power=$daikin->$k->power;
			if ($daikin->$k->adv == '') {
				$powermode=0;
			} else if (strstr($daikin->$k->adv, '/')) {
				$advs=explode("/", $daikin->$k->adv);
				if ($advs[0]==2) $powermode=2;
				else if ($advs[0]==12) $powermode=1;
				else $powermode=0;
			} else {
				if ($daikin->$k->adv==13)  $powermode=0; //Normal
				else if ($daikin->$k->adv==12)  $powermode=1; // Eco
				else if ($daikin->$k->adv==2)  $powermode=2; // Power
				else if ($daikin->$k->adv=='')  $powermode=0;
			}
			if (($daikin->$k->set!=$set||$daikin->$k->power!=$power||$daikin->$k->mode!=$mode||$daikin->$k->fan!=$fan)&&$powermode<2) {
				if(daikinset($k, $power, 4, $set, basename(__FILE__).':'.__LINE__, $fan, $spmode, $maxpow)) {
					$daikin->$k->power=$power;
					$daikin->$k->mode=4;
					$daikin->$k->fan=$fan;
					$daikin->$k->set=$set;
					$daikin->$k->spmode=$spmode;
					lg(print_r($daikin,true));
				}
			}
		} elseif (isset($power)&&$power==1&&$d['daikin']['s']=='Off') {
			if (past('daikin')>900) sw('daikin', 'On', $user.':'.__LINE__);
		}
	} elseif (past('raamliving')>300&&past('raamkeuken')>300&&past('deurgarage')>300) {
		$power=0;
		$mode=3;
		if ($daikin->$k->power!=$power||$daikin->$k->mode!=$mode) {
			if(daikinset($k, 0, $power, $set, basename(__FILE__).':'.__LINE__, $fan, $spmode, $maxpow)) {
				$daikin->$k->power=$power;
				$daikin->$k->mode=4;
				$daikin->$k->fan=$fan;
				$daikin->$k->set=$set;
				$daikin->$k->spmode=$spmode;
			}
		}
	}
}

require('_Rolluiken_Cooling.php');
require('_TC_badkamer.php');