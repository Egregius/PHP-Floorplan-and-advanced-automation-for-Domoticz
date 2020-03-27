<?php
/**
 * Pass2PHP Airco heating
 * php version 7.3.11-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/

$Setkamer=4;
if ($d['kamer_set']['m']==0) {
    if ($d['buiten_temp']['s']<14&&$d['minmaxtemp']['m']<15&&$d['deurkamer']['s']=='Closed'&&$d['raamkamer']['s']=='Closed'&&(past('raamkamer')>7198 || TIME>strtotime('21:00'))) {
        $Setkamer=10;
        if (TIME<strtotime('4:00')) $Setkamer=15.0;
        elseif (TIME>strtotime('21:00')) $Setkamer=15.0;
    }
    if ($d['kamer_set']['s']!=$Setkamer) {
        store('kamer_set', $Setkamer, basename(__FILE__).':'.__LINE__);
        $d['kamer_set']['s']=$Setkamer;
    }
}

$Setalex=4;
if ($d['alex_set']['m']==0) {
    if ($d['buiten_temp']['s']<16&&$d['minmaxtemp']['m']<15&&$d['deuralex']['s']=='Closed'&&$d['raamalex']['s']=='Closed'&&(past('raamalex')>1800 || TIME>strtotime('19:00'))) {
        $Setalex=10;
        if (TIME<strtotime('4:30')) $Setalex=15.0;
        elseif (TIME>strtotime('19:00')) $Setalex=15.5;
    }
    if ($d['alex_set']['s']!=$Setalex) {
        ud('alex_set', 0, $Setalex, true, basename(__FILE__).':'.__LINE__);
        $alex_set=$Setalex;
        $d['alex_set']['s']=$Setalex;
    }
}

$Setliving=10;
if ($d['living_set']['m']==0) {
    if ($d['buiten_temp']['s']<20&&$d['minmaxtemp']['m']<20&&$d['raamliving']['s']=='Closed'&&$d['deurinkom']['s']=='Closed'&&$d['deurgarage']['s']=='Closed') {
        $Setliving=16;
        if ($d['Weg']['s']==0) {
            if (TIME>=strtotime('5:00')&&TIME<strtotime('18:15')) $Setliving=20.5;
        } elseif ($d['Weg']['s']==1) {
        	$dow=date("w");
            if($dow==0||$dow==6) {
				if (TIME>=strtotime('7:00')&&TIME<strtotime('12:00')) $Setliving=20;
				elseif (TIME>=strtotime('6:40')&&TIME<strtotime('12:00')) $Setliving=19.5;
				elseif (TIME>=strtotime('6:20')&&TIME<strtotime('12:00')) $Setliving=19.0;
				elseif (TIME>=strtotime('6:00')&&TIME<strtotime('12:00')) $Setliving=18.5;
				elseif (TIME>=strtotime('5:40')&&TIME<strtotime('12:00')) $Setliving=18.0;
				elseif (TIME>=strtotime('5:20')&&TIME<strtotime('12:00')) $Setliving=17.5;
				elseif (TIME>=strtotime('5:00')&&TIME<strtotime('12:00')) $Setliving=17.0;
				elseif (TIME>=strtotime('4:40')&&TIME<strtotime('12:00')) $Setliving=16.5;
			} else {
				if (TIME>=strtotime('6:00')&&TIME<strtotime('12:00')) $Setliving=20;
				elseif (TIME>=strtotime('5:40')&&TIME<strtotime('12:00')) $Setliving=19.5;
				elseif (TIME>=strtotime('5:20')&&TIME<strtotime('12:00')) $Setliving=19.0;
				elseif (TIME>=strtotime('5:00')&&TIME<strtotime('12:00')) $Setliving=18.5;
				elseif (TIME>=strtotime('4:40')&&TIME<strtotime('12:00')) $Setliving=18.0;
				elseif (TIME>=strtotime('4:20')&&TIME<strtotime('12:00')) $Setliving=17.5;
				elseif (TIME>=strtotime('4:00')&&TIME<strtotime('12:00')) $Setliving=17.0;
				elseif (TIME>=strtotime('3:40')&&TIME<strtotime('12:00')) $Setliving=16.5;
			}
        }
        if ($Setliving>19.5) {
            if (TIME>=strtotime('11:00')&&$d['zon']['s']>3000&&$d['buiten_temp']['s']>15) $Setliving=19.5;
            elseif ($d['zon']['s']<2000) $Setliving=20.5;
        }
    }
    if ($d['living_set']['s']!=$Setliving&&past('raamliving')>60&&past('deurinkom')>60&&past('deurgarage')>60) {
        store('living_set', $Setliving, basename(__FILE__).':'.__LINE__);
        $living_set=$Setliving;
        $d['living_set']['s']=$Setliving;
    }
}
foreach (array('living', 'kamer', 'alex') as $k) {
	if (${'Set'.$k}>=${$k.'_temp'}) {
		if (${'daikin'.$k}!=${'Set'.$k}) {
			daikinset($k, 1, 4, ${'Set'.$k});
		}
	} elseif (${'Set'.$k}<${$k.'_temp'}) {
		if (${'daikin'.$k}!=${'Set'.$k}) {
			daikinset($k, 0, 4, ${'Set'.$k});
		}
	}
}

foreach (array('kamer', 'tobi', 'alex') as $k) {
    if (round($d[$k.'Z']['s'], 1)>4) {
        ud($k.'Z', 0, '4.0', basename(__FILE__).':'.__LINE__);
    }
}
//lg('bigdif='.$bigdif.'|brander='.$d['brander']['s'].'|timebrander='.past('brander'));
if ($d['brander']['s']=='On') sw('brander', 'Off', basename(__FILE__).':'.__LINE__);

if ($bigdif!=$d['bigdif']['m']) storemode('bigdif', $bigdif, basename(__FILE__).':'.__LINE__);

if ($d['deurbadkamer']['s']=='Open'&&$d['badkamer_set']['s']!=10&&(past('deurbadkamer')>57|| $d['lichtbadkamer']['s']==0)) {
    store('badkamer_set', 10, basename(__FILE__).':'.__LINE__);
    $d['badkamer_set']['s']=10.0;
    if ($d['badkamer_set']['m']==1) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
} elseif ($d['deurbadkamer']['s']=='Closed'&&$d['badkamer_set']['m']==0) {
    $b7=past('$ 8badkamer-7');
    $b7b=past('8Kamer-7');
    if ($b7b<$b7) $b7=$b7b;
    $x=22.4;
    if ($d['buiten_temp']['s']<21&&$d['lichtbadkamer']['s']>0&&$d['badkamer_set']['s']!=$x&&($b7>900&&$d['heating']['s']>=1&&(TIME>strtotime('5:00')&& TIME<strtotime('7:30')))) {
        store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
        $d['badkamer_set']['s']=$x;
    } elseif ($b7>900&&$d['lichtbadkamer']['s']==0&&$d['buiten_temp']['s']<21&&$d['Weg']['s']<2) {
        if ($d['heating']['s']>1) {
			if ($weekend==false) {
				if (TIME>=strtotime('6:00')&&TIME<=strtotime('6:30')) {
					$x=20;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('5:57')&&TIME<=strtotime('6:30')) {
					$x=19.5;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('5:54')&&TIME<=strtotime('6:30')) {
					$x=19;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('5:51')&&TIME<=strtotime('6:30')) {
					$x=18.5;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('5:48')&&TIME<=strtotime('6:30')) {
					$x=18;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('5:45')&&TIME<=strtotime('6:30')) {
					$x=17.5;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('5:42')&&TIME<=strtotime('6:30')) {
					$x=17;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('5:39')&&TIME<=strtotime('6:30')) {
					$x=16.5;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('5:36')&&TIME<=strtotime('6:30')) {
					$x=16;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('5:33')&&TIME<=strtotime('6:30')) {
					$x=15.5;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('5:30')&&TIME<=strtotime('6:30')) {
					$x=15;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('5:27')&&TIME<=strtotime('6:30')) {
					$x=14.5;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} else {
					$x=10;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				}
			} else {
				if (TIME>=strtotime('7:00')&&TIME<=strtotime('7:30')) {
					$x=20;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('6:57')&&TIME<=strtotime('7:30')) {
					$x=19.5;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('6:54')&&TIME<=strtotime('7:30')) {
					$x=19;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('6:51')&&TIME<=strtotime('7:30')) {
					$x=18.5;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('6:48')&&TIME<=strtotime('7:30')) {
					$x=18;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('6:45')&&TIME<=strtotime('7:30')) {
					$x=17.5;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('6:42')&&TIME<=strtotime('7:30')) {
					$x=17;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('6:39')&&TIME<=strtotime('7:30')) {
					$x=16.5;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('6:36')&&TIME<=strtotime('7:30')) {
					$x=16;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('6:33')&&TIME<=strtotime('7:30')) {
					$x=15.5;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('6:30')&&TIME<=strtotime('7:30')) {
					$x=15;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('6:27')&&TIME<=strtotime('7:30')) {
					$x=14.5;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} else {
					$x=10;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				}
			}
        } elseif ($d['badkamer_set']['s']!=10) {
            store('badkamer_set', 10, basename(__FILE__).':'.__LINE__);
            $d['badkamer_set']['s']=10.0;
        }
    } elseif ($b7>900&&($d['lichtbadkamer']['s']==0&&$d['badkamer_set']['s']!=10)
        || ($d['Weg']['s']==2&&$d['badkamer_set']['s']!=10)) {
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
    if (($d['badkamervuur2']['s']!='Off'&&past('badkamervuur2')>30)
        || $d['el']['s']>7500) {
        sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
    }
} else {
    if (($d['badkamervuur2']['s']!='Off'&&past('badkamervuur2')>30)
        || $d['el']['s']>7500) {
        sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
    }
    if (($d['badkamervuur1']['s']!='Off'&&past('badkamervuur1')>30)
        || $d['el']['s']>8200) {
        sw('badkamervuur1', 'Off', basename(__FILE__).':'.__LINE__);
    }
}
if ($d['minmaxtemp']['m']>19) {
    if ($d['zolder_set']['s']>4) {
        $d['zolder_set']['s']=4;
        store('zolder_set', 4, basename(__FILE__).':'.__LINE__);
    }

}
$difzolder=number_format($d['zolder_temp']['s']-$d['zolder_set']['s'], 1);

if ($d['Weg']['s']==0&&$difzolder<0) {
	lg($difzolder);
	$difheater1=0;
	$difheater2=-2;
	if ($difzolder<=$difheater2&&$d['zoldervuur2']['s']!='On'&&past('zoldervuur2')>90) {
		if ($d['zoldervuur1']['s']!='On') {
			sw('zoldervuur1', 'On', basename(__FILE__).':'.__LINE__);
		}
		sw('zoldervuur2', 'On', basename(__FILE__).':'.__LINE__);
	} elseif ($difzolder<=$difheater1&&$d['zoldervuur1']['s']!='On'&&past('zoldervuur1')>140&&$d['el']['s']<8000) {
		sw('zoldervuur1', 'On', basename(__FILE__).':'.__LINE__);
	} elseif ($difzolder>=$difheater2&&$d['zoldervuur2']['s']!='Off'&&past('zoldervuur2')>110||$d['el']['s']>8500) {
		sw('zoldervuur2', 'Off', basename(__FILE__).':'.__LINE__);
	} elseif ($d['heating']['s']<=0) {//Cooling or neutral
        if ($d['zoldervuur2']['s']!='Off') sw('zoldervuur2', 'Off', basename(__FILE__).':'.__LINE__);
	    if ($d['zoldervuur1']['s']!='Off') sw('zoldervuur1', 'Off', basename(__FILE__).':'.__LINE__);
    }
} else {
    //Niet thuis of slapen
    if ($d['zoldervuur2']['s']!='Off') sw('zoldervuur2', 'Off', basename(__FILE__).':'.__LINE__);
    if ($d['zoldervuur1']['s']!='Off') sw('zoldervuur1', 'Off', basename(__FILE__).':'.__LINE__);
}
/**
 * Function setradiator: calculates the setpoint for the Danfoss thermostat valve
 *
 * @param string  $name   Not used anymore
 * @param int     $dif    Difference in temperature
 * @param boolean $koudst Is it the coldest room of all?
 * @param int     $set    default setpoint
 *
 * @return null
 */
