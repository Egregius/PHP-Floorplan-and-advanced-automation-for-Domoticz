<?php
$totalmin = 0;
$weight = ['living'=>1.0,'kamer'=>0.3,'alex'=>0.3];

foreach (array('living','kamer','alex') as $k) {
    ${'dif'.$k} = $d[$k.'_temp']->s - $d[$k.'_set']->s;
    if (${'dif'.$k} < 0) $totalmin += -${'dif'.$k} * $weight[$k];
}
$trend=$d['living_temp']->i;
if($trend>0.15) $maxpow=40;
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
$prevadjLiving??=0;
$adjLiving??=getCache('adjLiving');
$daikinpower=floor($d['daikin']->p/100);
$daikinrunning=$daikinpower>=1?true:false;
//if($daikinrunning!=$prevdaikinrunning||!isset($prevdaikinrunning)) {
//	$prevdaikinrunning=$daikinrunning;
//	$adjLiving=0;lg('🔥 $adjLiving to 0, running cycle');
//}
$prevmsg??='';
$msg='';
$fanspeeds=[
	2=>'B',
	1=>3,
	0=>4,
	-1=>5,
	-2=>6,
	-3=>7
];
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
            if ($prevSet==1||($d['weg']->s==0&&$d['living_set']->m==1&&$dif<=-0.4)) {
            	if($dif>=-0.3) $maxpow=50;
            	elseif($dif>=-0.6) $maxpow=60;
            	elseif($dif>=-0.8) $maxpow=70;
            	elseif($dif>=-1) $maxpow=80;
            	elseif($dif>=-1.2) $maxpow=90;
            	else $maxpow=100;
            	$spmode=1;
            	$diffac=$trendfac=$factor=0;
            	$set=28;
            	$setrounded=$set;
            	$fan=$fanspeeds[clamp(round($dif*10)-2,-3,2)];
            } else {
            	if(past('living_set')>300) {
					if($dif>0.3) $factor = ($daikinrunning) ? $daikinpower/2:abs($dif)/2;
					elseif($dif>0.15) $factor = ($daikinrunning) ? $daikinpower/20:abs($dif)/2;
					elseif($dif<-0.1) $factor = ($daikinrunning) ? abs($dif)/2:10;
					else $factor=0.0001;
					$diffac = (-$dif / 50) * $factor;
					$trendfac = (-$trend / 10) * $factor;
					$change=clamp(($diffac + $trendfac),-0.02,0.04);
					$adjLiving += $change;
					$adjLiving = clamp($adjLiving, -2, 2);
					if($prevadjLiving!=$adjLiving) setCache('adjLiving',$adjLiving);
					$set+=$adjLiving;
				} else {
					$factor=$diffac=$trendfac=$change=$adjLiving=0;
				}
				$setrounded = clamp(ceil($set*2)/2,10,28);
		        $setrounded=min($setrounded, $target+1);
			}
			if ($time>strtotime('18:30') && $d['media']->s=='On') {
				$fan=$fanspeeds[clamp(round($dif*10)+2,0,2)];
				$maxpow=40;
			} else $fan=$fanspeeds[clamp(round($dif*10),-3,2)];

        } elseif ($k=='kamer' || $k=='alex') {
            $set -= 1.5;
            $setrounded = clamp(ceil($set*2)/2,10,28);
	        $setrounded=min($setrounded, $target);
            if (($k=='kamer' && ($time<strtotime('10:00')||$d['weg']->s==1)) ||
                ($k=='alex' && $d['alexslaapt']->s==1)) $fan='B';
        }

		if ($k=='living'&&$target>16) {
			$msg='🔥 set = '.number_format($set,3,',','').' ⇉ ceil = '.number_format($setrounded,1,',','').' ⇉ trend = '.$trend.' factor = '.round($factor,3).' diffac = '.$diffac.' trendfac = '.$trendfac.' change = '.$change.' maxpow='.$maxpow.' daikinpower='.$daikinpower.(isset($line)?'	['.$line.']':'');
			if($msg!=$prevmsg) {
//				lg($msg);
				publishmqtt('d/i',date("G:i:s").'|'.number_format($setrounded,1,',','').'|'.number_format($set,2,',','').'|'.number_format(($diffac + $trendfac),3,',','').'|'.round($factor,3).'|'.$maxpow.' '. $fan);
				$prevmsg=$msg;
				unset($line);
			}
			lgcsv('trend_living', [
				"Living target"=>number_format($target,1,',',''),
				"Living temp"=>number_format($d['living_temp']->s,2,',',''),
				"set"=>number_format($set,3,',',''),
				"setrounded"=>number_format($setrounded,1,',',''),
				"daikinpower"=>$d['daikin']->p,
				"trend"=>$trend,
				"factor"=>round($factor,3),
				"diffac"=>$diffac,
				"trendfac"=>$trendfac,
				"change"=>$change,
				"adjLiving"=>$adjLiving,
				"maxpow"=>$maxpow,
				"fan"=>$fan
			]);
		}
        if ($daikin->$k->power!=$power || $daikin->$k->mode!=4 || $daikin->$k->set!=$setrounded ||
            $daikin->$k->fan!=$fan || $daikin->$k->spmode!=$spmode || $daikin->$k->maxpow != $maxpow ||
            ($power!=0&&$daikin->$k->lastset <= $time-281)) {
            if($power==99&&$setrounded>=18) $power=1;
            if (daikinset($k,$power,4,$setrounded,basename(__FILE__).':'.__LINE__,$fan,$spmode,$maxpow)) {
				$daikin->$k->power = $power;
				$daikin->$k->mode  = 4;
				$daikin->$k->fan   = $fan;
				$daikin->$k->set   = $setrounded;
				$daikin->$k->spmode= $spmode;
				$daikin->$k->lastset=$time;
				$daikin->$k->maxpow = $maxpow;
            }
        }
    } elseif (isset($power) && $power==1 && $d['daikin']->s=='Off' && past('daikin')>900) {
        publishmqtt('d/l',"Daikin On");
        sw('daikin','On');
    }
    unset($power);
}

// --- brander fallback ---
if ($d['brander']->s=='On' && $d['badkamer_temp']->s>10 && past('brander')>=595) sw('brander','Off');
elseif ($d['brander']->s=='Off' && $d['badkamer_temp']->s<10 && past('brander')>=595) sw('brander','On');
