<?php
/**
 * Pass2PHP Temperature Control Gas heating
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
//lg(__FILE__);
$Setkamer=4;
if ($d['kamer_set']['m']==0) {
	if ($d['buiten_temp']['s']<10&&$d['minmaxtemp']['m']<10&&($d['deurkamer']['s']=='Closed'||($d['deurkamer']['s']=='Open'&&past('deurkamer')<600))&&$d['raamkamer']['s']=='Closed'&&$d['heating']['s']>=1&&(past('raamkamer')>7198 || TIME>strtotime('21:00'))) {
		if (TIME<strtotime('4:30')||TIME>strtotime('21:00')) $Setkamer=10;
	}
	if ($d['kamer_set']['s']!=$Setkamer) {
		store('kamer_set', $Setkamer, basename(__FILE__).':'.__LINE__);
		$d['kamer_set']['s']=$Setkamer;
	}
}

$Settobi=4;
if ($d['tobi_set']['m']==0) {
	if ($d['buiten_temp']['s']<14&&$d['minmaxtemp']['m']<15&&($d['deurtobi']['s']=='Closed'||($d['deurtobi']['s']=='Open'&&past('deurtobi')<600))&&$d['raamtobi']['s']=='Closed'&&$d['heating']['s']>=1&&(past('raamtobi')>7198 || TIME>strtotime('20:00'))) {
		$Settobi=10;
		if ($d['gcal']['s']) {
			if (TIME<strtotime('4:30')||TIME>strtotime('21:00')) $Settobi=14;
		}
	}
	if ($d['tobi_set']['s']!=$Settobi) {
		store('tobi_set', $Settobi, basename(__FILE__).':'.__LINE__);
		$tobi_set=$Settobi;
		$d['tobi_set']['s']=$Settobi;
	}
}

$Setalex=4;
if ($d['alex_set']['m']==0) {
	if ($d['buiten_temp']['s']<16&&$d['minmaxtemp']['m']<15&&($d['deuralex']['s']=='Closed'||($d['deuralex']['s']=='Open'&&past('deuralex')<600))&&$d['raamalex']['s']=='Closed'&&$d['heating']['s']>=1&&(past('raamalex')>1800 || TIME>strtotime('19:00'))) {
		$Setalex=10;
		if (TIME<strtotime('4:30')||TIME>strtotime('19:00')) $Setalex=14;
	}
	if ($d['alex_set']['s']!=$Setalex) {
		store('alex_set', $Setalex, basename(__FILE__).':'.__LINE__);
		$alex_set=$Setalex;
		$d['alex_set']['s']=$Setalex;
	}
}

$Setliving=10;
if ($d['living_set']['m']==0) {
	if ($d['buiten_temp']['s']<20&&$d['minmaxtemp']['m']<20&&$d['heating']['s']>=1&&$d['raamliving']['s']=='Closed'&&$d['deurinkom']['s']=='Closed'&&$d['deurgarage']['s']=='Closed') {
		$Setliving=16;
		if ($d['Weg']['s']==0) {
			if (TIME>=strtotime('18:00')&&TIME<strtotime('18:15')) $Setliving=21.0;
			elseif (TIME>=strtotime('17:30')&&TIME<strtotime('18:15')) $Setliving=20.9;
			elseif (TIME>=strtotime('17:00')&&TIME<strtotime('18:15')) $Setliving=20.8;
			elseif (TIME>=strtotime('16:30')&&TIME<strtotime('18:15')) $Setliving=20.7;
			elseif (TIME>=strtotime('16:00')&&TIME<strtotime('18:15')) $Setliving=20.6;
			elseif (TIME>=strtotime('7:00')&&TIME<strtotime('18:15')) $Setliving=20.5;
			elseif (TIME>=strtotime('6:30')&&TIME<strtotime('18:15')) $Setliving=20.4;
			elseif (TIME>=strtotime('6:00')&&TIME<strtotime('18:15')) $Setliving=20.3;
			elseif (TIME>=strtotime('5:30')&&TIME<strtotime('18:15')) $Setliving=20.2;
			elseif (TIME>=strtotime('5:00')&&TIME<strtotime('18:15')) $Setliving=20.1;
			elseif (TIME>=strtotime('4:30')&&TIME<strtotime('18:15')) $Setliving=20.0;
		} elseif ($d['Weg']['s']==1) {
			$dow=date("w");
			if($dow==0||$dow==6) {
				if (TIME>=strtotime('8:00')&&TIME<strtotime('12:00')) $Setliving=20.0;
				elseif (TIME>=strtotime('7:50')&&TIME<strtotime('12:00')) $Setliving=19.9;
				elseif (TIME>=strtotime('7:40')&&TIME<strtotime('12:00')) $Setliving=19.8;
				elseif (TIME>=strtotime('7:30')&&TIME<strtotime('12:00')) $Setliving=19.7;
				elseif (TIME>=strtotime('7:20')&&TIME<strtotime('12:00')) $Setliving=19.6;
				elseif (TIME>=strtotime('7:10')&&TIME<strtotime('12:00')) $Setliving=19.5;
				elseif (TIME>=strtotime('7:00')&&TIME<strtotime('12:00')) $Setliving=19.4;
				elseif (TIME>=strtotime('6:50')&&TIME<strtotime('12:00')) $Setliving=19.3;
				elseif (TIME>=strtotime('6:40')&&TIME<strtotime('12:00')) $Setliving=19.2;
				elseif (TIME>=strtotime('6:30')&&TIME<strtotime('12:00')) $Setliving=19.1;
				elseif (TIME>=strtotime('6:20')&&TIME<strtotime('12:00')) $Setliving=19.0;
				elseif (TIME>=strtotime('6:10')&&TIME<strtotime('12:00')) $Setliving=18.9;
				elseif (TIME>=strtotime('6:00')&&TIME<strtotime('12:00')) $Setliving=18.8;
				elseif (TIME>=strtotime('5:50')&&TIME<strtotime('12:00')) $Setliving=18.6;
				elseif (TIME>=strtotime('5:40')&&TIME<strtotime('12:00')) $Setliving=18.4;
				elseif (TIME>=strtotime('5:30')&&TIME<strtotime('12:00')) $Setliving=18.2;
				elseif (TIME>=strtotime('5:20')&&TIME<strtotime('12:00')) $Setliving=18.0;
				elseif (TIME>=strtotime('5:10')&&TIME<strtotime('12:00')) $Setliving=17.5;
				elseif (TIME>=strtotime('5:00')&&TIME<strtotime('12:00')) $Setliving=17.0;
				elseif (TIME>=strtotime('4:45')&&TIME<strtotime('12:00')) $Setliving=16.5;
				elseif (TIME>=strtotime('4:30')&&TIME<strtotime('12:00')) $Setliving=16.0;
				elseif (TIME>=strtotime('4:15')&&TIME<strtotime('12:00')) $Setliving=15.5;
				elseif (TIME>=strtotime('4:00')&&TIME<strtotime('12:00')) $Setliving=15.0;
			} else {
				if (TIME>=strtotime('7:00')&&TIME<strtotime('12:00')) $Setliving=20.0;
				elseif (TIME>=strtotime('6:50')&&TIME<strtotime('12:00')) $Setliving=19.9;
				elseif (TIME>=strtotime('6:40')&&TIME<strtotime('12:00')) $Setliving=19.8;
				elseif (TIME>=strtotime('6:30')&&TIME<strtotime('12:00')) $Setliving=19.7;
				elseif (TIME>=strtotime('6:20')&&TIME<strtotime('12:00')) $Setliving=19.6;
				elseif (TIME>=strtotime('6:10')&&TIME<strtotime('12:00')) $Setliving=19.5;
				elseif (TIME>=strtotime('6:00')&&TIME<strtotime('12:00')) $Setliving=19.4;
				elseif (TIME>=strtotime('5:50')&&TIME<strtotime('12:00')) $Setliving=19.3;
				elseif (TIME>=strtotime('5:40')&&TIME<strtotime('12:00')) $Setliving=19.2;
				elseif (TIME>=strtotime('5:30')&&TIME<strtotime('12:00')) $Setliving=19.1;
				elseif (TIME>=strtotime('5:20')&&TIME<strtotime('12:00')) $Setliving=19.0;
				elseif (TIME>=strtotime('5:10')&&TIME<strtotime('12:00')) $Setliving=18.9;
				elseif (TIME>=strtotime('5:00')&&TIME<strtotime('12:00')) $Setliving=18.8;
				elseif (TIME>=strtotime('4:50')&&TIME<strtotime('12:00')) $Setliving=18.6;
				elseif (TIME>=strtotime('4:40')&&TIME<strtotime('12:00')) $Setliving=18.4;
				elseif (TIME>=strtotime('4:30')&&TIME<strtotime('12:00')) $Setliving=18.2;
				elseif (TIME>=strtotime('4:20')&&TIME<strtotime('12:00')) $Setliving=18.0;
				elseif (TIME>=strtotime('4:10')&&TIME<strtotime('12:00')) $Setliving=17.5;
				elseif (TIME>=strtotime('4:00')&&TIME<strtotime('12:00')) $Setliving=17.0;
				elseif (TIME>=strtotime('3:45')&&TIME<strtotime('12:00')) $Setliving=16.5;
				elseif (TIME>=strtotime('3:30')&&TIME<strtotime('12:00')) $Setliving=16.0;
				elseif (TIME>=strtotime('3:15')&&TIME<strtotime('12:00')) $Setliving=15.5;
				elseif (TIME>=strtotime('3:00')&&TIME<strtotime('12:00')) $Setliving=15.0;
			}
		} elseif ($d['Weg']['s']>=2) {
			$Setliving=14.0;
		}
		if ($Setliving>19.5&&TIME>=strtotime('11:00')&&$d['zon']['s']>3000&&$d['buiten_temp']['s']>15) $Setliving=19.5;
	}
	if ($d['living_set']['s']!=$Setliving&&past('raamliving')>60&&past('deurinkom')>60&&past('deurgarage')>60) {
		store('living_set', $Setliving, basename(__FILE__).':'.__LINE__);
		$living_set=$Setliving;
		$d['living_set']['s']=$Setliving;
	}
}
$kamers=array('living'/*,'kamer','tobi','alex'*/);
$bigdif=100;
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
if ($d['heating']['s']==2) {
	if ($bigdif<=-0.2&&$d['brander']['s']=="Off"&&past('brander')>180) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
	elseif ($bigdif<=-0.1&&$d['brander']['s']=="Off"&&past('brander')>300) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
	elseif ($bigdif<= 0&&$d['brander']['s']=="Off"&&past('brander')>600) sw('brander','On', basename(__FILE__).':'.__LINE__);
	elseif ($bigdif>= 0&&$d['brander']['s']=="On"&&past('brander')>180) sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
	elseif ($bigdif>=-0.1&&$d['brander']['s']=="On"&&past('brander')>300) sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
	elseif ($bigdif>=-0.2&&$d['brander']['s']=="On"&&past('brander')>900) sw('brander','Off', basename(__FILE__).':'.__LINE__);
} elseif ($d['brander']['s']=='On') sw('brander', 'Off', basename(__FILE__).':'.__LINE__);

