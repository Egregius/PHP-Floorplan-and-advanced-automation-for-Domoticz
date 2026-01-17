<?php
foreach (array('living','badkamer') as $kamer) {
	${'dif'.$kamer}=number_format($d[$kamer.'_temp']->s-$d[$kamer.'_set']->s,1);
	if (${'dif'.$kamer}<$difgas) $difgas=${'dif'.$kamer};
}

$aanna=(1/(21-$d['buiten_temp']->s))*6000; if ($aanna<1000) $aanna=1000;
$uitna=(21-$d['buiten_temp']->s)*60;
if ($uitna<595) $uitna=595;
elseif ($uitna>1795) $uitna=1795;
$pastbrander=past('brander');
//lg('difgas='.$difgas.' pastbrander='.$pastbrander);
if (	$difgas<=-1.8&&$d['brander']->s=="Off"&&$pastbrander>$aanna*0.5&&$d['n']>-500&&$d['buiten_temp']->s<=5) sw('brander','On' , 'difgas = '.$difgas.' Aan na = '.$aanna*0.5);
elseif ($difgas<=-1.5&&$d['brander']->s=="Off"&&$pastbrander>$aanna*0.6&&$d['n']>-500&&$d['buiten_temp']->s<=4) sw('brander','On' , 'difgas = '.$difgas.' Aan na = '.$aanna*0.6);
elseif ($difgas<=-1.2&&$d['brander']->s=="Off"&&$pastbrander>$aanna*0.7&&$d['n']>-500&&$d['buiten_temp']->s<=3) sw('brander','On' , 'difgas = '.$difgas.' Aan na = '.$aanna*0.7);
elseif ($difgas<=-0.9&&$d['brander']->s=="Off"&&$pastbrander>$aanna*0.8&&$d['n']>-500&&$d['buiten_temp']->s<=2) sw('brander','On' , 'difgas = '.$difgas.' Aan na = '.$aanna*0.8);
elseif ($difgas<=-0.6&&$d['brander']->s=="Off"&&$pastbrander>$aanna*0.9&&$d['n']>-500&&$d['buiten_temp']->s<=1) sw('brander','On' , 'difgas = '.$difgas.' Aan na = '.$aanna*0.9);
elseif ($difgas<=-0.4   &&$d['brander']->s=="Off"&&$pastbrander>$aanna    &&$d['n']>-500&&$d['buiten_temp']->s<=0) sw('brander','On' , 'difgas = '.$difgas.' Aan na = '.$aanna);
elseif ($difgas>=0   &&$d['brander']->s=="On" &&$pastbrander>$uitna)     sw('brander','Off', 'Uit na = '.$uitna);
elseif ($difgas>=-0.1&&$d['brander']->s=="On" &&$pastbrander>$uitna*1.5) sw('brander','Off', 'Uit na = '.$uitna*6);
elseif ($difgas>=-0.2 &&$d['brander']->s=="On" &&$pastbrander>$uitna*2)   sw('brander','Off', 'Uit na = '.$uitna*12);

