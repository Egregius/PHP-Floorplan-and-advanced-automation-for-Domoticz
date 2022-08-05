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
foreach (array('zoldervuur1', 'zoldervuur2', 'brander', 'badkamervuur1', 'badkamervuur2') as $i) {
	if ($d[$i]['s']!='Off') sw($i, 'Off', basename(__FILE__).':'.__LINE__);
}
$bigdif=-100;
foreach (array('living','kamer','alex') as $kamer) {
	${'dif'.$kamer}=number_format($d[$kamer.'_temp']['s']-$d[$kamer.'_set']['s'],1);
	if (${'dif'.$kamer}>$bigdif) $bigdif=${'dif'.$kamer};
}
if ($bigdif>=2) $maxpow=100;
elseif ($bigdif>=0.5) $maxpow=60;
else $maxpow=40;
if ($d['daikin']['m']==1) {
	// KAMER
	$Setkamer=33;
	if ($d['kamer_set']['m']==0) {
		if (
				($d['raamkamer']['s']=='Closed'||$d['RkamerR']['s']>=80)
			&&
				(past('raamkamer')>300||TIME>strtotime('19:00'))
			&&
				(
					($d['deurkamer']['s']=='Closed'||($d['deurkamer']['s']=='Open'&&past('deurkamer')<300))
				||
					(
						($d['deuralex']['s']=='Closed'||($d['deuralex']['s']=='Open'&&past('deuralex')<300)||$d['raamalex']['s']=='Closed'||$d['Ralex']['s']>=80)
					&&
						($d['deurspeelkamer']['s']=='Closed'||($d['deurspeelkamer']['s']=='Open'&&past('deurspeelkamer')<300)||$d['raamspeelkamer']['s']=='Closed'||$d['Rspeelkamer']['s']>=80)
					&& $d['raamhall']['s']=='Closed'
					)
				)
		) {
			$base=20.5;
			if (TIME<strtotime('6:30')) $Setkamer=$base;
			elseif (TIME>strtotime('21:00')) $Setkamer=$base;
			elseif (TIME>strtotime('20:00')) $Setkamer=$base+0.5;
			elseif (TIME>strtotime('19:00')) $Setkamer=$base+1;
			elseif (TIME>strtotime('18:00')) $Setkamer=$base+1.5;
			elseif (TIME>strtotime('16:00')) $Setkamer=$base+2;
			elseif (TIME>strtotime('15:00')) $Setkamer=$base+2.5;
			elseif (TIME>strtotime('14:00')) $Setkamer=$base+3;
			elseif (TIME>strtotime('13:00')) $Setkamer=$base+3.5;
			elseif (TIME>strtotime('12:00')) $Setkamer=$base+4;
			elseif (TIME>strtotime('11:00')) $Setkamer=$base+4.5;
			elseif (TIME>strtotime('10:00')) $Setkamer=$base+5;
			elseif (TIME>strtotime('9:00')) $Setkamer=$base+5.5;
			elseif (TIME>strtotime('8:00')) $Setkamer=$base+6;
		}
		if ($d['Weg']['s']>=3) $Setkamer=28;
		if ($d['kamer_set']['s']!=$Setkamer) {
			store('kamer_set', $Setkamer, basename(__FILE__).':'.__LINE__);
			$d['kamer_set']['s']=$Setkamer;
		}
	}
	$dif=$d['kamer_temp']['s']-$d['kamer_set']['s'];
	if ($dif>=0) $power=1;
	elseif ($dif<-1.5) $power=0;
	if ($d['kamer_set']['s']<32) {
		if ($d['daikin']['s']=='On'&&past('daikin')>120) {
			$rate='A';
			if ($d['kamer_set']['s']==1) $rate=3;
			elseif($d['kamer_set']['s']==2) $rate=4;
			elseif($d['kamer_set']['s']==3) $rate=5;
			elseif($d['kamer_set']['s']==4) $rate=6;
			elseif($d['kamer_set']['s']==5) $rate=7;
			$set=$d['kamer_set']['s']-1;
			if (TIME<strtotime('8:30')||$d['Weg']['s']==1&&$set>10)$rate='B';
			$set=ceil($set * 2) / 2;
			if ($set>30) $set=30;
			elseif ($set<18) $set=18;
			$daikin=json_decode($d['daikinkamer']['s']);
			if (!isset($power)) $power=$daikin->power;
			if ($daikin->adv == '') {
				$powermode=0;
			} else if (strstr($daikin->adv, '/')) {
				$advs=explode("/", $daikin->adv);
				if ($advs[0]==2) $powermode=2;
				elseif ($advs[0]==12) $powermode=1;
				else $powermode=0;
			} else {
				if ($daikin->adv==13)  $powermode=0; //Normal
				elseif ($daikin->adv==12)  $powermode=1; // Eco
				elseif ($daikin->adv==2)  $powermode=2; // Power
				elseif ($daikin->adv=='')  $powermode=0;
			}
			if (($daikin->set!=$set||$daikin->power!=$power||$daikin->mode!=3||$daikin->fan!=$rate)&&$powermode<2) {
				$data=json_decode($d['kamer_set']['icon'], true);
				$data['power']=$power;
				$data['mode']=3;
				$data['fan']=$rate;
				$data['set']=$set;
				storeicon('kamer_set', json_encode($data));
				daikinset('kamer', $power, 3, $set, basename(__FILE__).':'.__LINE__, $rate, $maxpow);
			}
		} elseif (isset($power)&&$power==1&&$d['daikin']['s']=='Off') {
			if (past('daikin')>900) sw('daikin', 'On', basename(__FILE__).':'.__LINE__);
		}
	} elseif (past('raamkamer')>300&&past('deurkamer')>300) {
		$daikin=json_decode($d['daikinkamer']['s']);
		if (isset($daikin)&&($daikin->power!=0||$daikin->mode!=3)) {
			$data=json_decode($d['kamer_set']['icon'], true);
			$data['power']=0;
			$data['mode']=3;
			$data['fan']='A';
			$data['set']=33;
			storeicon('kamer_set', json_encode($data));
			daikinset('kamer', 0, 3, 10, basename(__FILE__).':'.__LINE__, 'A', -1, $maxpow);
			unset($daikin);
		}
	}

	// ALEX

	$Setalex=33;
	if ($d['alex_set']['m']==0) {
		if (
				($d['raamalex']['s']=='Closed'||$d['Ralex']['s']>=80)
			&&
				(past('raamalex')>300|| TIME>strtotime('19:00'))
			&&
				(
					($d['deuralex']['s']=='Closed'||($d['deuralex']['s']=='Open'&&past('deuralex')<300))
				||
					(
						($d['deurkamer']['s']=='Closed'||($d['deurkamer']['s']=='Open'&&past('deurkamer')<300)||$d['raamkamer']['s']=='Closed'||$d['RkamerR']['s']>=80)
					&&
						($d['deurspeelkamer']['s']=='Closed'||($d['deurspeelkamer']['s']=='Open'&&past('deurspeelkamer')<300)||$d['raamspeelkamer']['s']=='Closed'||$d['Rspeelkamer']['s']>=80)
					&& $d['raamhall']['s']=='Closed'
					)
				)
		) {
			$base=21;
			if (TIME<strtotime('7:00')) $Setalex=$base;
			elseif (TIME>strtotime('18:30')) $Setalex=$base;
			elseif (TIME>strtotime('17:30')) $Setalex=$base+0.5;
			elseif (TIME>strtotime('16:30')) $Setalex=$base+1;
			elseif (TIME>strtotime('15:30')) $Setalex=$base+1.5;
			elseif (TIME>strtotime('14:30')) $Setalex=$base+2;
			elseif (TIME>strtotime('13:30')) $Setalex=$base+2.5;
			elseif (TIME>strtotime('12:30')) $Setalex=$base+3;
			elseif (TIME>strtotime('11:30')) $Setalex=$base+3.5;
			elseif (TIME>strtotime('10:30')) $Setalex=$base+4;
		}
		if ($d['Weg']['s']>=3) $Setalex=28;
		if ($d['alex_set']['s']!=$Setalex) {
			store('alex_set', $Setalex, basename(__FILE__).':'.__LINE__);
			$d['alex_set']['s']=$Setalex;
		}
	}
	$dif=$d['alex_temp']['s']-$d['alex_set']['s'];
	if ($dif>=0) $power=1;
	elseif ($dif<-1.5) $power=0;
	if ($d['alex_set']['s']<32) {
		if ($d['daikin']['s']=='On'&&past('daikin')>120) {
			$rate='A';
			if ($d['alex_set']['s']==1) $rate=3;
			elseif($d['alex_set']['s']==2) $rate=4;
			elseif($d['alex_set']['s']==3) $rate=5;
			elseif($d['alex_set']['s']==4) $rate=6;
			elseif($d['alex_set']['s']==5) $rate=7;
			$set=$d['alex_set']['s']-1;
			if (TIME<strtotime('8:30')||TIME>strtotime('19:30')&&$set>10)$rate='B';
			$set=ceil($set * 2) / 2;
			if ($set>30) $set=30;
			elseif ($set<18) $set=18;
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
				if (($daikin->set!=$set||$daikin->power!=$power||$daikin->mode!=3||$daikin->fan!=$rate)&&$powermode<2) {
					$data=json_decode($d['alex_set']['icon'], true);
					$data['power']=$power;
					$data['mode']=3;
					$data['fan']=$rate;
					$data['set']=$set;
					storeicon('alex_set', json_encode($data));
					daikinset('alex', $power, 3, $set, basename(__FILE__).':'.__LINE__, $rate, $maxpow);
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
			storeicon('alex_set', json_encode($data));
			daikinset('alex', 0, 3, 10, basename(__FILE__).':'.__LINE__, 'A', -1, $maxpow);
		}
	}

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
		}
		if ($d['living_set']['s']!=$Setliving&&past('raamliving')>60&&($d['deurinkom']['s']=='Closed'||past('deurinkom')>60)&&($d['deurgarage']['s']=='Closed'||past('deurgarage')>60)) {
			store('living_set', $Setliving, basename(__FILE__).':'.__LINE__);
			$d['living_set']['s']=$Setliving;
		}
	}
	$dif=$d['living_temp']['s']-$d['living_set']['s'];
	if ($dif>=0) $power=1;
	elseif ($dif<-1.5) $power=0;
	//lg('dif living='.$dif);
	if ($d['living_set']['s']<32) {
		if ($d['daikin']['s']=='On'&&past('daikin')>120) {
			$rate='A';
			if ($d['living_set']['s']==1) $rate=3;
			elseif($d['living_set']['s']==2) $rate=4;
			elseif($d['living_set']['s']==3) $rate=5;
			elseif($d['living_set']['s']==4) $rate=6;
			elseif($d['living_set']['s']==5) $rate=7;
			$set=$d['living_set']['s']-0.5;
			$set=ceil($set * 2) / 2;
			if ($set>30) $set=30;
			elseif ($set<18) $set=18;
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
				if (($daikin->set!=$set||$daikin->power!=$power||$daikin->mode!=3||$daikin->fan!=$rate)&&$powermode<2) {
					$data=json_decode($d['living_set']['icon'], true);
					$data['power']=$power;
					$data['mode']=3;
					$data['fan']=$rate;
					$data['set']=$set;
					storeicon('living_set', json_encode($data));
					daikinset('living', $power, 3, $set, basename(__FILE__).':'.__LINE__, $rate, -1, $maxpow);
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
			storeicon('living_set', json_encode($data));
			daikinset('living', 0, 3, 10, basename(__FILE__).':'.__LINE__, 'A', -1, $maxpow);
		}
	}
}
$boven=array('Rspeelkamer','Ralex','RkamerL','RkamerR');
$beneden=array('Rbureel','RkeukenL','RkeukenR');
$benedenall=array('Rliving','Rbureel','RkeukenL','RkeukenR');
if (TIME>=$d['civil_twilight']['s']&&TIME<=$d['civil_twilight']['m']) $dag=true; else $dag=false;
$zon=$d['zon']['s'];
$heating=$d['heating']['s'];
if ($d['auto']['s']=='On') {
	if (TIME>=strtotime('5:00')&&TIME<strtotime('10:00')) {
		$dow=date("w");
		if ($dow==0||$dow==6) {
			if ($d['RkamerL']['s']>1&&TIME>=strtotime('7:30')) sl('RkamerL', 1, basename(__FILE__).':'.__LINE__);
			if ($d['RkamerR']['s']>1&&TIME>=strtotime('7:30')) sl('RkamerR', 1, basename(__FILE__).':'.__LINE__);
		} elseif ($dow==2||$dow==5) {
			if ($d['RkamerL']['s']>1&&TIME>=strtotime('6:45')) sl('RkamerL', 1, basename(__FILE__).':'.__LINE__);
			if ($d['RkamerR']['s']>1&&TIME>=strtotime('6:45')) sl('RkamerR', 1, basename(__FILE__).':'.__LINE__);
		} else {
			if ($d['RkamerL']['s']>1&&TIME>=strtotime('7:00')) sl('RkamerL', 1, basename(__FILE__).':'.__LINE__);
			if ($d['RkamerR']['s']>1&&TIME>=strtotime('7:00')) sl('RkamerR', 1, basename(__FILE__).':'.__LINE__);
		}
		if ($dag==true||$d['pirhall']['s']=='On') {
			if (($d['Weg']['s']!=1||$d['pirhall']['s']=='On')&&$d['Rspeelkamer']['s']>1&&TIME>=strtotime('7:30')&&($d['deurspeelkamer']['s']=='Open'||$d['speelkamer']['s']>0)) sl('Rspeelkamer', 1, basename(__FILE__).':'.__LINE__);
			if (($d['Weg']['s']!=1||$d['pirhall']['s']=='On')&&$d['Ralex']['s']>1&&TIME>=strtotime('7:30')&&($d['deuralex']['s']=='Open'||$d['alex']['s']>0)) sl('Ralex', 1, basename(__FILE__).':'.__LINE__);
		}
		if ($dag==true&&$zon==0&&$d['Weg']['s']!=1&&$d['lgtv']['s']=='Off') {
			foreach ($beneden as $i) {
				if ($d[$i]['s']>1) sl($i, 1, basename(__FILE__).':'.__LINE__);
			}
			if ($d['Rliving']['s']>1&&$d['lgtv']['s']=='Off'&&($d['Ralex']['s']<=1||TIME>=strtotime('7:30'))) sl('Rliving', 1, basename(__FILE__).':'.__LINE__);
		} elseif ($dag==true&&$zon>0&&$d['Weg']['s']!=1&&$d['lgtv']['s']=='Off') {
			foreach ($beneden as $i) {
				if ($d[$i]['s']>1) sl($i, 1, basename(__FILE__).':'.__LINE__);
			}
			if ($d['Rliving']['s']>0&&$d['Ralex']['s']<=1) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		}
	}

	elseif (TIME>=strtotime('11:00')&&TIME<strtotime('15:00')) {
		if($zon>1500) {
			if ($d['raamspeelkamer']['s']=='Closed'&&$d['Rspeelkamer']['s']<82&&past('Rspeelkamer')>3600) sl('Rspeelkamer', 82, basename(__FILE__).':'.__LINE__);
			if ($d['raamalex']['s']=='Closed'&&$d['Ralex']['s']<82&&past('Ralex')>3600) sl('Ralex', 82, basename(__FILE__).':'.__LINE__);
		}
	}

	elseif (TIME>=strtotime('15:00')&&TIME<strtotime('22:00')) {
		if($zon>1500) {
			if ($d['raamspeelkamer']['s']=='Closed'&&$d['Rspeelkamer']['s']<82&&past('Rspeelkamer')>3600) sl('Rspeelkamer', 82, basename(__FILE__).':'.__LINE__);
			if ($d['raamalex']['s']=='Closed'&&$d['Ralex']['s']<82&&past('Ralex')>3600) sl('Ralex', 82, basename(__FILE__).':'.__LINE__);
			if ($d['Rbureel']['s']<50&&past('Rbureel')>7200) sl('Rbureel', 50, basename(__FILE__).':'.__LINE__);
		}
	}

	elseif (TIME>=strtotime('22:00')||TIME<strtotime('3:00')) {
		if ($d['Weg']['s']>0) {
			foreach ($beneden as $i) {
				if ($d[$i]['s']<88) sl($i, 88, basename(__FILE__).':'.__LINE__);
			}
			if ($d['Rliving']['s']<86) sl('Rliving', 86, basename(__FILE__).':'.__LINE__);
			foreach ($boven as $i) {
				if ($i=='Rspeelkamer') {
					if ($d['deurspeelkamer']['s']=='Closed'&&$d[$i]['s']<82) sl($i, 82, basename(__FILE__).':'.__LINE__);
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
}
