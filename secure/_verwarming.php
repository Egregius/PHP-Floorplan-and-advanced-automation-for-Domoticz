<?php
/**
 * Pass2PHP verwarming
 * php version 7.3.4-2
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if (!isset($d)) {
	$d=fetchdata();
}
$user='heating';
/* Heating
0 = Neutral
1 = Cooling
2 = Elec
3 = Elec / Gas
*/
$x=0;//Neutral
if ($d['heatingauto']['s']=='On'&&past('heating')>3) {
    if ($d['buiten_temp']['s']>20||$d['minmaxtemp']['m']>21)$x=1;//Cooling
    elseif ($d['buiten_temp']['s']<12||$d['minmaxtemp']['m']<12||$d['minmaxtemp']['s']<6) {
        if ($d['jaarteller']['s']<1)$x=3;//Gas/Elec
        else $x=4;//Gas
    } elseif ($d['buiten_temp']['s']<15||$d['minmaxtemp']['m']<15||$d['minmaxtemp']['s']<10) {
        if ($d['jaarteller']['s']<2)$x=3;//Gas/Elec
        else $x=4;//Gas
    } elseif ($d['buiten_temp']['s']<18||$d['minmaxtemp']['m']<20)$x=2;//Elec
}
if ($d['heatingauto']['s']=='On'&&$d['heating']['s']!=$x) {
	store('heating', $x, basename(__FILE__).':'.__LINE__);//Cooling
	$d['heating']['s']=$x;
}
$Setkamer=4;
if ($d['kamer_set']['m']==0) {
    if ($d['buiten_temp']['s']<14
        && $d['minmaxtemp']['m']<15
        && $d['deurkamer']['s']=='Closed'
        && $d['raamkamer']['s']=='Closed'
        && $d['heating']['s']>=2
        && (past('raamkamer')>7198 || TIME>strtotime('21:00'))
    ) {
        $Setkamer=10;
        if (TIME<strtotime('4:00')) {
            $Setkamer=15.0;
        } elseif (TIME>strtotime('21:00')) {
            $Setkamer=15.0;
        }
    }
    if ($d['kamer_set']['s']!=$Setkamer) {
        store('kamer_set', $Setkamer, basename(__FILE__).':'.__LINE__);
        $d['kamer_set']['s']=$Setkamer;
    }
}

$Settobi=4;
if ($d['tobi_set']['m']==0) {
    if ($d['buiten_temp']['s']<14
        && $d['minmaxtemp']['m']<15
        && $d['deurtobi']['s']=='Closed'
        && $d['raamtobi']['s']=='Closed'
        && $d['heating']['s']>=2
        && (past('raamtobi')>7198 || TIME>strtotime('20:00'))
    ) {
        $Settobi=10;
        if ($d['gcal']['s']) {
            if (TIME<strtotime('4:30') || TIME>strtotime('19:10')) {
                $Settobi=15.0;
            }
        }
    }
    if ($d['tobi_set']['s']!=$Settobi) {
        store('tobi_set', $Settobi, basename(__FILE__).':'.__LINE__);
        $tobi_set=$Settobi;
        $d['tobi_set']['s']=$Settobi;
    }
}

$Setalex=4;
if ($d['alex_set']['m']==0) {
    if ($d['buiten_temp']['s']<16
        && $d['minmaxtemp']['m']<15
        && $d['deuralex']['s']=='Closed'
        && $d['raamalex']['s']=='Closed'
        && $d['heating']['s']>=2
        && (past('raamalex')>1800 || TIME>strtotime('19:00'))
    ) {
        $Setalex=10;
        if (TIME<strtotime('4:30')) {
            $Setalex=15.0;
        } elseif (TIME>strtotime('19:00')) {
            $Setalex=15.5;
        }
    }
    if ($d['alex_set']['s']!=$Setalex) {
        ud('alex_set', 0, $Setalex);
        $alex_set=$Setalex;
        $d['alex_set']['s']=$Setalex;
    }
}