if ($bigdif!=$d['bigdif']['m']) storemode('bigdif', $bigdif, basename(__FILE__).':'.__LINE__);

if ($d['deurbadkamer']['s']=='Open'&&$d['badkamer_set']['s']!=10&&(past('deurbadkamer')>57|| $d['lichtbadkamer']['s']==0)) {
	store('badkamer_set', 10, basename(__FILE__).':'.__LINE__);
	$d['badkamer_set']['s']=10.0;
	if ($d['badkamer_set']['m']==1) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
} elseif ($d['deurbadkamer']['s']=='Closed'&&$d['badkamer_set']['m']==0) {
	$b7=past('$ 8badkamer-7');
	$b7b=past('8Kamer-7');
	if ($b7b<$b7) $b7=$b7b;
	$x=21;
	if ($d['buiten_temp']['s']<21&&$d['lichtbadkamer']['s']>0&&$d['badkamer_set']['s']!=$x&&($b7>900&&$d['heating']['s']>=1&&(TIME>strtotime('5:00')&& TIME<strtotime('7:30')))) {
		store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
		$d['badkamer_set']['s']=$x;
	} elseif ($b7>900&&$d['lichtbadkamer']['s']==0&&$d['buiten_temp']['s']<21&&$d['Weg']['s']<2) {
		if ($d['badkamer_set']['s']!=10) {
			store('badkamer_set', 10, basename(__FILE__).':'.__LINE__);
			$d['badkamer_set']['s']=10.0;
		}
	} elseif ($b7>900&&($d['lichtbadkamer']['s']==0&&$d['badkamer_set']['s']!=10)
		|| ($d['Weg']['s']>=2&&$d['badkamer_set']['s']!=10)) {
		store('badkamer_set', 10, basename(__FILE__).':'.__LINE__);
		$d['badkamer_set']['s']=10.0;
	} elseif ($d['lichtbadkamer']['s']==0&&$d['badkamer_set']['s']!=10) {
		store('badkamer_set', 10, basename(__FILE__).':'.__LINE__);
		$d['badkamer_set']['s']=10.0;
	}
}
$difbadkamer=$d['badkamer_temp']['s']-$d['badkamer_set']['s'];
if ($difbadkamer<=-1) {
	if ($d['deurbadkamer']['s']=='Closed'&&$d['badkamervuur1']['s']!='On'&&past('badkamervuur1')>30&&$d['el']['s']<7200) {
		sw('badkamervuur1', 'On', basename(__FILE__).':'.__LINE__);
	}
	if ($d['deurbadkamer']['s']=='Closed'&&$d['badkamervuur2']['s']!='On'&&past('badkamervuur2')>30&&$d['lichtbadkamer']['s']>0&&$d['el']['s']<6800) {
		sw('badkamervuur2', 'On', basename(__FILE__).':'.__LINE__);
	}
} elseif ($difbadkamer<= 0) {
	if ($d['deurbadkamer']['s']=='Closed'&&$d['badkamervuur1']['s']!='On'&&past('badkamervuur1')>30&&$d['el']['s']<7200) {
		sw('badkamervuur1', 'On', basename(__FILE__).':'.__LINE__);
	}
	if (($d['badkamervuur2']['s']!='Off'&&past('badkamervuur2')>30)
		|| $d['el']['s']>7500) {
		sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
	}
} else {
	if (($d['badkamervuur2']['s']!='Off'&&past('badkamervuur2')>30)
		|| $d['el']['s']>7500) {
		sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
	}
	if (($d['badkamervuur1']['s']!='Off'&&past('badkamervuur1')>30)
		|| $d['el']['s']>8200) {
		sw('badkamervuur1', 'Off', basename(__FILE__).':'.__LINE__);
	}
}
if ($d['minmaxtemp']['m']>19) {
	if ($d['zolder_set']['s']>4) {
		$d['zolder_set']['s']=4;
		store('zolder_set', 4, basename(__FILE__).':'.__LINE__);
	}

}
$difzolder=number_format($d['zolder_temp']['s']-$d['zolder_set']['s'], 1);