function setradiator($name,$dif,$koudst=false,$set=14)
{
    if ($koudst==true) $setpoint=28;
    else $setpoint=$set-ceil($dif*4);
    if ($setpoint>28) $setpoint=28;
    elseif ($setpoint<4) $setpoint=4;
    return round($setpoint, 0);
}

$boven=array('Rtobi','Ralex','RkamerL','RkamerR');
$beneden=array('Rbureel','RkeukenL','RkeukenR');
$benedenall=array('Rliving','Rbureel','RkeukenL','RkeukenR');
if ($d['minmaxtemp']['s']>20||$d['minmaxtemp']['m']>22) $warm=true; else $warm=false;
if ($d['minmaxtemp']['s']<5&&$d['minmaxtemp']['m']<5) $koud=true; else $koud=false;
if ($d['auto']['m']) $dag=true; else $dag=false;
$zon=$d['zon']['s'];

if ($d['auto']['s']=='On') {
	if (TIME>=strtotime('6:00')&&TIME<strtotime('10:15')) {
		$dow=date("w");
		if($dow==0||$dow==6) {
			if ($d['RkamerL']['s']>0&&TIME>=strtotime('7:00')) {
				sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
				if ($d['RkamerL']['m']>0) {
					storemode('RKamerL', 0, basename(__FILE__).':'.__LINE__);
				}
			}
			if ($d['RkamerR']['s']>0&&TIME>=strtotime('7:15')) {
				sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
				if ($d['RkamerR']['m']>0) {
					storemode('RKamerR', 0, basename(__FILE__).':'.__LINE__);
				}
			}
		} else {
			if ($d['RkamerL']['s']>0) {
				sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
				if ($d['RkamerL']['m']>0) {
					storemode('RKamerL', 0, basename(__FILE__).':'.__LINE__);
				}
			}
			if ($d['RkamerR']['s']>0&&TIME>=strtotime('6:15')) {
				sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
				if ($d['RkamerR']['m']>0) {
					storemode('RKamerR', 0, basename(__FILE__).':'.__LINE__);
				}
			}
		}
		if ($dag==true) {
			if ($d['Weg']['s']!=1&&$d['Rtobi']['m']==0&&$d['Rtobi']['s']>0&&($d['deurtobi']['s']=='Open'||$d['tobi']['s']>0)) sl('Rtobi', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Weg']['s']!=1&&$d['Ralex']['m']==0&&$d['Ralex']['s']>0&&($d['deuralex']['s']=='Open'||$d['alex']['s']>0)) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
		}
		if ($dag==true&&$zon==0&&$d['Weg']['s']!=1) {
			foreach ($beneden as $i) {
				if ($d[$i]['m']==0&&$d[$i]['s']>0) sl($i, 0, basename(__FILE__).':'.__LINE__);
			}
			if ($d['Rliving']['m']==0&&$d['Rliving']['s']>0) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		} elseif ($dag==true&&$zon>0&&$d['Weg']['s']!=1) {
			foreach ($beneden as $i) {
				if ($d[$i]['m']==0&&$d[$i]['s']>0) sl($i, 0, basename(__FILE__).':'.__LINE__);
			}
			if ($d['Rliving']['m']==0&&$d['Rliving']['s']>0) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		}
	} 

	elseif (TIME>=strtotime('15:00')&&TIME<strtotime('17:00')) {
		if ($d['buiten_temp']['s']<16) {
			$items=array('tobi', 'alex');
			foreach ($items as $i) {
				if ($d['raam'.$i]['s']=='Open'&&$d[$i.'_temp']['s']<17&&past('R'.$i)>14400&&past('raam'.$i)>14400&&$d['R'.$i]['s']<100) {
					sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
				}
			}
			$items=array('kamerL', 'kamerR');
			foreach ($items as $i) {
				if ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<17&&past('R'.$i)>14400&&past('raamkamer')>14400&&$d['R'.$i]['s']<100) {
					sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	} 

	elseif (TIME>=strtotime('17:00')&&TIME<strtotime('22:00')) {
		if ($zon==0) {
			foreach ($boven as $i) {
				if ($d[$i]['m']==0&&$d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
			//foreach ($beneden as $i) {
			//	if ($d[$i]['m']==0&&$d[$i]['s']<31) sl($i, 31, basename(__FILE__).':'.__LINE__);
			//}
			if ($d['auto']['m']==false) {
				foreach ($beneden as $i) {
					if ($d[$i]['m']==0&&$d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
				}
			}
		} elseif ($d['buiten_temp']['s']<16) {
			$items=array('tobi', 'alex');
			foreach ($items as $i) {
				if ($d['raam'.$i]['s']=='Open'&&$d[$i.'_temp']['s']<17&&past('R'.$i)>14400&&past('raam'.$i)>14400&&$d['R'.$i]['s']<100) {
					sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
				}
			}
			$items=array('kamerL', 'kamerR');
			foreach ($items as $i) {
				if ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<17&&past('R'.$i)>14400&&past('raamkamer')>14400&&$d['R'.$i]['s']<100) {
					sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	} 

	elseif (TIME>=strtotime('22:00')||TIME<strtotime('6:00')) {
		if ($d['Weg']['s']>0) {
			foreach ($benedenall as $i) {
				if ($d[$i]['m']==0&&$d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
			foreach ($boven as $i) {
				if ($d[$i]['m']==0&&$d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
		}
	}
}