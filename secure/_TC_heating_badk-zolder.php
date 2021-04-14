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
if ($d['deurbadkamer']['s']=='Open'&&$d['badkamer_set']['s']!=10&&(past('deurbadkamer')>57|| $d['lichtbadkamer']['s']==0)) {
	store('badkamer_set', 10, basename(__FILE__).':'.__LINE__);
	$d['badkamer_set']['s']=10.0;
	if ($d['badkamer_set']['m']==1) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
} elseif ($d['deurbadkamer']['s']=='Closed'&&$d['badkamer_set']['m']==0) {
	$b7=past('$ 8badkamer-7');
	$b7b=past('$ 8Kamer-7');
	if ($b7b<$b7) $b7=$b7b;
	$b7b=past('$ 8badkamer-7d');
	if ($b7b<$b7) $b7=$b7b;
	$x=21;
	/*if ($d['buiten_temp']['s']<21&&$d['lichtbadkamer']['s']>0&&$d['badkamer_set']['s']!=$x&&($b7>900&&$d['heating']['s']>=1&&(TIME>strtotime('5:00')&& TIME<strtotime('7:30')))) {
		store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
		$d['badkamer_set']['s']=$x;
	} else*/if ($b7>900&&$d['lichtbadkamer']['s']==0&&$d['buiten_temp']['s']<21&&$d['Weg']['s']<2) {
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
	if (($d['badkamervuur2']['s']!='Off'&&past('badkamervuur2')>30)||$d['el']['s']>7500) {
		sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
	}
} else {
	if (($d['badkamervuur2']['s']!='Off'&&past('badkamervuur2')>30)||$d['el']['s']>7500) {
		sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
	}
	if (($d['badkamervuur1']['s']!='Off'&&past('badkamervuur1')>30)||$d['el']['s']>8200) {
		sw('badkamervuur1', 'Off', basename(__FILE__).':'.__LINE__);
	}
}
if ($d['heating']['s']>=3&&$d['deurbadkamer']['s']=='Closed'&&$d['badkamer_temp']['s']<15&&$d['Weg']['s']<=2&&$d['brander']['s']=="Off"&&past('brander')>900) sw('brander', 'On', basename(__FILE__).':'.__LINE__);

if ($d['minmaxtemp']['m']>19) {
	if ($d['zolder_set']['s']>4) {
		$d['zolder_set']['s']=4;
		store('zolder_set', 4, basename(__FILE__).':'.__LINE__);
	}

}
$difzolder=number_format($d['zolder_temp']['s']-$d['zolder_set']['s'], 1);

if ($d['Weg']['s']==0&&$difzolder<0&&TIME>=strtotime('7:00')&&TIME<strtotime('21:30')) {
	$difheater1=0;
	$difheater2=-0.5;
	if ($difzolder<=$difheater2&&$d['zoldervuur2']['s']!='On'&&past('zoldervuur2')>90) {
		if ($d['zoldervuur1']['s']!='On') {
			sw('zoldervuur1', 'On', basename(__FILE__).':'.__LINE__);
		}
		sw('zoldervuur2', 'On', basename(__FILE__).':'.__LINE__);
	} elseif ($difzolder<=$difheater1&&$d['zoldervuur1']['s']!='On'&&past('zoldervuur1')>140&&$d['el']['s']<5000) {
		sw('zoldervuur1', 'On', basename(__FILE__).':'.__LINE__);
	} elseif ($difzolder>=$difheater2&&$d['zoldervuur2']['s']!='Off'&&past('zoldervuur2')>110||$d['el']['s']>6000) {
		sw('zoldervuur2', 'Off', basename(__FILE__).':'.__LINE__);
	}
} else {
	//Niet thuis of slapen
	if ($d['zoldervuur2']['s']!='Off') sw('zoldervuur2', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['zoldervuur1']['s']!='Off') sw('zoldervuur1', 'Off', basename(__FILE__).':'.__LINE__);
}
