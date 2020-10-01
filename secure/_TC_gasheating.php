<?php
/**
 * Pass2PHP Temperature Control Gas heating
 * php version 7.3
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/

$Setkamer=4;
if ($d['kamer_set']['m']==0) {
    if ($d['buiten_temp']['s']<10&&$d['minmaxtemp']['m']<10&&($d['deurkamer']['s']=='Closed'||($d['deurkamer']['s']=='Open'&&past('deurkamer')<600))&&$d['raamkamer']['s']=='Closed'&&$d['heating']['s']>=1&&(past('raamkamer')>7198 || TIME>strtotime('21:00'))) {
        if (TIME<strtotime('4:30')||TIME>strtotime('21:00')) $Setkamer=10;
    }
    if ($d['kamer_set']['s']!=$Setkamer) {
        store('kamer_set', $Setkamer, basename(__FILE__).':'.__LINE__);
        $d['kamer_set']['s']=$Setkamer;
    }
}

$Settobi=4;
if ($d['tobi_set']['m']==0) {
    if ($d['buiten_temp']['s']<14&&$d['minmaxtemp']['m']<15&&($d['deurtobi']['s']=='Closed'||($d['deurtobi']['s']=='Open'&&past('deurtobi')<600))&&$d['raamtobi']['s']=='Closed'&&$d['heating']['s']>=1&&(past('raamtobi')>7198 || TIME>strtotime('20:00'))) {
        $Settobi=10;
        if ($d['gcal']['s']) {
            if (TIME<strtotime('4:30')||TIME>strtotime('21:00')) $Settobi=14;
        }
    }
    if ($d['tobi_set']['s']!=$Settobi) {
        store('tobi_set', $Settobi, true, basename(__FILE__).':'.__LINE__);
        $tobi_set=$Settobi;
        $d['tobi_set']['s']=$Settobi;
    }
}

$Setalex=4;
if ($d['alex_set']['m']==0) {
    if ($d['buiten_temp']['s']<16&&$d['minmaxtemp']['m']<15&&($d['deuralex']['s']=='Closed'||($d['deuralex']['s']=='Open'&&past('deuralex')<600))&&$d['raamalex']['s']=='Closed'&&$d['heating']['s']>=1&&(past('raamalex')>1800 || TIME>strtotime('19:00'))) {
        $Setalex=10;
        if (TIME<strtotime('4:30')||TIME>strtotime('19:00')) $Setalex=14;
    }
    if ($d['alex_set']['s']!=$Setalex) {
        ud('alex_set', 0, $Setalex, true, basename(__FILE__).':'.__LINE__);
        $alex_set=$Setalex;
        $d['alex_set']['s']=$Setalex;
    }
}

$Setliving=10;
if ($d['living_set']['m']==0) {
    if ($d['buiten_temp']['s']<20&&$d['minmaxtemp']['m']<20&&$d['heating']['s']>=1&&$d['raamliving']['s']=='Closed'&&$d['deurinkom']['s']=='Closed'&&$d['deurgarage']['s']=='Closed') {
        $Setliving=16;
        if ($d['Weg']['s']==0) {
            if (TIME>=strtotime('5:00')&&TIME<strtotime('18:15')) $Setliving=20.5;
        } elseif ($d['Weg']['s']==1) {
        	$dow=date("w");
            if($dow==0||$dow==6) {
				if (TIME>=strtotime('7:00')&&TIME<strtotime('12:00')) $Setliving=19.0;
				elseif (TIME>=strtotime('6:40')&&TIME<strtotime('12:00')) $Setliving=18.5;
				elseif (TIME>=strtotime('6:20')&&TIME<strtotime('12:00')) $Setliving=18.0;
				elseif (TIME>=strtotime('6:00')&&TIME<strtotime('12:00')) $Setliving=17.5;
				elseif (TIME>=strtotime('5:40')&&TIME<strtotime('12:00')) $Setliving=17.0;
				elseif (TIME>=strtotime('5:20')&&TIME<strtotime('12:00')) $Setliving=16.5;
				elseif (TIME>=strtotime('5:00')&&TIME<strtotime('12:00')) $Setliving=16.0;
				elseif (TIME>=strtotime('4:40')&&TIME<strtotime('12:00')) $Setliving=15.5;
			} else {
				if (TIME>=strtotime('6:00')&&TIME<strtotime('12:00')) $Setliving=19.0;
				elseif (TIME>=strtotime('5:40')&&TIME<strtotime('12:00')) $Setliving=18.5;
				elseif (TIME>=strtotime('5:20')&&TIME<strtotime('12:00')) $Setliving=18.0;
				elseif (TIME>=strtotime('5:00')&&TIME<strtotime('12:00')) $Setliving=17.5;
				elseif (TIME>=strtotime('4:40')&&TIME<strtotime('12:00')) $Setliving=17.0;
				elseif (TIME>=strtotime('4:20')&&TIME<strtotime('12:00')) $Setliving=16.5;
				elseif (TIME>=strtotime('4:00')&&TIME<strtotime('12:00')) $Setliving=16.0;
				elseif (TIME>=strtotime('3:40')&&TIME<strtotime('12:00')) $Setliving=15.5;
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
$kamers=array('living'/*,'kamer','tobi','alex'*/);
$bigdif=100;
$xxkamers=array();
foreach ($kamers as $kamer) {
    ${'dif'.$kamer}=number_format(
        $d[$kamer.'_temp']['s']-$d[$kamer.'_set']['s'],
        1
    );
    if (${'dif'.$kamer}<$bigdif) $bigdif=${'dif'.$kamer};
    ${'Set'.$kamer}=$d[$kamer.'_set']['s'];
    if (${'dif'.$kamer}<=0) {
        $xxkamers[]=$kamer;
        if ($kamer!='living') $d['heating']['s']=2;
    }
}
$first=true;
$xxxkamers='';
foreach ($xxkamers as $i) {
    if ($first) {
        $xxxkamers=$i;
        $first=false;
    } else {
        $xxxkamers.=', '.$i;
    }
}

