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
$l=0;
if ($d['deurbadkamer']['s']=='Open'&&$d['badkamer_set']['s']!=5&&past('deurbadkamer')>57) {
	if ($d['badkamer_set']['s']>5) store('badkamer_set', 5, basename(__FILE__).':'.__LINE__);
	$d['badkamer_set']['s']=5;
	if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
} elseif ($d['deurbadkamer']['s']=='Closed'&&$d['badkamer_set']['m']>0) {
	if (past('badkamer_set')>14400&&$d['lichtbadkamer']['s']==0&&$d['buiten_temp']['s']<21&&$d['Weg']['s']<2) {
		if ($d['badkamer_set']['s']>16.2) {
			store('badkamer_set', 16.2, basename(__FILE__).':'.__LINE__);
			$d['badkamer_set']['s']=16.2;
			if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
		}
	} elseif (past('badkamer_set')>14400&&($d['lichtbadkamer']['s']==0&&$d['badkamer_set']['s']!=16.2) || ($d['Weg']['s']>=2&&$d['badkamer_set']['s']!=16.2)) {
		store('badkamer_set', 16.2, basename(__FILE__).':'.__LINE__);
		$d['badkamer_set']['s']=16.2;
		if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
	}
} elseif ($d['deurbadkamer']['s']=='Closed'&&$d['badkamer_set']['m']==0&&$d['heating']['s']>=1) {
	if ($d['lichtbadkamer']['s']==0&&$d['buiten_temp']['s']<20&&$d['Weg']['s']<2) {
		if ($d['badkamer_set']['s']!=16.2) {
			store('badkamer_set', 16.2, basename(__FILE__).':'.__LINE__);
			$d['badkamer_set']['s']=16.2;
		}
	}
} elseif ($d['deurbadkamer']['s']=='Closed'&&$d['badkamer_set']['m']==0&&$d['heating']['s']<1) {
	if ($d['badkamer_set']['s']!=5) {
		store('badkamer_set', 5, basename(__FILE__).':'.__LINE__);
		$d['badkamer_set']['s']=5;
	}
}
$difbadkamer=$d['badkamer_temp']['s']-$d['badkamer_set']['s'];
if ($d['badkamer_set']['s']==16.2&&$d['heating']['s']>=3) $difbadkamer+=0.5;


if ($d['badkamer_temp']['m']>=55&&($d['deurbadkamer']['s']=='Closed'||($d['deurbadkamer']['s']=='Open'&&past('deurbadkamer')<60))) $l=1;
if ($d['deurbadkamer']['s']=='Open'&&past('deurbadkamer')>60) {
	if ($d['deurkamer']['s']=='Closed'||($d['deurkamer']['s']=='Open'&&$d['raamkamer']['s']=='Closed'&&$d['kamer_temp']['m']>=55)) $l=1;
	if ($d['deurspeelkamer']['s']=='Closed'||($d['deurspeelkamer']['s']=='Open'&&$d['raamspeelkamer']['s']=='Closed'&&$d['speelkamer_temp']['m']>=55)) $l=1;
	if ($d['deuralex']['s']=='Closed'||($d['deuralex']['s']=='Open'&&$d['raamalex']['s']=='Closed'&&$d['alex_temp']['m']>=55)) $l=1;
}

if ($difbadkamer<=-1) $l=3;
elseif ($difbadkamer<=-0.5) $l=2;
elseif ($difbadkamer< 0) $l=1;
else {
	if ($difbadkamer>0.2&&$d['badkamer_set']['s']>19) {
		if ($d['badkamer_set']['s']>16.2) {
			store('badkamer_set', 16.2, basename(__FILE__).':'.__LINE__);
		
		}
		if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
	}
}

if ($d['deurbadkamer']['s']=='Open'&&past('deurbadkamer')>60) {
	if ($d['deurkamer']['s']=='Open'&&$d['raamkamer']['s']=='Open') $l=0;
	if ($d['deurspeelkamer']['s']=='Open'&&$d['raamspeelkamer']['s']=='Open') $l=0;
	if ($d['deuralex']['s']=='Open'&&$d['raamalex']['s']=='Open') $l=0;
}

if ($d['Weg']['s']>2) $l=0;
if ($l==0) {
	if ($d['luchtdroger']['s']=='On') {
		if ($d['luchtdroger1']['s']=='On') sw('luchtdroger1', 'Off', basename(__FILE__).':'.__LINE__);
		if ($d['luchtdroger2']['s']=='On') sw('luchtdroger2', 'Off', basename(__FILE__).':'.__LINE__);
		if ($d['luchtdroger1']['s']=='Off'&&$d['luchtdroger2']['s']=='Off'&&past('luchtdroger1')>115&&past('luchtdroger2')>115) sw('luchtdroger', 'Off', basename(__FILE__).':'.__LINE__);
	}
} elseif ($l==1) {
	if ($d['luchtdroger']['s']=='Off') sw('luchtdroger', 'On', basename(__FILE__).':'.__LINE__);
	else {
		if ($d['luchtdroger1']['s']=='Off') sw('luchtdroger1', 'On', basename(__FILE__).':'.__LINE__);
		if ($d['luchtdroger2']['s']=='On') sw('luchtdroger2', 'Off', basename(__FILE__).':'.__LINE__);
	}
} elseif ($l==2) {
	if ($d['luchtdroger']['s']=='Off') sw('luchtdroger', 'On', basename(__FILE__).':'.__LINE__);
	else {
		if ($d['luchtdroger1']['s']=='On') sw('luchtdroger1', 'Off', basename(__FILE__).':'.__LINE__);
		if ($d['luchtdroger2']['s']=='Off') sw('luchtdroger2', 'On', basename(__FILE__).':'.__LINE__);
	}
} elseif ($l==3) {
	if ($d['luchtdroger']['s']=='Off') sw('luchtdroger', 'On', basename(__FILE__).':'.__LINE__);
	else {
		if ($d['luchtdroger1']['s']=='Off') sw('luchtdroger1', 'On', basename(__FILE__).':'.__LINE__);
		if ($d['luchtdroger2']['s']=='Off') sw('luchtdroger2', 'On', basename(__FILE__).':'.__LINE__);
	}
} 