if ($d['Weg']['s']==0&&$difzolder<0&&TIME>=strtotime('7:00')&&TIME<strtotime('21:30')) {
	$difheater1=0;
	$difheater2=-2;
	if ($difzolder<=$difheater2&&$d['zoldervuur2']['s']!='On'&&past('zoldervuur2')>90) {
		if ($d['zoldervuur1']['s']!='On') {
			sw('zoldervuur1', 'On', basename(__FILE__).':'.__LINE__);
		}
		sw('zoldervuur2', 'On', basename(__FILE__).':'.__LINE__);
	} elseif ($difzolder<=$difheater1&&$d['zoldervuur1']['s']!='On'&&past('zoldervuur1')>140&&$d['el']['s']<8000) {
		sw('zoldervuur1', 'On', basename(__FILE__).':'.__LINE__);
	} elseif ($difzolder>=$difheater2&&$d['zoldervuur2']['s']!='Off'&&past('zoldervuur2')>110||$d['el']['s']>8500) {
		sw('zoldervuur2', 'Off', basename(__FILE__).':'.__LINE__);
	}
} else {
	//Niet thuis of slapen
	if ($d['zoldervuur2']['s']!='Off') sw('zoldervuur2', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['zoldervuur1']['s']!='Off') sw('zoldervuur1', 'Off', basename(__FILE__).':'.__LINE__);
}


