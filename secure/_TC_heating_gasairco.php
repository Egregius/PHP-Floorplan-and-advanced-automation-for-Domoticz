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

$maxpow = 40;
if ($d['weg']->s > 0)            $maxpow = 40;
elseif ($totalmin >= 1.4)        $maxpow = 100;
elseif ($totalmin >= 1.2)        $maxpow = 90;
elseif ($totalmin >= 1.0)        $maxpow = 80;
elseif ($totalmin >= 0.8)        $maxpow = 70;
elseif ($totalmin >= 0.6)        $maxpow = 60;
elseif ($totalmin >= 0.4)        $maxpow = 50;

if ($d['n'] > 3500 && $maxpow > 40)      $maxpow = 40;
elseif ($d['n'] > 3000 && $maxpow > 60)  $maxpow = 60;
elseif ($d['n'] > 2500 && $maxpow > 80)  $maxpow = 80;
$adjLiving??=0;
$daikinrunning=$d['daikin']->p>100?true:false;
if($daikinrunning!=$prevdaikinrunning) {
	$prevdaikinrunning=$daikinrunning;
//	$adjLiving=0;
}
foreach (array('living','kamer','alex') as $k) {
    $set = $d[$k.'_set']->s;
    $target=$set;
    $dif = $d[$k.'_temp']->s - $set;
    if ($dif > 2) $power = 0;
    elseif ($dif <= 0) $power = 1;
    else $power = $daikin->$k->power;
    if ($d['daikin']->s=='On') {
        $fan='A';
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
				$k_factor = 0.4;
				$trend_factor = 1.5;
				$scale = 1.0;
				if ($dif > 1.0) $scale*=$dif;
				elseif ($dif < -1.0) $scale*=-$dif;
				$trend = 0.0;
				$stmt = $db->query("
					SELECT stamp, living FROM temp
					WHERE stamp >= NOW() - INTERVAL 10 MINUTE
					ORDER BY stamp ASC
				");
				$points = [];
				while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
					$points[] = [
						't' => strtotime($row[0]),
						'v' => (float)$row[1]
					];
				}
				if (count($points) > 1) {
					$n = count($points);
					$sumX = $sumY = $sumXY = $sumX2 = 0;
					foreach ($points as $p) {
						$x = $p['t'];
						$y = $p['v'];
						$sumX += $x;
						$sumY += $y;
						$sumXY += $x*$y;
						$sumX2 += $x*$x;
					}
					$trend = number_format((($n*$sumXY - $sumX*$sumY) / ($n*$sumX2 - $sumX*$sumX))*600,7);
				}
				if ($dif > 0) $adj = -($dif * $k_factor + max(0, $trend) * $trend_factor ) * $scale;
				else $adj = -($dif * $k_factor + min(0, $trend) * $trend_factor) * $scale;
				$adj = clamp($adj, -1.5, 0.6);
				//$set+=$adj;
				if($dif<-0.2&&!$daikinrunning&&$d['living_temp']->i<0)$adjLiving+=0.1;
				elseif($dif>=0.2&&$daikinrunning&&$d['living_temp']->i>0)$adjLiving-=0.1;
				else $adjLiving=0;
				$set+=$adjLiving-1;
				$set=min($set, $target);
				lg('$adjLiving='.$adjLiving);
			}
			if ($time>strtotime('19:00') && $d['media']->s=='On') $fan='B';
        } elseif ($k=='kamer' || $k=='alex') {
            $set -= 1.5;
            if (($k=='kamer' && ($time<strtotime('10:00')||$d['weg']->s==1)) ||
                ($k=='alex' && $d['alexslaapt']->s==1)) $fan='B';
        }
        $setrounded = clamp(round($set*2)/2,10,28);
		if ($k=='living'&&past('living_set')>3600) {
//			lg("set=$set	rounded=$setrounded	dif=$dif	trend=".$trend ."	adj=$adj	difk=".$dif * $k_factor." trendf=".$trend*$trend_factor);
			lgcsv('trend_living', [
				"Living target"=>number_format($target,1,',',''),
				"Living temp"=>number_format($d['living_temp']->s,1,',',''),
				"dif"=>number_format($dif,1,',',''),
				"trend"=>number_format($trend,6,',',''),
				"adj"=>number_format($adj,3,',',''),
				"adjLiving"=>number_format($adjLiving,1,',',''),
				"scale"=>number_format($scale,1,',',''),
				"trend_factor"=>number_format($trend_factor,1,',',''),
				"k_factor"=>number_format($k_factor,1,',',''),
				"set"=>number_format($set,1,',',''),
				"setrounded"=>number_format($setrounded,1,',',''),
				"spm"=>$spm[$spmode],
				"maxpow"=>$maxpow,
				"zon"=>$d['z'],
				"daikinpower"=>$d['daikin']->p,
				"fan"=>$fan,
				"buiten temp"=>number_format($d['buiten_temp']->s,1,',',''),
				"daikinset"=>($daikin->$k->power!=$power || $daikin->$k->mode!=4 || $daikin->$k->set!=$set || $daikin->$k->fan!=$fan || $daikin->$k->spmode!=$spmode  || $daikin->$k->maxpow != $maxpow?'SET':''),
			]);
		}
        if ($daikin->$k->power!=$power || $daikin->$k->mode!=4 || $daikin->$k->set!=$setrounded ||
            $daikin->$k->fan!=$fan || $daikin->$k->spmode!=$spmode || $daikin->$k->maxpow != $maxpow ||
            ($power!=0&&$daikin->$k->lastset <= $time-581)) {
            if($power==99&&$setrounded>=18) $power=1;
            if (daikinset($k,$power,4,$setrounded,basename(__FILE__).':'.__LINE__,$fan,$spmode,$maxpow)) {
//				if($daikin->$k->set!=$setrounded) $adjLiving=0;
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