if ($d['daikin']->m==1) {
	$log_file = '/var/www/html/secure/daikin_learn.json';
	$config_file = '/var/www/html/secure/daikin_config.json';
	if (file_exists($config_file)) {
		$config = json_decode(file_get_contents($config_file), true);
		$trend_factor = $config['trend_factor'];
	} else {
		$trend_factor = ['living' => 2, 'kamer' => 2.5, 'alex' => 2.5];
	}
	$totalmin=0;
	foreach (array('living','kamer','alex') as $k) {
		${'dif'.$k}=$d[$k.'_temp']->s-$d[$k.'_set']->s;
		if (${'dif'.$k}<0) $totalmin-=${'dif'.$k};
	}
	if ($d['weg']->s>0) $maxpow=40;
	elseif ($totalmin>=2.5) $maxpow=100;
	elseif ($totalmin>=2.0) $maxpow=90;
	elseif ($totalmin>=1.6) $maxpow=80;
	elseif ($totalmin>=1.2) $maxpow=70;
	elseif ($totalmin>=0.8) $maxpow=60;
	elseif ($totalmin>=0.4) $maxpow=50;
	else $maxpow=40;

//	$daikin->living->fan=4;
	if ($d['n']>3500&&$maxpow>40) $maxpow=40;
	elseif ($d['n']>3000&&$maxpow>60) $maxpow=60;
	elseif ($d['n']>2500&&$maxpow>80) $maxpow=80;
	$pastdaikin=past('daikin');
//	lg(print_r($daikin,true));
	$trend_factor = ['living' => 2, 'kamer' => 2.5, 'alex' => 2.5];
	foreach (array('living', 'kamer', 'alex') as $k) {
		if ($d[$k.'_set']->s>10) {
			$trend = $d[$k.'_temp']->i;
			$target = $d[$k.'_set']->s;
			$dif=$d[$k.'_temp']->s-$target;
			$effective_dif = $dif + ($trend * $trend_factor[$k]);
			if ($dif>2) $power=0;
			elseif ($dif<=0) $power=1;
			else $power=$daikin->$k->power;
			if ($d['daikin']->s=='On') {
				$fan='A';
				if ($k=='living') {
					$log_entry = [
						't' => $time,                           // timestamp
						'a' => round($d[$k.'_temp']->s, 1),    // actual temp
						'tg' => round($target, 1),              // target
						'd' => round($dif, 2),                  // dif
						'tr' => round($trend, 2),               // trend
						'ed' => round($effective_dif, 2),       // effective_dif
						'sb' => $daikin->$k->set,               // set_before
						'sa' => null,                           // set_after (later gevuld)
						'sp' => null,                           // spmode
						'p' => $power,                          // power
						'l' => $line,                           // line
						'tf' => $trend_factor[$k]               // trend_factor
					];
				}
				if ($effective_dif >= 1.5) {$line=__LINE__;
					$set = $target - 1.0;
					$spmode = -1;
				} elseif ($effective_dif >= 0.8) {$line=__LINE__;
					$set = $target - 0.5;
					$spmode = -1;
				} elseif ($effective_dif >= 0.3) {$line=__LINE__;
					$set = $target;
					$spmode = -1;
				} elseif ($effective_dif <= -2.5) {$line=__LINE__;
					$set = $target + 1.5;
					$spmode = 1;
				} elseif ($effective_dif <= -1.5) {$line=__LINE__;
					$set = $target + 1.0;
					$spmode = 0;
				} elseif ($effective_dif <= -0.8) {$line=__LINE__;
					$set = $target + 0.5;
					$spmode = 0;
				} elseif ($effective_dif <= -0.3) {$line=__LINE__;
					$set = $target;
					$spmode = 0;
				} else {$line=__LINE__;
					$set = $target;
					$spmode = -1;
				}
				if ($d['weg']->s>0) $spmode=-1;
				if ($k=='living') {
					if ($prevSet==1) {
						$maxpow=100;$spmode=1;$set+=10;
					} else {
						$set+=-2;
						if ($prevSet==0&&$prevSetTime<$time-1800) {
							$log_entry['sa'] = $set;
							$log_entry['sp'] = $spmode;
							file_put_contents($log_file, json_encode($log_entry) . "\n", FILE_APPEND);
						}
					}
					if ($time>strtotime('19:00')&&$d['media']->s=='On') $fan='B';
//					lg(sprintf("DAIKIN %s: dif=%.2f trend=%.2f eff_dif=%.2f set=%.1f->%.1f spmode=%d line=%d", $k, $dif, $trend, $effective_dif, $target, $set, $spmode, $line));
				} elseif ($k=='kamer') {
					$set+=-1.5;
					if ($time<strtotime('10:00')||$d['weg']->s==1) $fan='B';
				} elseif ($k=='alex') {
					$set+=-1.5;
					if ($d['alexslaapt']->s==1) $fan='B';
				}
				$set=ceil($set * 2) / 2;
				if ($set>28) $set=28;
				elseif ($set<10) $set=10;
				if ($daikin->$k->power!=$power||$daikin->$k->mode!=4||$daikin->$k->set!=$set||$daikin->$k->fan!=$fan||$daikin->$k->spmode!=$spmode||$daikin->$k->maxpow!=$maxpow||$daikin->$k->lastset<=$time-581) {
					if(daikinset($k, $power, 4, $set, basename(__FILE__).':'.__LINE__, $fan, $spmode, $maxpow)) {
						$daikin->$k->power=$power;
						$daikin->$k->mode=4;
						$daikin->$k->fan=$fan;
						$daikin->$k->set=$set;
						$daikin->$k->spmode=$spmode;
						$daikin->$k->maxpow=$maxpow;
						$daikin->$k->lastset=$time;
					}
				}
			} elseif (isset($power)&&$power==1&&$d['daikin']->s=='Off'&&$pastdaikin>900) sw('daikin', 'On');
		} else {
			if ($d['daikin']->s=='On'&&$pastdaikin>70) {
				if ($daikin->$k->power!=0||$daikin->$k->mode!=4) {
					if(daikinset($k, 0, 4, 10, basename(__FILE__).':'.__LINE__, 'A', -1, $maxpow)) {
						$daikin->$k->power=0;
						$daikin->$k->mode=4;
					}
				}
			}
		}
		unset($power);
	}

//	unset($daikin);
}
if ($difgas>=0&&$d['brander']->s=='On'&&$d['badkamer_temp']->s>12&&past('brander')>=595) sw('brander', 'Off');
elseif ($d['brander']->s=='Off'&&$d['badkamer_temp']->s<12&&past('brander')>=595) sw('brander', 'On');
