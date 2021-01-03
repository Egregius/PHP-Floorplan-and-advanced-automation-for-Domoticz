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
include('_TC_heating.php');

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
if ($d['Weg']['s']==1) {
	if ($bigdif<=-0.2&&$d['brander']['s']=="Off"&&past('brander')>300) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
	elseif ($bigdif<=-0.1&&$d['brander']['s']=="Off"&&past('brander')>600) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
	elseif ($bigdif<=-0&&$d['brander']['s']=="Off"&&past('brander')>900) sw('brander','On', basename(__FILE__).':'.__LINE__);
	elseif ($bigdif>=-0&&$d['brander']['s']=="On"&&past('brander')>300) sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
	elseif ($bigdif>=-0.1&&$d['brander']['s']=="On"&&past('brander')>600) sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
	elseif ($bigdif>=-0.2&&$d['brander']['s']=="On"&&past('brander')>900) sw('brander','Off', basename(__FILE__).':'.__LINE__);
} else {
	if ($bigdif<=-0.7&&$d['brander']['s']=="Off"&&past('brander')>300) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
	elseif ($bigdif<=-0.6&&$d['brander']['s']=="Off"&&past('brander')>600) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
	elseif ($bigdif<=-0.5&&$d['brander']['s']=="Off"&&past('brander')>900) sw('brander','On', basename(__FILE__).':'.__LINE__);
	elseif ($bigdif>=-0.5&&$d['brander']['s']=="On"&&past('brander')>300) sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
	elseif ($bigdif>=-0.6&&$d['brander']['s']=="On"&&past('brander')>600) sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
	elseif ($bigdif>=-0.7&&$d['brander']['s']=="On"&&past('brander')>900) sw('brander','Off', basename(__FILE__).':'.__LINE__);
}

if ($bigdif!=$d['bigdif']['m']) storemode('bigdif', $bigdif, basename(__FILE__).':'.__LINE__);

foreach (array('living', 'kamer', 'alex') as $k) {
	${'dif'.$k}=number_format($d[$k.'_temp']['s']-$d[$k.'_set']['s'], 1);
	if (${'dif'.$k}<$bigdif) $bigdif=${'dif'.$k};
	$daikin=json_decode($d['daikin'.$k]['s']);

//	lg($k.' corr='.$corr.' set='.$set.' temp='.$d[$k.'_temp']['s']);
	if ($d[$k.'_set']['s']>22) $d[$k.'_set']['s']=22;
	if ($d[$k.'_set']['s']>10) {
		if (${'dif'.$k}>=0.4)		{$power=0;$set=$d[$k.'_set']['s'];}
		elseif (${'dif'.$k}>=0.3)	{$power=1;$set=$d[$k.'_set']['s']-3;}
		elseif (${'dif'.$k}>=0.2)	{$power=1;$set=$d[$k.'_set']['s']+1;}
		elseif (${'dif'.$k}>=0.1)	{$power=1;$set=$d[$k.'_set']['s']+1.5;}
		elseif (${'dif'.$k}>=0)	{$power=1;$set=$d[$k.'_set']['s']+2;}
		elseif (${'dif'.$k}>=-0.1)	{$power=1;$set=$d[$k.'_set']['s']+2;}
		elseif (${'dif'.$k}>=-0.2)	{$power=1;$set=$d[$k.'_set']['s']+2;}
		elseif (${'dif'.$k}>=-0.3)	{$power=1;$set=$d[$k.'_set']['s']+2.5;}
		elseif (${'dif'.$k}>=-0.4)	{$power=1;$set=$d[$k.'_set']['s']+2.5;}
		elseif (${'dif'.$k}>=-0.5)	{$power=1;$set=$d[$k.'_set']['s']+2.5;}
		elseif (${'dif'.$k}>=-0.6)	{$power=1;$set=$d[$k.'_set']['s']+2.5;}
		elseif (${'dif'.$k}>=-0.7)	{$power=1;$set=$d[$k.'_set']['s']+2.5;}
		elseif (${'dif'.$k}>=-0.8)	{$power=1;$set=$d[$k.'_set']['s']+2.5;}
		elseif (${'dif'.$k}>=-0.9)	{$power=1;$set=$d[$k.'_set']['s']+2.5;}
		elseif (${'dif'.$k}>=-1.0)	{$power=1;$set=$d[$k.'_set']['s']+2.5;}
		elseif (${'dif'.$k}>=-1.1)	{$power=1;$set=$d[$k.'_set']['s']+3;}
		elseif (${'dif'.$k}>=-1.2)	{$power=1;$set=$d[$k.'_set']['s']+3;}
		elseif (${'dif'.$k}>=-1.3)	{$power=1;$set=$d[$k.'_set']['s']+3;}
		elseif (${'dif'.$k}>=-1.4)	{$power=1;$set=$d[$k.'_set']['s']+3;}
		elseif (${'dif'.$k}>=-1.5)	{$power=1;$set=$d[$k.'_set']['s']+3;}
		if (${'dif'.$k}>=1)		{$power=0;$set=$d[$k.'_set']['s'];}
		else {$power=1;$set=$d[$k.'_set']['s']+1;}
		$rate='A';
		if ($k=='living') $set=$set;
		elseif ($k=='kamer') $set=$set-2;
		elseif ($k=='alex') {
			$set=$set-2;
			if (TIME<strtotime('8:30')||TIME>strtotime('19:30'))$rate='B';
		}
		$set=ceil($set * 2) / 2;
		if ($set>25) $set=25;
		elseif ($set<10) $set=10;

		if ($daikin->stemp!=$set||$daikin->pow!=$power||$daikin->mode!=4||$daikin->f_rate!=$rate) {
			$data=json_decode($d[$k.'_set']['icon'], true);
			$data['power']=$power;
			$data['mode']=4;
			$data['fan']=$rate;
			$data['set']=$set;
			storeicon($k.'_set', json_encode($data));
			daikinset($k, $power, 4, $set, basename(__FILE__).':'.__LINE__, $rate);
		}
	} else {
		if ($daikin->pow!=0||$daikin->mode!=4) {
			$data=json_decode($d[$k.'_set']['icon'], true);
			$data['power']=$power;
			$data['mode']=4;
			$data['fan']=$rate;
			$data['set']=$set;
			storeicon($k.'_set', json_encode($data));
			daikinset($k, 0, 4, 10, basename(__FILE__).':'.__LINE__);
		}
	}
}
include('_TC_heating_badk-zolder.php');
include('_Rolluiken_Heating.php');