foreach (array('living', 'kamer', 'alex') as $k) {
	${'dif'.$k}=number_format($d[$k.'_temp']['s']-$d[$k.'_set']['s'], 1);
	if (${'dif'.$k}<$bigdif) $bigdif=${'dif'.$k};
	$daikin=json_decode($d['daikin'.$k]['s']);

//	lg($k.' corr='.$corr.' set='.$set.' temp='.$d[$k.'_temp']['s']);
	if ($d[$k.'_set']['s']>22) $d[$k.'_set']['s']=22;
	if ($d[$k.'_set']['s']>21&&$d['Weg']['s']==0) {
		if (${'dif'.$k}>=0.3) {$power=0;}
		else {$power=1;}
		$rate='A';
		if ($k=='living') $set=$d[$k.'_set']['s'];
		elseif ($k=='kamer') $set=$d[$k.'_set']['s']-2;
		elseif ($k=='alex') {
			$set=$d[$k.'_set']['s']-2;
			if (TIME<strtotime('8:30')||TIME>strtotime('19:30'))$rate='B';
		}
		$set=ceil($set * 2) / 2;

		if ($daikin->stemp!=$set||$daikin->pow!=$power||$daikin->mode!=4||$daikin->f_rate!=$rate) {
			daikinset($k, $power, 4, $set, basename(__FILE__).':'.__LINE__, $rate);
			$data=json_decode($d[$k.'_set']['icon'], true);
			$data['power']=$power;
			$data['mode']=4;
			$data['fan']=$rate;
			$data['set']=$d[$k.'_set']['s'];
			storeicon($k.'_set', json_encode($data));
		}
	} else {
		if ($daikin->pow!=0||$daikin->mode!=4) {
			daikinset($k, 0, 4, 10, basename(__FILE__).':'.__LINE__);
			$data=json_decode($d[$k.'_set']['icon'], true);
			$data['power']=$power;
			$data['mode']=4;
			$data['fan']=$rate;
			$data['set']=$d[$k.'_set']['s'];
			storeicon($k.'_set', json_encode($data));
		}
	}
}
include('_Rolluiken_Heating.php');
