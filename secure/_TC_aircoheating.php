<?php
/**
 * Pass2PHP Temperature Control Airco heating
 * php version 7.3
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/

$Setkamer=10;
if ($d['kamer_set']['m']==0) {
    if ($d['buiten_temp']['s']<14&&$d['kamer_temp']['s']<15.8&&$d['minmaxtemp']['m']<15&&($d['deurkamer']['s']=='Closed'||($d['deurkamer']['s']=='Open'&&past('deurkamer')<300))&&$d['raamkamer']['s']=='Closed'&&(past('raamkamer')>7198 || TIME>strtotime('21:00'))) {
        if (TIME<strtotime('4:00')) $Setkamer=12;
        elseif (TIME>strtotime('21:00')) $Setkamer=12;
    }
    if ($d['kamer_set']['s']!=$Setkamer) {
        store('kamer_set', $Setkamer, basename(__FILE__).':'.__LINE__);
        $d['kamer_set']['s']=$Setkamer;
    }
}

$Setalex=10;
if ($d['alex_set']['m']==0) {
    if ($d['buiten_temp']['s']<14&&$d['alex_temp']['s']<15.8&&$d['minmaxtemp']['m']<15&&($d['deuralex']['s']=='Closed'||($d['deuralex']['s']=='Open'&&past('deuralex')<300))&&$d['raamalex']['s']=='Closed'&&(past('raamalex')>1800 || TIME>strtotime('19:00'))) {
        if (TIME<strtotime('4:30')) $Setalex=14;
        elseif (TIME>strtotime('19:00')) $Setalex=14;
    }
    if ($d['alex_set']['s']!=$Setalex) {
        ud('alex_set', 0, $Setalex, true, basename(__FILE__).':'.__LINE__);
        $d['alex_set']['s']=$Setalex;
    }
}

$Setliving=10;
if ($d['living_set']['m']==0) {
    if (
    		$d['buiten_temp']['s']<20
    	&&	$d['living_temp']['s']<21
    	&&	$d['minmaxtemp']['m']<24
    	&&	($d['raamliving']['s']=='Closed'||($d['raamliving']['s']=='Open'&&past('raamliving')<300))
    	&&	($d['raamkeuken']['s']=='Closed'||($d['raamkeuken']['s']=='Open'&&past('raamkeuken')<300))
    	&&	($d['deurinkom']['s']=='Closed'||($d['deurinkom']['s']=='Open'&&past('deurinkom')<300))
    	&&	($d['deurgarage']['s']=='Closed'||($d['deurgarage']['s']=='Open'&&past('deurgarage')<300))
    ) {
        if ($d['Weg']['s']==0) {
            if (TIME>=strtotime('5:00')&&TIME<strtotime('19:00')) $Setliving=20.5;
        }
    }
    if ($d['living_set']['s']!=$Setliving&&past('raamliving')>60&&past('deurinkom')>60&&past('deurgarage')>60) {
        store('living_set', $Setliving, basename(__FILE__).':'.__LINE__);
        $d['living_set']['s']=$Setliving;
    }
}
$bigdif=100;
foreach (array('living', 'kamer', 'alex') as $k) {
	${'dif'.$k}=number_format($d[$k.'_temp']['s']-$d[$k.'_set']['s'], 1);
    if (${'dif'.$k}<$bigdif) $bigdif=${'dif'.$k};
    $daikin=json_decode($d['daikin'.$k]['s']);
    
//    lg($k.' corr='.$corr.' set='.$set.' temp='.$d[$k.'_temp']['s']);
    if ($d[$k.'_set']['s']>22) $d[$k.'_set']['s']=22;
	if ($d[$k.'_set']['s']>10) {
		if (${'dif'.$k}>=0.5) {$d[$k.'_set']['s']=$d[$k.'_set']['s']-6;$power=0;}
		elseif (${'dif'.$k}>=0.4) {$d[$k.'_set']['s']=$d[$k.'_set']['s']-5;$power=1;}
		elseif (${'dif'.$k}>=0.3) {$d[$k.'_set']['s']=$d[$k.'_set']['s']-4.5;$power=1;}
		elseif (${'dif'.$k}>=0.2) {$d[$k.'_set']['s']=$d[$k.'_set']['s']-4;$power=1;}
		elseif (${'dif'.$k}>=0.1) {$d[$k.'_set']['s']=$d[$k.'_set']['s']-4;$power=1;}
		elseif (${'dif'.$k}>=0) {$d[$k.'_set']['s']=$d[$k.'_set']['s']-3.5;$power=1;}
		elseif (${'dif'.$k}>=-0.1) {$d[$k.'_set']['s']=$d[$k.'_set']['s']-3;$power=1;}
		elseif (${'dif'.$k}>=-0.2) {$d[$k.'_set']['s']=$d[$k.'_set']['s']-2.5;$power=1;}
		elseif (${'dif'.$k}>=-0.3) {$d[$k.'_set']['s']=$d[$k.'_set']['s']-2.5;$power=1;}
		elseif (${'dif'.$k}>=-0.4) {$d[$k.'_set']['s']=$d[$k.'_set']['s']-2;$power=1;}
		elseif (${'dif'.$k}>=-0.5) {$d[$k.'_set']['s']=$d[$k.'_set']['s']-1.5;$power=1;}
		elseif (${'dif'.$k}>=-0.6) {$d[$k.'_set']['s']=$d[$k.'_set']['s']-1;$power=1;}
		elseif (${'dif'.$k}>=-0.7) {$d[$k.'_set']['s']=$d[$k.'_set']['s']-0.5;$power=1;}
		elseif (${'dif'.$k}>=-0.8) {$d[$k.'_set']['s']=$d[$k.'_set']['s'];$power=1;}
		else {$d[$k.'_set']['s']=$d[$k.'_set']['s']+1;$power=1;}
		
		if ($d[$k.'_temp']['icon']>0.15&&${'dif'.$k}>=0) $d[$k.'_set']['s']=$d[$k.'_set']['s']-1;
		elseif ($d[$k.'_temp']['icon']>0.05&&${'dif'.$k}>=0) $d[$k.'_set']['s']=$d[$k.'_set']['s']-0.5;
		elseif ($d[$k.'_temp']['icon']<0.2&&${'dif'.$k}<=0) $d[$k.'_set']['s']=$d[$k.'_set']['s']+1;
		elseif ($d[$k.'_temp']['icon']<0.1&&${'dif'.$k}<=0) $d[$k.'_set']['s']=$d[$k.'_set']['s']+0.5;
		
		$rate='A';
		if ($k=='kamer'&&$d['Weg']['s']==1) $rate='B';
		if ($k=='alex'&&(TIME>strtotime('19:45')||TIME<strtotime('08:00'))) $rate='B';
	
		$daikin=json_decode($d['daikin'.$k]['s']);
		if ($daikin->adv == '') {
			$streamer=0;
			$powermode=0;
		} else if (strstr($daikin->adv, '/')) { 
			$advs=explode("/", $daikin->adv);
			if ($advs[1]==13) $streamer=1;
			else if ($adv[1]=='') $streamer=0;
			if ($advs[0]==2) $powermode=2;
			else if ($advs[0]==12) $powermode=1;
			else $powermode=0;
		} else {
			if ($daikin->adv==13) {
				$streamer=1;
				$powermode=0; //Normal
			} else if ($daikin->adv==12) {
				$streamer=0;
				$powermode=1; // Eco
			} else if ($daikin->adv==2) {
				$streamer=0;
				$powermode=2; // Power
			} else if ($daikin->adv=='') {
				$streamer=0;
				$powermode=0;
			}
		}
		if ($powermode<2) {
			if ($daikin->stemp!=$d[$k.'_set']['s']||$daikin->pow!=$power||$daikin->mode!=4||$daikin->f_rate!=$rate) {
				daikinset($k, $power, 4, $d[$k.'_set']['s'], basename(__FILE__).':'.__LINE__, $rate);
				storemode('daikin'.$k, 4);
				if ($k=='living') $ip=111;
				elseif ($k=='kamer') $ip=112;
				elseif ($k=='alex') $ip=113;
				$streamer=0;
			
				$data=json_decode($d[$k.'_set']['icon'], true);
				$data['power']=$power;
				$data['mode']=4;
				$data['fan']=$rate;
				$data['set']=$d[$k.'_set']['s'];
				if ((isset($data['streamer'])&&$data['streamer']!=$streamer)||!isset($data['streamer'])) {
					sleep(1);
					file_get_contents('http://192.168.2.'.$ip.'/aircon/set_special_mode?en_streamer='.$streamer);
					$data['streamer']=$streamer;
				}
				storeicon($k.'_set', json_encode($data));
			}
		}
	} else {
		$power=0;
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
    $x=21;
    if ($d['buiten_temp']['s']<21&&$d['lichtbadkamer']['s']>0&&$d['badkamer_set']['s']!=$x&&($b7>900&&$d['heating']['s']>=1&&(TIME>strtotime('5:00')&& TIME<strtotime('7:30')))) {
        store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
        $d['badkamer_set']['s']=$x;
    } elseif ($b7>900&&$d['lichtbadkamer']['s']==0&&$d['buiten_temp']['s']<21&&$d['Weg']['s']<2) {
        if ($d['badkamer_set']['s']!=10) {
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

if ($d['Weg']['s']==0&&$difzolder<0) {
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
			if ($d['Rliving']['s']>0&&$d['tv']['s']=='Off') sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		} elseif ($dag==true&&$zon>0&&$d['Weg']['s']!=1&&$d['tv']['s']=='Off') {
			foreach ($beneden as $i) {
				if ($d[$i]['s']>0) sl($i, 0, basename(__FILE__).':'.__LINE__);
			}
			if ($d['Rliving']['s']>0) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		}
	} 

	elseif (TIME>=strtotime('15:00')&&TIME<strtotime('17:00')) {
		if ($d['buiten_temp']['s']<10) {
			$items=array('tobi', 'alex');
			foreach ($items as $i) {
				if ($d['raam'.$i]['s']=='Open'&&$d[$i.'_temp']['s']<16&&past('R'.$i)>14400&&past('raam'.$i)>14400&&$d['R'.$i]['s']<100) {
					sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
				}
			}
			$items=array('kamerL', 'kamerR');
			foreach ($items as $i) {
				if ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<16&&past('R'.$i)>14400&&past('raamkamer')>14400&&$d['R'.$i]['s']<100) {
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
				if ($d['raam'.$i]['s']=='Open'&&$d[$i.'_temp']['s']<16&&past('R'.$i)>14400&&past('raam'.$i)>14400&&$d['R'.$i]['s']<100) {
					sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
				}
			}
			$items=array('kamerL', 'kamerR');
			foreach ($items as $i) {
				if ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<16&&past('R'.$i)>14400&&past('raamkamer')>14400&&$d['R'.$i]['s']<100) {
					sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	} 

	elseif (TIME>=strtotime('22:00')||TIME<strtotime('6:00')) {
		if ($d['Weg']['s']>0) {
			foreach ($benedenall as $i) {
				if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
			foreach ($boven as $i) {
				if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
		}
	}
} elseif ($d['auto']['s']=='On'&&$d['Weg']['s']==3) {
	include('_Rolluiken_Vakantie.php');
}