if (isset($device)&&isset($difheater2)&&$device=='living_temp') {
    if ($difliving<$difheater2+0.1) {
        lg(
            'heater | Living Set = '.$Setliving
            .' | Living temp = '.$living_temp
            .' | Diff living = '.round($difliving, 2)
            .' | Verbruik = '.$d['el']['s']
            .' | Jaarteller = '.round($d['jaarteller']['s'], 3)
            .' | kamers = '.$xxxkamers
        );
    }
}
/*$kamers=array('tobi','alex','kamer');
foreach ($kamers as $kamer) {
    if (${'dif'.$kamer}<=number_format(($bigdif+ 0.2), 1)&&${'dif'.$kamer}<=0.2) {
        ${'RSet'.$kamer}=setradiator(
            $kamer,
            ${'dif'.$kamer},
            true,
            $d[$kamer.'_set']['s']
        );
    } else {
        ${'RSet'.$kamer}=setradiator(
            $kamer,
            ${'dif'.$kamer},
            false,
            $d[$kamer.'_set']['s']
        );
    }
    if (TIME>=strtotime('15:00')&&${'RSet'.$kamer}<15&&$d['raam'.$kamer]['s']=='Closed'&&$d['deur'.$kamer]['s']=='Closed') {
        if ($kamer!='tobi') {
            if ($d[$kamer.'_temp']['s']<15) ${'RSet'.$kamer}=18;
            elseif ($d[$kamer.'_temp']['s']<16) ${'RSet'.$kamer}=17;
            elseif ($d[$kamer.'_temp']['s']<17) ${'RSet'.$kamer}=16;
        } elseif ($kamer=='tobi'&&$d['gcal']['s']) {
            if ($d[$kamer.'_temp']['s']<15) ${'RSet'.$kamer}=18;
            elseif ($d[$kamer.'_temp']['s']<16) ${'RSet'.$kamer}=17;
            elseif ($d[$kamer.'_temp']['s']<17) ${'RSet'.$kamer}=16;
        }
    }
    if (round($d[$kamer.'Z']['s'], 1)!=round(${'RSet'.$kamer}, 1)) {
        ud($kamer.'Z', 0, round(${'RSet'.$kamer}, 0).'.0', basename(__FILE__).':'.__LINE__);
    }
}*/
//lg('bigdif='.$bigdif.'|brander='.$d['brander']['s'].'|timebrander='.past('brander'));
if ($d['heating']['s']==2) {
    if ($bigdif<=-0.2&&$d['brander']['s']=="Off"&&past('brander')>180) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
    elseif ($bigdif<=-0.1&&$d['brander']['s']=="Off"&&past('brander')>300) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
    elseif ($bigdif<= 0&&$d['brander']['s']=="Off"&&past('brander')>600) sw('brander','On', basename(__FILE__).':'.__LINE__);
    elseif ($bigdif>= 0&&$d['brander']['s']=="On"&&past('brander')>180) sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
    elseif ($bigdif>=-0.1&&$d['brander']['s']=="On"&&past('brander')>300) sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
    elseif ($bigdif>=-0.2&&$d['brander']['s']=="On"&&past('brander')>900) sw('brander','Off', basename(__FILE__).':'.__LINE__);
} elseif ($d['brander']['s']=='On') sw('brander', 'Off', basename(__FILE__).':'.__LINE__);

