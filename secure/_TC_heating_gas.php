<?php
/**
 * Pass2PHP Temperature Control
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
//lg(__FILE__);
$kamers=array('living'/*,'kamer','tobi','alex'*/);
$xxkamers=array();
foreach ($kamers as $kamer) {
	${'dif'.$kamer}=number_format(
		$d[$kamer.'_temp']['s']-$d[$kamer.'_set']['s'],
		1
	);
	if (${'dif'.$kamer}<$bigdif) $bigdif=${'dif'.$kamer};
	${'Set'.$kamer}=$d[$kamer.'_set']['s'];
	if (${'dif'.$kamer}<=0) {
		$xxkamers[]=$kamer;
		if ($kamer!='living') $d['heating']['s']=2;
	}
}
$first=true;
$xxxkamers='';
foreach ($xxkamers as $i) {
	if ($first) {
		$xxxkamers=$i;
		$first=false;
	} else {
		$xxxkamers.=', '.$i;
	}
}

if (isset($device)&&isset($difheater2)&&$device=='living_temp') {
	if ($difliving<$difheater2+0.1) {
		lg(
			'heater | Living Set = '.$Setliving
			.' | Living temp = '.$living_temp
			.' | Diff living = '.round($difliving, 2)
			.' | Verbruik = '.$d['el']['s']
			.' | Jaarteller = '.round($d['jaarteller']['s'], 3)
			.' | kamers = '.$xxxkamers
		);
	}
}
/*$kamers=array('tobi','alex','kamer');
foreach ($kamers as $kamer) {
	if (${'dif'.$kamer}<=number_format(($bigdif+ 0.2), 1)&&${'dif'.$kamer}<=0.2) {
		${'RSet'.$kamer}=setradiator(
			$kamer,
			${'dif'.$kamer},
			true,
			$d[$kamer.'_set']['s']
		);
	} else {
		${'RSet'.$kamer}=setradiator(
			$kamer,
			${'dif'.$kamer},
			false,
			$d[$kamer.'_set']['s']
		);
	}
	if (TIME>=strtotime('15:00')&&${'RSet'.$kamer}<15&&$d['raam'.$kamer]['s']=='Closed'&&$d['deur'.$kamer]['s']=='Closed') {
		if ($kamer!='tobi') {
			if ($d[$kamer.'_temp']['s']<15) ${'RSet'.$kamer}=18;
			elseif ($d[$kamer.'_temp']['s']<16) ${'RSet'.$kamer}=17;
			elseif ($d[$kamer.'_temp']['s']<17) ${'RSet'.$kamer}=16;
		} elseif ($kamer=='tobi'&&$d['gcal']['s']) {
			if ($d[$kamer.'_temp']['s']<15) ${'RSet'.$kamer}=18;
			elseif ($d[$kamer.'_temp']['s']<16) ${'RSet'.$kamer}=17;
			elseif ($d[$kamer.'_temp']['s']<17) ${'RSet'.$kamer}=16;
		}
	}
	if (round($d[$kamer.'Z']['s'], 1)!=round(${'RSet'.$kamer}, 1)) {
		ud($kamer.'Z', 0, round(${'RSet'.$kamer}, 0).'.0', basename(__FILE__).':'.__LINE__);
	}
}*/
//lg('bigdif='.$bigdif.'|brander='.$d['brander']['s'].'|timebrander='.past('brander'));
if ($bigdif<=-0.2&&$d['brander']['s']=="Off"&&past('brander')>180) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
elseif ($bigdif<=-0.1&&$d['brander']['s']=="Off"&&past('brander')>300) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
elseif ($bigdif<= 0&&$d['brander']['s']=="Off"&&past('brander')>600) sw('brander','On', basename(__FILE__).':'.__LINE__);
elseif ($bigdif>= 0&&$d['brander']['s']=="On"&&past('brander')>180) sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
elseif ($bigdif>=-0.1&&$d['brander']['s']=="On"&&past('brander')>300) sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
elseif ($bigdif>=-0.2&&$d['brander']['s']=="On"&&past('brander')>900) sw('brander','Off', basename(__FILE__).':'.__LINE__);

if ($bigdif!=$d['bigdif']['m']) storemode('bigdif', $bigdif, basename(__FILE__).':'.__LINE__);

foreach (array('living', 'kamer', 'alex') as $k) {
	${'dif'.$k}=number_format($d[$k.'_temp']['s']-$d[$k.'_set']['s'], 1);
	if (${'dif'.$k}<$bigdif) $bigdif=${'dif'.$k};
	$daikin=json_decode($d['daikin'.$k]['s']);
	if ($daikin->power!=0||$daikin->mode!=4) {
		daikinset($k, 0, 4, 10, basename(__FILE__).':'.__LINE__);
		$data=json_decode($d[$k.'_set']['icon'], true);
		$data['power']=0;
		$data['mode']=4;
		$data['fan']='A';
		$data['set']=10;
		storeicon($k.'_set', json_encode($data));
	}
}
