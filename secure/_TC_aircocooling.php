<?php
/**
 * Pass2PHP Airco cooling
 * php version 7.3.4-2
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/

foreach	(array('zoldervuur1', 'zoldervuur2', 'brander', 'badkamervuur1', 'badkamervuur2') as $i) {
	if ($d[$i]['s']!='Off') sw($i, 'Off', basename(__FILE__).':'.__LINE__);
}

$Setkamer=33;
if ($d['kamer_set']['m']==0) {
    if ($d['buiten_temp']['s']>21&&$d['minmaxtemp']['m']>21/*&&$d['deurkamer']['s']=='Closed'*/&&$d['raamkamer']['s']=='Closed'&&(past('raamkamer')>7198 || TIME>strtotime('21:00'))) {
        $Setkamer=26;
        if (TIME<strtotime('4:00')) $Setkamer=22.0;
        elseif (TIME>strtotime('21:00')) $Setkamer=22.0;
        elseif (TIME>strtotime('20:00')) $Setkamer=22.5;
        elseif (TIME>strtotime('19:00')) $Setkamer=23.0;
        elseif (TIME>strtotime('18:00')) $Setkamer=23.5;
        elseif (TIME>strtotime('17:00')) $Setkamer=24.0;
        elseif (TIME>strtotime('16:00')) $Setkamer=24.5;
        elseif (TIME>strtotime('15:00')) $Setkamer=25.0;
        elseif (TIME>strtotime('14:00')) $Setkamer=25.5;
    }
    if ($d['kamer_set']['s']!=$Setkamer) {
        store('kamer_set', $Setkamer, basename(__FILE__).':'.__LINE__);
        $d['kamer_set']['s']=$Setkamer;
    }
}

$Setalex=33;
if ($d['alex_set']['m']==0) {
    if ($d['buiten_temp']['s']>21&&$d['minmaxtemp']['m']>21/*&&$d['deuralex']['s']=='Closed'*/&&$d['raamalex']['s']=='Closed'&&(past('raamalex')>1800 || TIME>strtotime('19:00'))) {
        $Setalex=26;
        if (TIME<strtotime('4:00')) $Setkamer=22.0;
        elseif (TIME>strtotime('19:00')) $Setkamer=22.0;
        elseif (TIME>strtotime('18:00')) $Setkamer=22.5;
        elseif (TIME>strtotime('17:00')) $Setkamer=23.0;
        elseif (TIME>strtotime('16:00')) $Setkamer=23.5;
        elseif (TIME>strtotime('15:00')) $Setkamer=24.0;
        elseif (TIME>strtotime('14:00')) $Setkamer=24.5;
        elseif (TIME>strtotime('13:00')) $Setkamer=25.0;
        elseif (TIME>strtotime('12:00')) $Setkamer=25.5;    }
    if ($d['alex_set']['s']!=$Setalex) {
        ud('alex_set', 0, $Setalex, true, basename(__FILE__).':'.__LINE__);
        $alex_set=$Setalex;
        $d['alex_set']['s']=$Setalex;
    }
}

$Setliving=33;
if ($d['living_set']['m']==0) {
    if ($d['buiten_temp']['s']>21&&$d['minmaxtemp']['m']>21&&$d['raamliving']['s']=='Closed'&&($d['deurinkom']['s']=='Closed'||past('deurinkom')<600)&&($d['deurgarage']['s']=='Closed'||past('deurgarage')<600)) {
        $Setliving=25;
        if ($d['Weg']['s']==0) {
            if (TIME>=strtotime('5:00')&&TIME<strtotime('21:15')) $Setliving=22.5;
        } 
    }
    if ($d['living_set']['s']!=$Setliving&&past('raamliving')>60&&past('deurinkom')>60&&past('deurgarage')>60) {
        store('living_set', $Setliving, basename(__FILE__).':'.__LINE__);
        $living_set=$Setliving;
        $d['living_set']['s']=$Setliving;
    }
}
$bigdif=100;
$corliving=1.5;
$corkamer=1;
$coralex=1;
foreach (array('living', 'kamer', 'alex') as $k) {
	$corr=${'cor'.$k};
	$set=$d[$k.'_set']['s']+$corr;
	if($set<18)$set=18;
	elseif($set>33)$set=33;
	$set=floor($set*2)/2;
	${'dif'.$k}=number_format(
        $d[$k.'_temp']['s']-$d[$k.'_set']['s'],
        1
    );
    if (${'dif'.$k}<$bigdif) $bigdif=${'dif'.$k};
    $daikin=json_decode($d['daikin'.$k]['s']);
    
//    lg($k.' corr='.$corr.' set='.$set.' temp='.$d[$k.'_temp']['s']);
    
	if ($set<32) {
		if (${'dif'.$k}<0) {
			if ($daikin->stemp!=$set||$daikin->pow!=1||$daikin->f_rate!='B') {
				daikinset($k, 1, 3, $set, basename(__FILE__).':'.__LINE__, 'B');
			}
		} else {
			if ($daikin->stemp!=$set||$daikin->pow!=1||$daikin->f_rate!='A') {
				daikinset($k, 1, 3, $set, basename(__FILE__).':'.__LINE__, 'A');
			}
		}
	} else {
		if ($daikin->pow!=0) {
			daikinset($k, 0, 3, $set, basename(__FILE__).':'.__LINE__);
		}
	}
}

