<?php
// --- bereken verschil gas voor brander ---
foreach (array('living','badkamer') as $kamer) {
    ${'dif'.$kamer}=number_format($d[$kamer.'_temp']->s-$d[$kamer.'_set']->s,1);
    if (${'dif'.$kamer}<$difgas) $difgas=${'dif'.$kamer};
}

// --- bereken aan/uit tijden brander ---
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
elseif ($totalmin >= 2.4)        $maxpow = 100;
elseif ($totalmin >= 2.0)        $maxpow = 90;
elseif ($totalmin >= 1.6)        $maxpow = 80;
elseif ($totalmin >= 1.2)        $maxpow = 70;
elseif ($totalmin >= 0.8)        $maxpow = 60;
elseif ($totalmin >= 0.4)        $maxpow = 50;
if ($d['n'] > 3500 && $maxpow > 40)      $maxpow = 40;
elseif ($d['n'] > 3000 && $maxpow > 60)  $maxpow = 60;
elseif ($d['n'] > 2500 && $maxpow > 80)  $maxpow = 80;

foreach (array('living','kamer','alex') as $k) {
    $set = $d[$k.'_set']->s;
    $target=$set;
    $dif = $d[$k.'_temp']->s - $set;
    $trend = $d[$k.'_temp']->i;
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
			} else {
				$k_factor = 0.8;
				$trend_factor = 2.2;
				$scale = 1.0;
				if ($dif > 1.5) $scale = 1.5;
				elseif ($dif > 1.0) $scale = 1.2;
				$trend = 0.0;
				$stmt = $db->query("
					SELECT stamp, living FROM temp
					WHERE stamp >= NOW() - INTERVAL 10 MINUTE
					ORDER BY stamp ASC
				");
				$points = [];
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$points[] = [
						't' => strtotime($row['stamp']),
						'v' => (float)$row['living']
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
					$trend = ($n*$sumXY - $sumX*$sumY) / ($n*$sumX2 - $sumX*$sumX);
				}
				if ($dif > 0) $adj = -($dif * 0.8 + max(0, $trend*600) * 4) * $scale;
				else $adj = -($dif * 0.4 + min(0, $trend*600) * 2) * $scale;
				$adj = clamp($adj, -1.5, 0.6);
				$elapsed=$time-$lastSetLiving;
				$stepPerLoop = 0.1;
				$step = $stepPerLoop * max(1, $elapsed / 30);
				if ($adj > 0) $accumAdjLiving += min($adj, $step);
				else $accumAdjLiving += max($adj, -$step);
				$set += $accumAdjLiving;
				$set -= 1;
				$set = min($set, $target);
			}
			if ($time>strtotime('19:00') && $d['media']->s=='On') $fan='B';
		}
 		elseif ($k=='kamer' || $k=='alex') {
            $set -= 1.5;
            if (($k=='kamer' && ($time<strtotime('10:00')||$d['weg']->s==1)) ||
                ($k=='alex' && $d['alexslaapt']->s==1)) $fan='B';
        }
        $setrounded = clamp(ceil($set*2)/2, 10, 28);

		/* --- wijzigingsdetectie --- */
		$setChanged = ($daikin->$k->set != $setrounded);

		$stateChanged =
			$daikin->$k->power  != $power  ||
			$daikin->$k->fan    != $fan    ||
			$daikin->$k->spmode != $spmode ||
			$daikin->$k->maxpow != $maxpow;

		/* set-rate-limit: alleen living */
		$setAllowed = true;
		if ($k === 'living' && $setChanged) {
			$setAllowed = ($time - $lastSetLiving >= 150); // 2,5 min
		}

		if ($k=='living'&&past('living_set')>3600) {
			lgcsv('trend_living', [
				"Living target"=>number_format($target,1,',',''),
				"Living temp"=>number_format($d['living_temp']->s,1,',',''),
				"dif"=>number_format($dif,1,',',''),
				"trend"=>number_format($trend,6,',',''),
				"trend_10min"=>number_format($trend*600,6,',',''),
				"adj"=>number_format($adj,3,',',''),
				"accumAdjLiving"=>number_format($accumAdjLiving,3,',',''),
				"scale"=>number_format($scale,1,',',''),
				"step"=>number_format($step,1,',',''),
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
				"daikinset"=>(
					($setChanged && $setAllowed) ? 'SET' :
					($stateChanged ? 'STATE' : '')
				),

			]);
		}
        if (
			($setChanged && $setAllowed) ||
			$stateChanged
		) {
            if (daikinset($k,$power,4,$setrounded,basename(__FILE__).':'.__LINE__,$fan,$spmode,$maxpow)) {
                $daikin->$k->power = $power;
                $daikin->$k->mode  = 4;
                $daikin->$k->fan   = $fan;
                $daikin->$k->set   = $setrounded;
                $daikin->$k->spmode= $spmode;
                $daikin->$k->maxpow = $maxpow;
                $daikin->$k->lastset=$time;
                publishmqtt('d/l',"Daikin {$set} {$spm[$spmode]} {$maxpow}");
                if ($k === 'living' && $setChanged && $setAllowed) {
					$lastSetLiving   = $time;
					$accumAdjLiving  = 0;
				}
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