if ($bigdif!=$d['bigdif']['m']) storemode('bigdif', $bigdif, basename(__FILE__).':'.__LINE__);

if ($d['deurbadkamer']['s']=='Open'&&$d['badkamer_set']['s']!=10&&(past('deurbadkamer')>57|| $d['lichtbadkamer']['s']==0)) {
    store('badkamer_set', 10, basename(__FILE__).':'.__LINE__);
    $d['badkamer_set']['s']=10.0;
    if ($d['badkamer_set']['m']==1) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
} elseif ($d['deurbadkamer']['s']=='Closed'&&$d['badkamer_set']['m']==0) {
    $b7=past('$ 8badkamer-7');
    $b7b=past('8Kamer-7');
    if ($b7b<$b7) $b7=$b7b;
    $x=21;
    if ($d['buiten_temp']['s']<21&&$d['lichtbadkamer']['s']>0&&$d['badkamer_set']['s']!=$x&&($b7>900&&$d['heating']['s']>=1&&(TIME>strtotime('5:00')&& TIME<strtotime('7:30')))) {
        store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
        $d['badkamer_set']['s']=$x;
    } elseif ($b7>900&&$d['lichtbadkamer']['s']==0&&$d['buiten_temp']['s']<21&&$d['Weg']['s']<2) {
        if ($d['heating']['s']>1) {
			if ($weekend==false) {
				if (TIME>=strtotime('6:00')&&TIME<=strtotime('6:30')) {
					$x=19;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('5:57')&&TIME<=strtotime('6:30')) {
					$x=18.5;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('5:54')&&TIME<=strtotime('6:30')) {
					$x=18;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('5:51')&&TIME<=strtotime('6:30')) {
					$x=17.5;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('5:48')&&TIME<=strtotime('6:30')) {
					$x=17;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('5:45')&&TIME<=strtotime('6:30')) {
					$x=16.5;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('5:42')&&TIME<=strtotime('6:30')) {
					$x=16;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('5:39')&&TIME<=strtotime('6:30')) {
					$x=15.5;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('5:36')&&TIME<=strtotime('6:30')) {
					$x=15;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('5:33')&&TIME<=strtotime('6:30')) {
					$x=14.5;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('5:30')&&TIME<=strtotime('6:30')) {
					$x=14;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('5:27')&&TIME<=strtotime('6:30')) {
					$x=13.5;
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
					$x=19;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('6:57')&&TIME<=strtotime('7:30')) {
					$x=18.5;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('6:54')&&TIME<=strtotime('7:30')) {
					$x=18;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('6:51')&&TIME<=strtotime('7:30')) {
					$x=17.5;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('6:48')&&TIME<=strtotime('7:30')) {
					$x=17;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('6:45')&&TIME<=strtotime('7:30')) {
					$x=16.5;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('6:42')&&TIME<=strtotime('7:30')) {
					$x=16;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('6:39')&&TIME<=strtotime('7:30')) {
					$x=15.5;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('6:36')&&TIME<=strtotime('7:30')) {
					$x=15;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('6:33')&&TIME<=strtotime('7:30')) {
					$x=14.5;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('6:30')&&TIME<=strtotime('7:30')) {
					$x=14;
					if ($d['badkamer_set']['s']!=$x) {
						store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
						$d['badkamer_set']['s']=$x;
					}
				} elseif (TIME>=strtotime('6:27')&&TIME<=strtotime('7:30')) {
					$x=13.5;
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
        || ($d['Weg']['s']>=2&&$d['badkamer_set']['s']!=10)) {
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

if ($d['Weg']['s']==0&&$difzolder<0&&TIME>=strtotime('7:00')&&TIME<strtotime('21:30')) {
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


foreach (array('living', 'kamer', 'alex') as $k) {
	${'dif'.$k}=number_format($d[$k.'_temp']['s']-$d[$k.'_set']['s'], 1);
    if (${'dif'.$k}<$bigdif) $bigdif=${'dif'.$k};
    $daikin=json_decode($d['daikin'.$k]['s']);
    
//    lg($k.' corr='.$corr.' set='.$set.' temp='.$d[$k.'_temp']['s']);
    if ($d[$k.'_set']['s']>22) $d[$k.'_set']['s']=22;
	if ($d[$k.'_set']['s']>10) {
		if (${'dif'.$k}>=-0.3) {$rate='B';$d[$k.'_set']['s']=$d[$k.'_set']['s']-5;$power=0;}
		elseif (${'dif'.$k}>=-0.6) {$rate='3';$d[$k.'_set']['s']=$d[$k.'_set']['s']-4;$power=1;}
		elseif (${'dif'.$k}>=-0.9) {$rate=4;$d[$k.'_set']['s']=$d[$k.'_set']['s']-3.5;$power=1;}
		elseif (${'dif'.$k}>=-1.2) {$rate=5;$d[$k.'_set']['s']=$d[$k.'_set']['s']-3;$power=1;}
		elseif (${'dif'.$k}>=-1.5) {$rate=6;$d[$k.'_set']['s']=$d[$k.'_set']['s']-2.5;$power=1;}
		else {$rate=7;$d[$k.'_set']['s']=$d[$k.'_set']['s'];$power=1;}
		if ($daikin->stemp!=$d[$k.'_set']['s']||$daikin->pow!=$power||$daikin->mode!=4||$daikin->f_rate!=$rate) {
			daikinset($k, $power, 4, $d[$k.'_set']['s'], basename(__FILE__).':'.__LINE__, $rate);
			storemode('daikin'.$k, 4);
			if ($k=='living') $ip=111;
			elseif ($k=='kamer') $ip=112;
			elseif ($k=='alex') $ip=113;
			if (TIME>strtotime('8:00')||TIME<strtotime('19:00')) $streamer=1;
			else $streamer=0;
			
			$data=json_decode($d[$k.'_set']['icon'], true);
			$data['power']=$power;
			$data['mode']=3;
			$data['fan']=$rate;
			$data['set']=$d[$k.'_set']['s'];
			if ((isset($data['streamer'])&&$data['streamer']!=$streamer)||!isset($data['streamer'])) {
				sleep(1);
				file_get_contents('http://192.168.2.'.$ip.'/aircon/set_special_mode?en_streamer='.$streamer);
				$data['streamer']=$streamer;
			}
			storeicon($k.'_set', json_encode($data));
		}
	} else {
		if ($daikin->pow!=$power||$daikin->mode!=4) {
			daikinset($k, $power, 4, $d[$k.'_set']['s'], basename(__FILE__).':'.__LINE__);
			storemode('daikin'.$k, 0);
			$data=json_decode($d[$k.'_set']['icon'], true);
			$data['power']=$power;
			$data['mode']=4;
			$data['set']=$d[$k.'_set']['s'];
			storeicon($k.'_set', json_encode($data));
		}
	}
}
$boven=array('Rtobi','Ralex','RkamerL','RkamerR');
$beneden=array('Rbureel','RkeukenL','RkeukenR');
$benedenall=array('Rliving','Rbureel','RkeukenL','RkeukenR');
if ($d['minmaxtemp']['s']>20||$d['minmaxtemp']['m']>22) $warm=true; else $warm=false;
if ($d['minmaxtemp']['s']<5&&$d['minmaxtemp']['m']<5) $koud=true; else $koud=false;
if (TIME>=$d['civil_twilight']['s']&&TIME<=$d['civil_twilight']['m']) $dag=true; else $dag=false;
$zon=$d['zon']['s'];
if ($d['auto']['s']=='On'&&$d['Weg']['s']<3) {
	if (TIME>=strtotime('5:30')&&TIME<strtotime('10:00')) {
		$dow=date("w");
		if($dow==0||$dow==6) {
			if ($d['RkamerL']['s']>0&&TIME>=strtotime('7:00')) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
			if ($d['RkamerR']['s']>0&&TIME>=strtotime('7:00')) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Rtobi']['s']>0&&TIME>=strtotime('9:00')) sl('Rtobi', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Ralex']['s']>0&&TIME>=strtotime('9:00')) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
		} else {
			if ($d['RkamerL']['s']>0&&TIME>=strtotime('6:00')) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
			if ($d['RkamerR']['s']>0&&TIME>=strtotime('6:00')) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Rtobi']['s']>0&&TIME>=strtotime('8:00')) sl('Rtobi', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Ralex']['s']>0&&TIME>=strtotime('9:00')) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
		}
		if ($dag==true) {
			if ($d['Weg']['s']!=1&&$d['Rtobi']['s']>0&&($d['deurtobi']['s']=='Open'||$d['tobi']['s']>0)) sl('Rtobi', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Weg']['s']!=1&&$d['Ralex']['s']>0&&($d['deuralex']['s']=='Open'||$d['alex']['s']>0)) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
		}
		if ($dag==true&&$zon==0&&$d['Weg']['s']!=1&&$d['tv']['s']=='Off') {
			foreach ($beneden as $i) {
				if ($d[$i]['s']>0) sl($i, 0, basename(__FILE__).':'.__LINE__);
			}
			if ($d['Rliving']['s']>0) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		} elseif ($dag==true&&$zon>0&&$d['Weg']['s']!=1&&$d['tv']['s']=='Off') {
			foreach ($beneden as $i) {
				if ($d[$i]['s']>0&&past($i)>7200) sl($i, 0, basename(__FILE__).':'.__LINE__);
			}
			if ($d['Rliving']['s']>0) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		}
	}
	
	elseif (TIME>=strtotime('15:00')&&TIME<strtotime('17:00')) {
		if ($d['buiten_temp']['s']<10) {
			$items=array('tobi', 'alex');
			foreach ($items as $i) {
				if ($d['raam'.$i]['s']=='Open'&&$d[$i.'_temp']['s']<16&&past('raam'.$i)>14400&&$d['R'.$i]['s']<100) {
					sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
				}
			}
			$items=array('kamerL', 'kamerR');
			foreach ($items as $i) {
				if ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<16&&past('raamkamer')>14400&&$d['R'.$i]['s']<100) {
					sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
	
	elseif (TIME>=strtotime('17:00')&&TIME<strtotime('22:00')) {
		if ($zon==0) {
			foreach ($boven as $i) {
				if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
			if ($d['Weg']['s']>0) {
				foreach ($benedenall as $i) {
					if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
				}
			} else {
				if ($dag==false) {
					foreach ($beneden as $i) {
						if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
					}
				}
			}
		} elseif ($d['buiten_temp']['s']<15) {
			$items=array('tobi', 'alex');
			foreach ($items as $i) {
				if ($d['raam'.$i]['s']=='Open'&&$d[$i.'_temp']['s']<16&&past('raam'.$i)>14400&&$d['R'.$i]['s']<100) {
					sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
				}
			}
			$items=array('kamerL', 'kamerR');
			foreach ($items as $i) {
				if ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<16&&past('raamkamer')>14400&&$d['R'.$i]['s']<100) {
					sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	} 
	
	elseif (TIME>=strtotime('22:00')||TIME<strtotime('6:00')) {
		if ($d['Weg']['s']>0) {
			foreach ($benedenall as $i) {
				if ($d[$i]['s']<100&&past($i)>7200) sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
			foreach ($boven as $i) {
				if ($d[$i]['s']<100&&past($i)>7200) sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
		}
	}
} elseif ($d['auto']['s']=='On'&&$d['Weg']['s']==3) {
	include('_Rolluiken_Vakantie.php');
}