$Setliving=16;
if ($d['living_set']['m']==0) {
    if ($d['buiten_temp']['s']<20
        && $d['minmaxtemp']['m']<20
        && $d['heating']['s']>=2
        && $d['raamliving']['s']=='Closed'
        && $d['deurinkom']['s']=='Closed'
        && $d['deurgarage']['s']=='Closed'
    ) {
        $Setliving=17;
        if ($d['Weg']['s']==0) {
            if (TIME>=strtotime('5:00') && TIME<strtotime('18:45')) {
                $Setliving=20.5;
            }
        } elseif ($d['Weg']['s']==1) {
            if (TIME>=strtotime('7:00') && TIME<strtotime('18:45')) {
                $Setliving=20;
            } elseif (TIME>=strtotime('6:30') && TIME<strtotime('18:45')) {
                $Setliving=19.5;
            } elseif (TIME>=strtotime('6:00') && TIME<strtotime('18:45')) {
                $Setliving=19.0;
            } elseif (TIME>=strtotime('5:30') && TIME<strtotime('18:45')) {
                $Setliving=18.5;
            } elseif (TIME>=strtotime('5:00') && TIME<strtotime('18:45')) {
                $Setliving=18.0;
            } elseif (TIME>=strtotime('4:30') && TIME<strtotime('18:45')) {
                $Setliving=17.5;
            } elseif (TIME>=strtotime('4:00') && TIME<strtotime('18:45')) {
                $Setliving=17.0;
            } elseif (TIME>=strtotime('3:30') && TIME<strtotime('18:45')) {
                $Setliving=16.5;
            }
        }
        if ($Setliving>19.5) {
            if (TIME>=strtotime('11:00')
                && $d['zon']['s']>3000
                && $d['buiten_temp']['s']>15
            ) {
                $Setliving=19.5;
            } elseif ($d['zon']['s']<2000) {
                $Setliving=20.5;
            }
        }
    }
    if ($d['living_set']['s']!=$Setliving
        && past('deurinkom')>60
        && past('deurgarage')>60
    ) {
        store('living_set', $Setliving, basename(__FILE__).':'.__LINE__);
        $living_set=$Setliving;
        $d['living_set']['s']=$Setliving;
    }
}
$kamers=array('living','kamer','tobi','alex');
$bigdif=100;
$xxkamers=array();
foreach ($kamers as $kamer) {
    ${'dif'.$kamer}=number_format(
        $d[$kamer.'_temp']['s']-$d[$kamer.'_set']['s'],
        1
    );
    if (${'dif'.$kamer}>9.9) {
        ${'dif'.$kamer}=9.9;
    }
    if (${'dif'.$kamer}<$bigdif) {
        $bigdif=${'dif'.$kamer};
    }
    ${'Set'.$kamer}=$d[$kamer.'_set']['s'];
    if (${'dif'.$kamer}<=0) {
        $xxkamers[]=$kamer;
        if ($kamer!='living') {
            $d['heating']['s']=3;
        }
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
if ($d['Weg']['s']==0) {
    if ($d['heating']['s']==0 || $d['heating']['s']==2) {//Neutral of elec
        $difheater2=0;
        $difheater3=-0.2;
        $difheater4=-0.4;
        //lg ('difliving='.$difliving.' - difheater2 = '.$difheater2.' -  1='.$d['heater1']['s'].' 2='.$d['heater2']['s'].'   3='.$d['heater3']['s'].'    4='.$d['heater4']['s']);
        if ($difliving>$difheater2
            && $d['heater1']['s']!='Off'
            && past('heater1')>90
            && past('heater2')>90
        ) {
            sw('heater1', 'Off', basename(__FILE__).':'.__LINE__);
        }
        if ($difliving<$difheater2
            && $d['heater2']['s']!='On'
            && past('heater2')>90
        ) {
            if ($d['heater1']['s']!='On') {
                sw('heater1', 'On', basename(__FILE__).':'.__LINE__);
            }
            sw('heater2', 'On', basename(__FILE__).':'.__LINE__);
            lg('111');
        } elseif ($difliving==$difheater2
            && $d['heater2']['s']!='On'
            && past('heater2')>140
            && $d['el']['s']<8000
        ) {
            sw('heater2', 'On', basename(__FILE__).':'.__LINE__);
            lg('222');
        } elseif ($difliving>=$difheater2
            && $d['heater2']['s']!='Off'
            && past('heater2')>110
            || $d['el']['s']>8500
        ) {
            sw('heater2', 'Off', basename(__FILE__).':'.__LINE__);
        }
        if ($difliving<=$difheater3
            && $d['heater3']['s']!='On'
            && past('heater3')>90
            && $d['el']['s']<7000
        ) {
            sw('heater3', 'On', basename(__FILE__).':'.__LINE__);
        } elseif ($difliving>=$difheater3
            && $d['heater3']['s']!='Off'
            && past('heater3')>30
            || $d['el']['s']>8000
        ) {
            sw('heater3', 'Off', basename(__FILE__).':'.__LINE__);
        }
        if ($difliving<=$difheater4
            && $d['heater4']['s']!='On'
            && past('heater4')>90
            && $d['el']['s']<6000
        ) {
            sw('heater4', 'On', basename(__FILE__).':'.__LINE__);
        } elseif ($difliving>=$difheater4
            && $d['heater4']['s']!='Off'
            && past('heater4')>30
            || $d['el']['s']>7000
        ) {
            sw('heater4', 'Off', basename(__FILE__).':'.__LINE__);
        }
    } elseif ($d['heating']['s']==3) {//gas/elec
        $difheater2=-0.3;
        $difheater3=-0.6;
        $difheater4=-1.0;
        if ($difliving>$difheater2
            && $d['heater1']['s']!='Off'
            && past('heater1')>90
            && past('heater2')>90
        ) {
            sw('heater1', 'Off', basename(__FILE__).':'.__LINE__);
        }
        if ($difliving<$difheater2
            && $d['heater2']['s']!='On'
            && past('heater2')>90
            && $d['el']['s']<8000
        ) {
            sw('heater2', 'On', basename(__FILE__).':'.__LINE__);
        } elseif ($difliving==$difheater2
            && $d['heater2']['s']!='On'
            && past('heater2')>180
            && $d['el']['s']<8000
        ) {
            sw('heater2', 'On', basename(__FILE__).':'.__LINE__);
        } elseif ($difliving>=$difheater2
            && $d['heater2']['s']!='Off'
            && past('heater2')>90
            || $d['el']['s']>8500
        ) {
            sw('heater2', 'Off', basename(__FILE__).':'.__LINE__);
        }
        if ($difliving<$difheater3
            && $d['heater3']['s']!='On'
            && past('heater3')>90
            && $d['el']['s']<7000
        ) {
            sw('heater3', 'On', basename(__FILE__).':'.__LINE__);
        } elseif ($difliving>=$difheater3
            && $d['heater3']['s']!='Off'
            && past('heater3')>30
            || $d['el']['s']>8000
        ) {
            sw('heater3', 'Off', basename(__FILE__).':'.__LINE__);
        }
        if ($difliving<$difheater4
            && $d['heater4']['s']!='On'
            && past('heater4')>90
            && $d['el']['s']<6000
        ) {
            sw('heater4', 'On', basename(__FILE__).':'.__LINE__);
        } elseif ($difliving>=$difheater4
            && $d['heater4']['s']!='Off'
            && past('heater4')>30
            || $d['el']['s']>7000
        ) {
            sw('heater4', 'Off', basename(__FILE__).':'.__LINE__);
        }
    } elseif ($d['heating']['s']==4) {//gas
        $difheater2=-1;
        $difheater3=-1.5;
        $difheater4=-2;
        if ($difliving>$difheater2
            && $d['heater1']['s']!='Off'
            && past('heater1')>90
            && past('heater2')>90
        ) {
            sw('heater1', 'Off', basename(__FILE__).':'.__LINE__);
        }
        if ($difliving<$difheater2
            && $d['heater2']['s']!='On'
            && past('heater2')>90
            && $d['el']['s']<8000
        ) {
            sw('heater2', 'On', basename(__FILE__).':'.__LINE__);
        } elseif ($difliving==$difheater2
            && $d['heater2']['s']!='On'
            && past('heater2')>180
            && $d['el']['s']<8000
        ) {
            sw('heater2', 'On', basename(__FILE__).':'.__LINE__);
        } elseif ($difliving>=$difheater2
            && $d['heater2']['s']!='Off'
            && past('heater2')>90
            || $d['el']['s']>8500
        ) {
            sw('heater2', 'Off', basename(__FILE__).':'.__LINE__);
        }
        if ($difliving<$difheater3
            && $d['heater3']['s']!='On'
            && past('heater3')>90
            && $d['el']['s']<7000
        ) {
            sw('heater3', 'On', basename(__FILE__).':'.__LINE__);
        } elseif ($difliving>=$difheater3
            && $d['heater3']['s']!='Off'
            && past('heater3')>30
            || $d['el']['s']>8000
        ) {
            sw('heater3', 'Off', basename(__FILE__).':'.__LINE__);
        }
        if ($difliving<$difheater4
            && $d['heater4']['s']!='On'
            && past('heater4')>90
            && $d['el']['s']<6000
        ) {
            sw('heater4', 'On', basename(__FILE__).':'.__LINE__);
        } elseif ($difliving>=$difheater4
            && $d['heater4']['s']!='Off'
            && past('heater4')>30
            || $d['el']['s']>7000
        ) {
            sw('heater4', 'Off', basename(__FILE__).':'.__LINE__);
        }
    } elseif ($d['heating']['s']==1) {//Cooling
        if ($d['heater4']['s']!='Off') {
            sw('heater4', 'Off', basename(__FILE__).':'.__LINE__);
        }
        if ($d['heater3']['s']!='Off') {
            sw('heater3', 'Off', basename(__FILE__).':'.__LINE__);
        }
        if ($d['heater2']['s']!='Off') {
            sw('heater2', 'Off', basename(__FILE__).':'.__LINE__);
        }
    }
} else {
    //Niet thuis of slapen
    if ($d['heater4']['s']!='Off') {
        sw('heater4', 'Off', basename(__FILE__).':'.__LINE__);
    }
    if ($d['heater3']['s']!='Off') {
        sw('heater3', 'Off', basename(__FILE__).':'.__LINE__);
    }
    if ($d['heater2']['s']!='Off') {
        sw('heater2', 'Off', basename(__FILE__).':'.__LINE__);
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
$kamers=array('tobi','alex','kamer');
foreach ($kamers as $kamer) {
    if (${'dif'.$kamer}<=number_format(($bigdif+ 0.2), 1)
        && ${'dif'.$kamer}<=0.2
    ) {
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
    if (TIME>=strtotime('15:00')
        && ${'RSet'.$kamer}<15
        && $d['raam'.$kamer]['s']!='Open'
    ) {
        if ($kamer!='tobi') {
            if ($d[$kamer.'_temp']['s']<15) {
                ${'RSet'.$kamer}=18;
            } elseif ($d[$kamer.'_temp']['s']<16) {
                ${'RSet'.$kamer}=17;
            } elseif ($d[$kamer.'_temp']['s']<17) {
                ${'RSet'.$kamer}=16;
            }
        } elseif ($kamer=='tobi' && $d['gcal']['s']) {
            if ($d[$kamer.'_temp']['s']<15) {
                ${'RSet'.$kamer}=18;
            } elseif ($d[$kamer.'_temp']['s']<16) {
                ${'RSet'.$kamer}=17;
            } elseif ($d[$kamer.'_temp']['s']<17) {
                ${'RSet'.$kamer}=16;
            }
        }
    }
    if (round($d[$kamer.'Z']['s'], 1)!=round(${'RSet'.$kamer}, 1)) {
        //store('Tset'.$kamer.'Z',TIME, basename(__FILE__).':'.__LINE__);
        ud($kamer.'Z', 0, round(${'RSet'.$kamer}, 0).'.0');
    }
}
//lg('bigdif='.$bigdif.'|brander='.$d['brander']['s'].'|timebrander='.past('brander'));
if ($d['heating']['s']>=3) {
    if ($bigdif<=-0.2
        && $d['brander']['s']=="Off"
        && past('brander')>180
    ) {
        sw('brander', 'On', basename(__FILE__).':'.__LINE__);
    } elseif ($bigdif<=-0.1
        && $d['brander']['s']=="Off"
        && past('brander')>300
    ) {
        sw('brander', 'On', basename(__FILE__).':'.__LINE__);
    } elseif ($bigdif<= 0
        && $d['brander']['s']=="Off"
        && past('brander')>600
    ) {
        sw('brander','On', basename(__FILE__).':'.__LINE__);
    } elseif ($bigdif>= 0
        && $d['brander']['s']=="On"
        && past('brander')>180
    ) {
        sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
    } elseif ($bigdif>=-0.1
        && $d['brander']['s']=="On"
        && past('brander')>300
    ) {
        sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
    } elseif ($bigdif>=-0.2
        && $d['brander']['s']=="On"
        && past('brander')>900
    ) {
        sw('brander','Off', basename(__FILE__).':'.__LINE__);
    }
} elseif ($d['brander']['s']=='On') {
    sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
}
if ($bigdif!=$d['heating']['m']) {
    storemode('heating', $bigdif, basename(__FILE__).':'.__LINE__);
}
$x=10;
if ($d['deurbadkamer']['s']=='Open'&&(past('deurbadkamer')>57||$d['lichtbadkamer']['s']==0))$x=10;
elseif ($d['deurbadkamer']['s']=='Closed') {
    $b7=past('8badkamer-7');
    $b7b=past('8Kamer-7');
    if($b7b<$b7)$b7=$b7b;
    if ($d['buiten_temp']['s']<21&&$d['lichtbadkamer']['s']>0&&TIME>strtotime('5:00')&&TIME<strtotime('7:00')&&($b7>900&&($d['heating']['s']==0||$d['heating']['s']>=2)))$x=22.5;
    elseif ($b7>900&&$d['lichtbadkamer']['s']==0&&$d['buiten_temp']['s']<21&&$d['Weg']['s']<2) {
        if ($d['heating']['s']>=2) {
            if     (TIME>=strtotime('6:00') && TIME<=strtotime('6:45'))$x=20;
            elseif (TIME>=strtotime('5:45') && TIME<=strtotime('6:45'))$x=19.5;
            elseif (TIME>=strtotime('5:30') && TIME<=strtotime('6:45'))$x=19;
            elseif (TIME>=strtotime('5:15') && TIME<=strtotime('6:45'))$x=18.5;
            elseif (TIME>=strtotime('5:00') && TIME<=strtotime('6:45'))$x=18;
            elseif (TIME>=strtotime('4:45') && TIME<=strtotime('6:45'))$x=17.5;
            elseif (TIME>=strtotime('4:30') && TIME<=strtotime('6:45'))$x=17;
            elseif (TIME>=strtotime('4:15') && TIME<=strtotime('6:45'))$x=16.5;
            elseif (TIME>=strtotime('4:00') && TIME<=strtotime('6:45'))$x=16;
            elseif (TIME>=strtotime('3:45') && TIME<=strtotime('6:45'))$x=15.5;
            elseif (TIME>=strtotime('3:30') && TIME<=strtotime('6:45'))$x=15;
            elseif (TIME>=strtotime('3:15') && TIME<=strtotime('6:45'))$x=14.5;
            elseif (TIME>=strtotime('3:00') && TIME<=strtotime('6:45'))$x=14;
        }
    }
}
if ($d['badkamer_set']['s']!=$x) {
	store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
	$d['badkamer_set']['s']=$x;
}
$difbadkamer=$d['badkamer_temp']['s']-$d['badkamer_set']['s'];
if ($difbadkamer<=-1) {
    if ($d['deurbadkamer']['s']=='Closed'
        && $d['badkamervuur1']['s']!='On'
        && past('badkamervuur1')>30
        && $d['el']['s']<7200
    ) {
        sw('badkamervuur1', 'On', basename(__FILE__).':'.__LINE__);
    }
    if ($d['deurbadkamer']['s']=='Closed'
        && $d['badkamervuur2']['s']!='On'
        && past('badkamervuur2')>30
        && $d['lichtbadkamer']['s']>0
        && $d['el']['s']<6800
    ) {
        sw('badkamervuur2', 'On', basename(__FILE__).':'.__LINE__);
    }
} elseif ($difbadkamer<= 0) {
    if ($d['deurbadkamer']['s']=='Closed'
        && $d['badkamervuur1']['s']!='On'
        && past('badkamervuur1')>30
        && $d['el']['s']<7200
    ) {
        sw('badkamervuur1', 'On', basename(__FILE__).':'.__LINE__);
    }
    if (($d['badkamervuur2']['s']!='Off'
        && past('badkamervuur2')>30)
        || $d['el']['s']>7500
    ) {
        sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
    }
} else {
    if (($d['badkamervuur2']['s']!='Off'
        && past('badkamervuur2')>30)
        || $d['el']['s']>7500
    ) {
        sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
    }
    if (($d['badkamervuur1']['s']!='Off'
        && past('badkamervuur1')>30)
        || $d['el']['s']>8200
    ) {
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

if ($difzolder<=-0.2
    && $d['zoldervuur']['s']!="On"
    && past('zoldervuur')>30
    && $d['el']['s']<4800
    && $d['heating']['s']>=2
    && $d['Weg']['s']==0
) {
    sw('zoldervuur', 'On', basename(__FILE__).':'.__LINE__);
    lg('>>>>>>>>>> difzolder = '.$difzolder);
} elseif ($difzolder<=-0.1
    && $d['zoldervuur']['s']!="On"
    && past('zoldervuur')>90
    && $d['el']['s']<4800
    && $d['heating']['s']>=2
    && $d['Weg']['s']==0
) {
    sw('zoldervuur', 'On', basename(__FILE__).':'.__LINE__);
    lg('>>>>>>>>>> difzolder = '.$difzolder);
} elseif ($difzolder<= 0
    && $d['zoldervuur']['s']!="On"
    && past('zoldervuur')>180
    && $d['el']['s']<4800
    && $d['heating']['s']>=2
    && $d['Weg']['s']==0
) {
    sw('zoldervuur', 'On', basename(__FILE__).':'.__LINE__);
    lg('>>>>>>>>>> difzolder = '.$difzolder);
} elseif (($difzolder>= 0
    && $d['zoldervuur']['s']!="Off"
    && past('zoldervuur')>30)
    || ($d['zoldervuur']['s']!="Off"
    && ($d['el']['s']>6600 || $d['Weg']['s']>0))
) {
    sw('zoldervuur', 'Off', basename(__FILE__).':'.__LINE__);
    lg('>>>>>>>>>> difzolder = '.$difzolder);
} elseif (($difzolder>=-0.3
    && $d['zoldervuur']['s']!="Off"
    && past('zoldervuur')>120)
    || ($d['zoldervuur']['s']!="Off"
    && ($d['el']['s']>6600 || $d['Weg']['s']>0))
) {
    sw('zoldervuur', 'Off', basename(__FILE__).':'.__LINE__);
    lg('>>>>>>>>>> difzolder = '.$difzolder);
} elseif (($difzolder>=-0.5
    && $d['zoldervuur']['s']!="Off"
    && past('zoldervuur')>180)
    || ($d['zoldervuur']['s']!="Off"
    && ($d['el']['s']>6600 || $d['Weg']['s']>0))
) {
    sw('zoldervuur', 'Off', basename(__FILE__).':'.__LINE__);
    lg('>>>>>>>>>> difzolder = '.$difzolder);
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
    if ($koudst==true) {
        $setpoint=28.0;
    } else {
        $setpoint=$set-ceil($dif*4);
    }
    if ($setpoint>28) {
        $setpoint=28.0;
    } elseif ($setpoint<4) {
        $setpoint=4.0;
    }
    return round($setpoint, 0);
}