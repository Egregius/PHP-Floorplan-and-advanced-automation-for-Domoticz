<?php
$user=basename(__FILE__);
$kamers=array('living',/*'kamer','alex','badkamer'*/);
foreach ($kamers as $kamer) {
	${'dif'.$kamer}=number_format($d[$kamer.'_temp']['s']-$d[$kamer.'_set']['s'],1);
	if (${'dif'.$kamer}<$bigdif) $bigdif=${'dif'.$kamer};
}

$aanna=(1/(21-$d['buiten_temp']['s']))*6000; if ($aanna<1000) $aanna=1000;
$uitna=(21-$d['buiten_temp']['s'])*60; if ($uitna<595) $uitna=595;

if ($bigdif<=-0.2&&$d['brander']['s']=="Off"&&past('brander')>$aanna*0.6) sw('brander', 'On', 'Aan na = '.$aanna*0.6.' '.basename(__FILE__).':'.__LINE__);
elseif ($bigdif<=-0.1&&$d['brander']['s']=="Off"&&past('brander')>$aanna*0.8) sw('brander', 'On', 'Aan na = '.$aanna*0.8.' '.basename(__FILE__).':'.__LINE__);
elseif ($bigdif<= 0  &&$d['brander']['s']=="Off"&&past('brander')>$aanna) sw('brander','On', 'Aan na = '.$aanna.' '.basename(__FILE__).':'.__LINE__);
elseif ($bigdif<= 0.1&&$d['brander']['s']=="Off"&&past('brander')>$aanna*1.5) sw('brander','On', 'Aan na = '.$aanna.' '.basename(__FILE__).':'.__LINE__);
elseif ($bigdif>= 0.1&&$d['brander']['s']=="On"&&(past('brander')>$uitna*0.75||$d['living_temp']['icon']>=0.2)) sw('brander', 'Off', 'Uit na = '.$uitna*0.75.' '.basename(__FILE__).':'.__LINE__);
elseif ($bigdif>= 0 &&$d['brander']['s']=="On"&&(past('brander')>$uitna||$d['living_temp']['icon']>=0.2)) sw('brander', 'Off', 'Uit na = '.$uitna.' '.basename(__FILE__).':'.__LINE__);
elseif ($bigdif>=-0.1&&$d['brander']['s']=="On"&&(past('brander')>$uitna*1.25||$d['living_temp']['icon']>=0.2)) sw('brander', 'Off', 'Uit na = '.$uitna*2 .' '.basename(__FILE__).':'.__LINE__);
elseif ($bigdif>=-0.2&&$d['brander']['s']=="On"&&(past('brander')>$uitna*1.5||$d['living_temp']['icon']>=0.3)) sw('brander','Off', 'Uit na = '.$uitna*4 .' '.basename(__FILE__).':'.__LINE__);

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
			$data['spmode']=$daikin->spmode;
			$data['maxpow']=$daikin->maxpow;
			storeicon($k.'_set', json_encode($data));
		}
	}
}
if ($d['deurbadkamer']['s']=='Closed'||($d['deurbadkamer']['s']=='Open'&&$d['raamkamer']['s']=='Closed'&&$d['raamwaskamer']['s']=='Closed'&&$d['raamalex']['s']=='Closed')) {
	if ($d['brander']['s']=='Off'&&$d['badkamer_temp']['s']<14&&past('brander')>=595) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
}
