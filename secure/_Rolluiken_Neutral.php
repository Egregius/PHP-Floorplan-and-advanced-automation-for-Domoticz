<?php
$user=basename(__FILE__);
$zonelevatie=-6.2;
$boven=array('rwaskamer','ralex','rkamerl','rkamerr');
$beneden=array('rbureel','rkeukenl','rkeukenr');
$benedenall=array('rliving','rbureel','rkeukenl','rkeukenr');
if ($d['auto']->s=='On') {
	if (($time>=$t||($d['weg']->s==0&&$d['dag']->s>=$zonelevatie))&&$time>strtotime('5:00')&&$time<strtotime('8:30')) {
		if ($time>=$t) {
			if ($d['rkamerl']->s>0) sl('rkamerl', 0, basename(__FILE__).':'.__LINE__);
			if ($d['rkamerr']->s>0) sl('rkamerr', 0, basename(__FILE__).':'.__LINE__);
			if ($d['ralex']->s==0&&$d['rwaskamer']->s>0) sl('rwaskamer', 0, basename(__FILE__).':'.__LINE__);
			if ($d['ralex']->s>0&&$d['alexslaapt']->s==0) sl('ralex', 0, basename(__FILE__).':'.__LINE__);
			if ($time>=$t+1800&&$weekend==false&&$d['verlof']->s==0) {
				if ($d['ralex']->s>0) sl('ralex', 0, basename(__FILE__).':'.__LINE__);
			}
		}
		if ($d['dag']->s>=$zonelevatie) {
			if ($d['lgtv']->s!='On') {
				foreach ($benedenall as $i) {
					if ($d[$i]->s>0&&past($i)>7200) sl($i, 0, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
	elseif ($time>=strtotime('18:00')&&$time<strtotime('22:00')) {
		if ($d['dag']->s<-5) {
			foreach (array('waskamer', 'alex') as $i) if ($d['raam'.$i]->s=='Open'&&$d[$i.'_temp']->s<14&&$d['r'.$i]->s<50) sl('r'.$i, 100);
			foreach (array('kamerl', 'kamerr') as $i) if ($d['raamkamer']->s=='Open'&&$d['kamer_temp']->s<14&&$d['r'.$i]->s<50) sl('r'.$i, 100);
			if ($d['weg']->s>=1) {
				foreach (array('rliving','rbureel','rkeukenl','rkeukenr') as $i) if ($d[$i]->s<50) sl($i, 100);
			} else {
				foreach (array('rbureel','rkeukenl','rkeukenr') as $i) if ($d[$i]->s<50) {
					sl($i, 100);
				}
			}
		} elseif ($d['dag']->s<0) {
			foreach (array('waskamer', 'alex') as $i) if ($d['raam'.$i]->s=='Open'&&$d[$i.'_temp']->s<14&&$d['r'.$i]->s<50) sl('r'.$i, 100);
			foreach (array('kamerl', 'kamerr') as $i) if ($d['raamkamer']->s=='Open'&&$d['kamer_temp']->s<14&&$d['r'.$i]->s<50) sl('r'.$i, 100);
			if ($d['weg']->s>=1) {
				foreach (array('rliving','rbureel','rkeukenl','rkeukenr') as $i) if ($d[$i]->s<50) sl($i, 100);
			}
		} elseif ($d['buiten_temp']->s<3) {
			foreach (array('waskamer', 'alex') as $i) if ($d['raam'.$i]->s=='Open'&&$d[$i.'_temp']->s<15&&$d['r'.$i]->s<50) sl('r'.$i, 100);
			foreach (array('kamerl', 'kamerr') as $i) if ($d['raamkamer']->s=='Open'&&$d['kamer_temp']->s<15&&$d['r'.$i]->s<50) sl('r'.$i, 100);
		}
	}
	elseif ($time>=strtotime('22:00')) {
		if ($d['weg']->s>0&&$d['dag']->s<0) {
			foreach (array('rliving','rbureel','rkeukenl','rkeukenr') as $i) if ($d[$i]->s<50) sl($i, 100);
			foreach (array('waskamer', 'alex') as $i) if ($d['raam'.$i]->s=='Open'&&$d[$i.'_temp']->s<15&&$d['r'.$i]->s<50) sl('r'.$i, 100);
			foreach (array('kamerl', 'kamerr') as $i) if ($d['raamkamer']->s=='Open'&&$d['kamer_temp']->s<15&&$d['r'.$i]->s<50) sl('r'.$i, 100);
		} elseif ($d['dag']->s<0) {
			foreach (array('rbureel','rkeukenl','rkeukenr') as $i) if ($d[$i]->s<50) sl($i, 100);
			foreach (array('waskamer', 'alex') as $i) if ($d['raam'.$i]->s=='Open'&&$d[$i.'_temp']->s<15&&$d['r'.$i]->s<50) sl('r'.$i, 100);
			foreach (array('kamerl', 'kamerr') as $i) if ($d['raamkamer']->s=='Open'&&$d['kamer_temp']->s<15&&$d['r'.$i]->s<50) sl('r'.$i, 100);
		}
	}
}