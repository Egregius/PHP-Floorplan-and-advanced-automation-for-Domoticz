<?php
foreach (array('living','badkamer') as $kamer) {
    ${'dif'.$kamer}=number_format($d[$kamer.'_temp']->s-$d[$kamer.'_set']->s,1);
    if (${'dif'.$kamer}<$difgas) $difgas=${'dif'.$kamer};
}

$aanna = (1/(21-$d['buiten_temp']->s))*6000; if ($aanna<1000) $aanna=1000;
$uitna  = (21-$d['buiten_temp']->s)*60; if ($uitna<595) $uitna=595; elseif ($uitna>1795) $uitna=1795;
$pastbrander = past('brander');

if ($difgas <= -1.8 && $d['brander']->s=="Off" && $pastbrander>$aanna*0.5 && $d['n']>-500 && $d['buiten_temp']->s<=5) sw('brander','On','difgas='.$difgas);
elseif ($difgas <= -1.5 && $d['brander']->s=="Off" && $pastbrander>$aanna*0.6 && $d['n']>-500 && $d['buiten_temp']->s<=4) sw('brander','On','difgas='.$difgas);
elseif ($difgas <= -1.2 && $d['brander']->s=="Off" && $pastbrander>$aanna*0.7 && $d['n']>-500 && $d['buiten_temp']->s<=3) sw('brander','On','difgas='.$difgas);
elseif ($difgas <= -0.9 && $d['brander']->s=="Off" && $pastbrander>$aanna*0.8 && $d['n']>-500 && $d['buiten_temp']->s<=2) sw('brander','On','difgas='.$difgas);
elseif ($difgas <= -0.6 && $d['brander']->s=="Off" && $pastbrander>$aanna*0.9 && $d['n']>-500 && $d['buiten_temp']->s<=1) sw('brander','On','difgas='.$difgas);
elseif ($difgas <= -0.4 && $d['brander']->s=="Off" && $pastbrander>$aanna && $d['n']>-500 && $d['buiten_temp']->s<=0) sw('brander','On','difgas='.$difgas);
elseif ($difgas >= 0 && $d['brander']->s=="On" && $pastbrander>$uitna) sw('brander','Off');
elseif ($difgas >= -0.1 && $d['brander']->s=="On" && $pastbrander>$uitna*1.5) sw('brander','Off');
elseif ($difgas >= -0.2 && $d['brander']->s=="On" && $pastbrander>$uitna*2) sw('brander','Off');

$totalmin = 0;
$weight = ['living'=>1.0,'kamer'=>0.3,'alex'=>0.3];

