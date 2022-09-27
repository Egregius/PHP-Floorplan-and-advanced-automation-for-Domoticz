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
if ($d['deurbadkamer']['s']=='Open'&&$d['badkamer_set']['s']!=5&&past('deurbadkamer')>57) {
	if ($d['badkamer_set']['m']>5) store('badkamer_set', 5, basename(__FILE__).':'.__LINE__);
	$d['badkamer_set']['s']=5;
	if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
} elseif ($d['deurbadkamer']['s']=='Closed'&&$d['badkamer_set']['m']>0) {

	$x=21;
	if (past('badkamer_set')>14400&&$d['lichtbadkamer']['s']==0&&$d['buiten_temp']['s']<21&&$d['Weg']['s']<2) {
		if ($d['badkamer_set']['s']>15) {
			store('badkamer_set', 15, basename(__FILE__).':'.__LINE__);
			$d['badkamer_set']['s']=15;
			if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
		}
	} elseif (past('badkamer_set')>14400&&($d['lichtbadkamer']['s']==0&&$d['badkamer_set']['s']!=5) || ($d['Weg']['s']>=2&&$d['badkamer_set']['s']!=5)) {
		store('badkamer_set', 5, basename(__FILE__).':'.__LINE__);
		$d['badkamer_set']['s']=5;
		if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
	}
}
$difbadkamer=$d['badkamer_temp']['s']-$d['badkamer_set']['s'];
if ($difbadkamer<=-5) {
	if ($d['deurbadkamer']['s']=='Closed'&&$d['badkamervuur1']['s']=='Off'&&past('badkamervuur1')>30&&$d['el']['s']<6200) {
		sw('badkamervuur1', 'On', basename(__FILE__).':'.__LINE__);
	}
	if ($d['deurbadkamer']['s']=='Closed'&&$d['badkamervuur2']['s']=='Off'&&past('badkamervuur2')>30&&$d['el']['s']<5800&&$d['badkamer_set']['m']==2) {
		sw('badkamervuur2', 'On', basename(__FILE__).':'.__LINE__);
	}
} elseif ($difbadkamer<=-0.8) {
	if ($d['deurbadkamer']['s']=='Closed'&&$d['badkamervuur1']['s']=='Off'&&past('badkamervuur1')>30&&$d['el']['s']<6200) {
		sw('badkamervuur1', 'On', basename(__FILE__).':'.__LINE__);
	}
	if ($d['deurbadkamer']['s']=='Closed'&&$d['badkamervuur2']['s']=='Off'&&past('badkamervuur2')>30&&$d['el']['s']<5800&&$d['badkamer_set']['m']==2) {
		sw('badkamervuur2', 'On', basename(__FILE__).':'.__LINE__);
	} elseif (($d['badkamervuur2']['s']=='On'&&past('badkamervuur2')>90&&$d['badkamer_set']['m']==1)||$d['el']['s']>6200) {
		sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
	}
} elseif ($difbadkamer<= 0) {
	if ($d['deurbadkamer']['s']=='Closed'&&$d['badkamervuur1']['s']=='Off'&&past('badkamervuur1')>30&&$d['el']['s']<6200) {
		sw('badkamervuur1', 'On', basename(__FILE__).':'.__LINE__);
	}
	if ($d['badkamervuur2']['s']=='On'&&(past('badkamervuur2')>90||$d['badkamer_set']['m']==1||$d['el']['s']>6200)) {
		sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
	}
} else {
	if ($d['badkamervuur2']['s']=='On'&&(past('badkamervuur2')>60||$d['el']['s']>6200)) {
		sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
	}
	if ($d['badkamervuur1']['s']=='On'&&(past('badkamervuur1')>60||$d['el']['s']>7200)) {
		sw('badkamervuur1', 'Off', basename(__FILE__).':'.__LINE__);
	}
}
if ($d['heating']['s']>=2&&$d['deurbadkamer']['s']=='Closed'&&$d['badkamer_temp']['s']<=15&&$d['Weg']['s']<=2&&$d['brander']['s']=="Off"&&past('brander')>900) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