foreach (array('kamer', 'tobi', 'alex') as $k) {
    if (round($d[$k.'Z']['s'], 1)>4) {
        ud($k.'Z', 0, '4.0', basename(__FILE__).':'.__LINE__);
    }
}
foreach (array('kamer', 'tobi', 'alex') as $k) {
    if (round($d[$k.'Z']['s'], 1)>4) {
        ud($k.'Z', 0, '4.0', basename(__FILE__).':'.__LINE__);
    }
}


$boven=array('Rtobi','Ralex','RkamerL','RkamerR');
$beneden=array('Rbureel','RkeukenL','RkeukenR');
$benedenall=array('Rliving','Rbureel','RkeukenL','RkeukenR');
if ($d['minmaxtemp']['s']>20||$d['minmaxtemp']['m']>22) $warm=true; else $warm=false;
if ($d['minmaxtemp']['s']<5&&$d['minmaxtemp']['m']<5) $koud=true; else $koud=false;
if (TIME>=$d['civil_twilight']['s']&&TIME<=$d['civil_twilight']['m']) $dag=true; else $dag=false;
$zon=$d['zon']['s'];
$heating=$d['heating']['s'];
if ($d['auto']['s']=='On') {
	if (TIME>=strtotime('6:00')&&TIME<strtotime('10:15')) {
		$dow=date("w");
		if($dow==0||$dow==6) {
			if ($d['RkamerL']['s']>0&&TIME>=strtotime('7:00')) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
			if ($d['RkamerR']['s']>0&&TIME>=strtotime('7:15')) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
		} else {
			if ($d['RkamerL']['s']>0) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
			if ($d['RkamerR']['s']>0&&TIME>=strtotime('6:15')) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
		}
		if ($dag==true) {
			if ($d['Weg']['s']!=1&&$d['Rtobi']['s']>0&&($d['deurtobi']['s']=='Open'||$d['tobi']['s']>0)) sl('Rtobi', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Weg']['s']!=1&&$d['Ralex']['s']>0&&($d['deuralex']['s']=='Open'||$d['alex']['s']>0)) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
		}
		if ($dag==true&&$zon==0&&$d['Weg']['s']!=1) {
			foreach ($beneden as $i) {
				if ($d[$i]['s']>0) sl($i, 0, basename(__FILE__).':'.__LINE__);
			}
			if ($d['Rliving']['s']>0) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		} elseif ($dag==true&&$zon>0&&$d['Weg']['s']!=1) {
			foreach ($beneden as $i) {
				if ($d[$i]['s']>0) sl($i, 0, basename(__FILE__).':'.__LINE__);
			}
			if ($d['Rliving']['s']>0) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		}
	} 

	elseif (TIME>=strtotime('10:15')&&TIME<strtotime('15:00')) {
		if ($warm) {
			if($zon>2000) {
				if ($d['raamtobi']['s']=='Closed'&&$d[$i]['s']!=81) sl('Rtobi', 81, basename(__FILE__).':'.__LINE__);
				if ($d['raamalex']['s']=='Closed'&&$d[$i]['s']!=81) sl('Ralex', 81, basename(__FILE__).':'.__LINE__);
			}
		} else {
			if ($dag==true) {
				if ($d['RkamerL']['s']>0&&($d['deurkamer']['s']=='Open'||$d['kamer']['s']>0)) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
				if ($d['RkamerR']['s']>0&&($d['deurkamer']['s']=='Open'||$d['kamer']['s']>0)) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
				if ($d['Rtobi']['s']>0&&($d['deurtobi']['s']=='Open'||$d['tobi']['s']>0)) sl('Rtobi', 0, basename(__FILE__).':'.__LINE__);
				if ($d['Ralex']['s']>0&&($d['deuralex']['s']=='Open'||$d['alex']['s']>0)) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
			}
		}
	} 

	elseif (TIME>=strtotime('15:00')&&TIME<strtotime('22:00')) {
		if ($warm) {
			if($zon>2000) {
				if ($d['raamtobi']['s']=='Closed'&&$d['Ralex']['s']!=81) sl('Rtobi', 81, basename(__FILE__).':'.__LINE__);
				if ($d['raamalex']['s']=='Closed'&&$d['Ralex']['s']!=81) sl('Ralex', 81, basename(__FILE__).':'.__LINE__);
				if ($d['Rbureel']['s']<45) sl('Rbureel', 45, basename(__FILE__).':'.__LINE__);
			}
		}
	}

	elseif (TIME>=strtotime('22:00')||TIME<strtotime('6:00')) {
		if ($d['Weg']['s']>0) {
			foreach ($benedenall as $i) {
				if ($d[$i]['s']<84) sl($i, 84, basename(__FILE__).':'.__LINE__);
			}
			foreach ($boven as $i) {
				if ($d[$i]['s']<82) sl($i, 82, basename(__FILE__).':'.__LINE__);
			}
		}
	}
}