foreach (array('living','kamer','alex') as $k) {
    ${'dif'.$k} = $d[$k.'_temp']->s - $d[$k.'_set']->s;
    if (${'dif'.$k} < 0) $totalmin += -${'dif'.$k} * $weight[$k];
}
$trend=$d['living_temp']->i;
if($trend>=0) $maxpow=40;
else {
	$maxpow = 40;
	if ($d['weg']->s > 0)            $maxpow = 40;
	elseif ($totalmin >= 1.5)        $maxpow = 100;
	elseif ($totalmin >= 1.3)        $maxpow = 90;
	elseif ($totalmin >= 1.1)        $maxpow = 80;
	elseif ($totalmin >= 0.9)        $maxpow = 70;
	elseif ($totalmin >= 0.7)        $maxpow = 60;
	elseif ($totalmin >= 0.5)        $maxpow = 50;

	if ($d['n'] > 3500 && $maxpow > 40)      $maxpow = 40;
	elseif ($d['n'] > 3000 && $maxpow > 60)  $maxpow = 60;
	elseif ($d['n'] > 2500 && $maxpow > 80)  $maxpow = 80;
}
$adjLiving??=0;
$daikinpower=floor($d['daikin']->p/100);
$daikinrunning=$daikinpower>=1?true:false;
//if($daikinrunning!=$prevdaikinrunning||!isset($prevdaikinrunning)) {
//	$prevdaikinrunning=$daikinrunning;
//	$adjLiving=0;lg('🔥 $adjLiving to 0, running cycle');
//}
$prevmsg??='';
$msg='';
foreach (array('living','kamer','alex') as $k) {
    $set = $d[$k.'_set']->s;
    $target=$set;
    $dif = $d[$k.'_temp']->s - $set;
    if ($dif > 2) $power = 0;
    elseif ($dif <= 0) $power = 1;
    else $power = $daikin->$k->power;
    if ($d['daikin']->s=='On') {
        $fan=7; // A=auto	B=silence	3=lvl_1	4=lvl_2	5=lvl_3	6=lvl_4	7=lvl_5
        $spmode=-1;
        if ($dif<-2) $spmode=1;
        elseif ($dif<-1) $spmode=0;
        if ($k=='living') {
            if ($prevSet==1) {
            	$maxpow=100;
            	$spmode=1;
            	$set=28;
            	$fan=7;
            } else {
				if($dif==0) $line='NO DIF';
				elseif($dif<0) {
					if($daikinrunning) {
						if($trend>=8){		$line=__LINE__;$adjLiving-=0.05;}
						elseif($trend>=6){	$line=__LINE__;$adjLiving-=0.04;}
						elseif($trend>=4){	$line=__LINE__;$adjLiving-=0.03;}
						elseif($trend>=2){	$line=__LINE__;$adjLiving-=0.02;}
						elseif($trend>0){	$line=__LINE__;$adjLiving-=0.01;}
						elseif($trend==0){	$line=__LINE__;$adjLiving+=0.005;}
						elseif($trend<=-8){	$line=__LINE__;$adjLiving+=0.06;}
						elseif($trend<=-6){	$line=__LINE__;$adjLiving+=0.05;}
						elseif($trend<=-4){	$line=__LINE__;$adjLiving+=0.04;}
						elseif($trend<=-2){	$line=__LINE__;$adjLiving+=0.03;}
						elseif($trend<0){	$line=__LINE__;$adjLiving+=0.02;}
						else $line=__LINE__;
					} else {
						if($trend>=8){		$line=__LINE__;$adjLiving-=0.05;}
						elseif($trend>=6){	$line=__LINE__;$adjLiving-=0.04;}
						elseif($trend>=4){	$line=__LINE__;$adjLiving-=0.03;}
						elseif($trend>=2){	$line=__LINE__;$adjLiving-=0.02;}
						elseif($trend>0){	$line=__LINE__;$adjLiving-=0.005;}
						elseif($trend==0){	$line=__LINE__;$adjLiving+=0.01;}
						elseif($trend<=-8){	$line=__LINE__;$adjLiving+=0.05;}
						elseif($trend<=-6){	$line=__LINE__;$adjLiving+=0.04;}
						elseif($trend<=-4){	$line=__LINE__;$adjLiving+=0.03;}
						elseif($trend<=-2){	$line=__LINE__;$adjLiving+=0.02;}
						elseif($trend<0){	$line=__LINE__;$adjLiving+=0.01;}
						else $line=__LINE__;
					}
				} elseif($dif>0) {
					if($daikinrunning) {
						if($trend>=8){		$line=__LINE__;$adjLiving-=0.06;}
						elseif($trend>=6){	$line=__LINE__;$adjLiving-=0.05;}
						elseif($trend>=4){	$line=__LINE__;$adjLiving-=0.04;}
						elseif($trend>=2){	$line=__LINE__;$adjLiving-=0.03;}
						elseif($trend>0){	$line=__LINE__;$adjLiving-=0.02;}
						elseif($trend==0){	$line=__LINE__;$adjLiving-=0.01;}
						elseif($trend<=-8){	$line=__LINE__;$adjLiving+=0.05;}
						elseif($trend<=-6){	$line=__LINE__;$adjLiving+=0.04;}
						elseif($trend<=-4){	$line=__LINE__;$adjLiving+=0.03;}
						elseif($trend<=-2){	$line=__LINE__;$adjLiving+=0.02;}
						elseif($trend<0){	$line=__LINE__;$adjLiving+=0.01;}
						else $line=__LINE__;
					} else {
						if($trend>=8){		$line=__LINE__;$adjLiving-=0.05;}
						elseif($trend>=6){	$line=__LINE__;$adjLiving-=0.04;}
						elseif($trend>=4){	$line=__LINE__;$adjLiving-=0.03;}
						elseif($trend>=2){	$line=__LINE__;$adjLiving-=0.02;}
						elseif($trend>0){	$line=__LINE__;$adjLiving-=0.01;}
						elseif($trend==0){	$line=__LINE__;$adjLiving+=0.01;}
						elseif($trend<=-8){	$line=__LINE__;$adjLiving+=0.05;}
						elseif($trend<=-6){	$line=__LINE__;$adjLiving+=0.04;}
						elseif($trend<=-4){	$line=__LINE__;$adjLiving+=0.03;}
						elseif($trend<=-2){	$line=__LINE__;$adjLiving+=0.02;}
						elseif($trend<0){	$line=__LINE__;$adjLiving+=0.01;}
						else $line=__LINE__;
					}
				}
				$adjLiving = clamp($adjLiving, -2, 2);
				$set+=$adjLiving-1;
			}
			if ($time>strtotime('19:00') && $d['media']->s=='On') $fan='B';
        } elseif ($k=='kamer' || $k=='alex') {
            $set -= 1.5;
            if (($k=='kamer' && ($time<strtotime('10:00')||$d['weg']->s==1)) ||
                ($k=='alex' && $d['alexslaapt']->s==1)) $fan='B';
        }
        $setrounded = clamp(ceil($set*2)/2,10,28);
        $setrounded=min($setrounded, $target);
		if ($k=='living'&&past('living_set')>1800) {
			$msg='🔥 set = '.number_format($set,3,',','').' ⇉ ceil = '.number_format($setrounded,3,',','').' ⇉ adj = '.number_format($adjLiving,3,',','').'  ⇉ trend = '.$trend.(isset($line)?'	['.$line.']':'');
//			if($msg!=$prevmsg) {
				lg($msg);
				$prevmsg=$msg;
				unset($line);
//			}
			lgcsv('trend_living', [
				"Living target"=>number_format($target,1,',',''),
				"Living temp"=>number_format($d['living_temp']->s,1,',',''),
				"dif"=>number_format($dif,1,',',''),
				"trend"=>$d['living_temp']->i,
				"adjLiving"=>number_format($adjLiving,3,',',''),
				"set"=>number_format($set,3,',',''),
				"setrounded"=>number_format($setrounded,1,',',''),
				"spm"=>$spm[$spmode],
				"maxpow"=>$maxpow,
				"daikinpower"=>$d['daikin']->p,
				"buiten temp"=>number_format($d['buiten_temp']->s,1,',',''),
			]);
		}
        if ($daikin->$k->power!=$power || $daikin->$k->mode!=4 || $daikin->$k->set!=$setrounded ||
            $daikin->$k->fan!=$fan || $daikin->$k->spmode!=$spmode || $daikin->$k->maxpow != $maxpow ||
            ($power!=0&&$daikin->$k->lastset <= $time-581)) {
            if($power==99&&$setrounded>=18) $power=1;
            if (daikinset($k,$power,4,$setrounded,basename(__FILE__).':'.__LINE__,$fan,$spmode,$maxpow)) {
				$daikin->$k->power = $power;
				$daikin->$k->mode  = 4;
				$daikin->$k->fan   = $fan;
				$daikin->$k->set   = $setrounded;
				$daikin->$k->spmode= $spmode;
				$daikin->$k->lastset=$time;
				$daikin->$k->maxpow = $maxpow;
//				publishmqtt('d/l',"Daikin {$setrounded} {$adjLiving}");
            }
        }
    } elseif (isset($power) && $power==1 && $d['daikin']->s=='Off' && past('daikin')>900) {
        publishmqtt('d/l',"Daikin On");
        sw('daikin','On');
    }
    unset($power);
}

// --- brander fallback ---
if ($difgas>=0 && $d['brander']->s=='On' && $d['badkamer_temp']->s>12 && past('brander')>=595) sw('brander','Off');
elseif ($d['brander']->s=='Off' && $d['badkamer_temp']->s<12 && past('brander')>=595) sw('brander','On');
