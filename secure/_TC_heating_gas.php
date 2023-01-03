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
$kamers=array('living','kamer','alex','badkamer');
foreach ($kamers as $kamer) {
	${'dif'.$kamer}=number_format($d[$kamer.'_temp']['s']-$d[$kamer.'_set']['s'],1);
	if (${'dif'.$kamer}<$bigdif) $bigdif=${'dif'.$kamer};
}

$aanna=(1/(21-$d['buiten_temp']['s']))*6000; if ($aanna<900) $aanna=900;
$uitna=(21-$d['buiten_temp']['s'])*60; if ($uitna<475) $uitna=475;

//lg('bigdif='.$bigdif.'	difgas='.$difgas.'	aanna='.$aanna.'	uitna='.$uitna);


if ($bigdif<=-0.2&&$d['brander']['s']=="Off"&&past('brander')>$aanna*0.6) sw('brander', 'On', 'Aan na = '.$aanna*0.6.' '.basename(__FILE__).':'.__LINE__);
elseif ($bigdif<=-0.1&&$d['brander']['s']=="Off"&&past('brander')>$aanna*0.8) sw('brander', 'On', 'Aan na = '.$aanna*0.8.' '.basename(__FILE__).':'.__LINE__);
elseif ($bigdif<= 0&&$d['brander']['s']=="Off"&&past('brander')>$aanna) sw('brander','On', 'Aan na = '.$aanna.' '.basename(__FILE__).':'.__LINE__);
elseif ($bigdif<= 0.1&&$d['brander']['s']=="Off"&&past('brander')>$aanna*1.5) sw('brander','On', 'Aan na = '.$aanna.' '.basename(__FILE__).':'.__LINE__);
elseif ($bigdif<= 0.2&&$d['brander']['s']=="Off"&&past('brander')>$aanna*2) sw('brander','On', 'Aan na = '.$aanna.' '.basename(__FILE__).':'.__LINE__);
elseif ($bigdif>= 0.1&&$d['brander']['s']=="On") sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
elseif ($bigdif>= 0&&$d['brander']['s']=="On"&&past('brander')>$uitna) sw('brander', 'Off', 'Uit na = '.$uitna.' '.basename(__FILE__).':'.__LINE__);
elseif ($bigdif>=-0.1&&$d['brander']['s']=="On"&&past('brander')>$uitna*1.25) sw('brander', 'Off', 'Uit na = '.$uitna*1.25 .' '.basename(__FILE__).':'.__LINE__);
elseif ($bigdif>=-0.2&&$d['brander']['s']=="On"&&past('brander')>$uitna*1.5) sw('brander','Off', 'Uit na = '.$uitna*1.5 .' '.basename(__FILE__).':'.__LINE__);

if ($d['daikin']['m']==1) {
	foreach (array('living', 'kamer', 'alex') as $k) {
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
}