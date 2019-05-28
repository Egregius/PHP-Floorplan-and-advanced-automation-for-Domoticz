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
//lg('               __verwarming__');
$user='verwarming';
if ($d['heatingauto']['s']=='On'&&past('heating')>36) {
    if ($d['buiten_temp']['s']>20||$d['minmaxtemp']['m']>21) {
        if ($d['heating']['s']!=1) {
            store('heating', 1);
            $d['heating']['s']=1;
        }
    } elseif ($d['buiten_temp']['s']<12
        ||$d['minmaxtemp']['m']<12
        ||$d['minmaxtemp']['s']<5
    ) {
        if ($d['heating']['s']!=3) {
            store('heating', 3);
            $d['heating']['s']=3;
        }
    } elseif ($d['buiten_temp']['s']<15||$d['minmaxtemp']['m']<16) {
        if ($d['heating']['s']!=2) {
            store('heating', 2);
            $d['heating']['s']=2;
        }
    } else {
        if ($d['heating']['s']!=0) {
            store('heating', 0);
            $d['heating']['s']=0;
        }
    }
}

$Setkamer=4;
if ($d['kamer_set']['m']!=2) {
    if ($d['buiten_temp']['s']<14
        && $d['minmaxtemp']['m']<15
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
        store('kamer_set', $Setkamer);
        $d['kamer_set']['s']=$Setkamer;
    }
}

$Settobi=4;
if ($d['tobi_set']['m']!=2) {
    if ($d['buiten_temp']['s']<14
        && $d['minmaxtemp']['m']<15
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
        store('tobi_set', $Settobi);
        $tobi_set=$Settobi;
        $d['tobi_set']['s']=$Settobi;
    }
}

$Setalex=4;
if ($d['alex_set']['m']!=2) {
    if ($d['buiten_temp']['s']<16
        && $d['minmaxtemp']['m']<15
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
if ($d['living_set']['m']!=2) {
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
            }
        }
        if ($Setliving>=20.0) {
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
        store('living_set', $Setliving);
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
            sw('heater1', 'Off', false, 1);
        }
        if ($difliving<$difheater2
            && $d['heater2']['s']!='On'
            && past('heater2')>90
        ) {
            if ($d['heater1']['s']!='On') {
                sw('heater1', 'On', false, 2);
                sleep(5);
            }
            sw('heater2', 'On', false, 3);
            lg('111');
        } elseif ($difliving==$difheater2
            && $d['heater2']['s']!='On'
            && past('heater2')>180
            && $d['el']['s']<8000
        ) {
            sw('heater2', 'On', false, 4);
            lg('222');
        } elseif ($difliving>=$difheater2
            && $d['heater2']['s']!='Off'
            && past('heater2')>90
            || $d['el']['s']>8500
        ) {
            sw('heater2', 'Off', false, 5);
        }
        if ($difliving<=$difheater3
            && $d['heater3']['s']!='On'
            && past('heater3')>90
            && $d['el']['s']<7000
        ) {
            sw('heater3', 'On');
        } elseif ($difliving>=$difheater3
            && $d['heater3']['s']!='Off'
            && past('heater3')>30
            || $d['el']['s']>8000
        ) {
            sw('heater3', 'Off', false, 6);
        }
        if ($difliving<=$difheater4
            && $d['heater4']['s']!='On'
            && past('heater4')>90
            && $d['el']['s']<6000
        ) {
            sw('heater4', 'On', false, 7);
        } elseif ($difliving>=$difheater4
            && $d['heater4']['s']!='Off'
            && past('heater4')>30
            || $d['el']['s']>7000
        ) {
            sw('heater4', 'Off', false, 8);
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
            sw('heater1', 'Off', false, 9);
        }
        if ($difliving<$difheater2
            && $d['heater2']['s']!='On'
            && past('heater2')>90
            && $d['el']['s']<8000
        ) {
            sw('heater2', 'On', false, 10);
            lg('333');
        } elseif ($difliving==$difheater2
            && $d['heater2']['s']!='On'
            && past('heater2')>180
            && $d['el']['s']<8000
        ) {
            sw('heater2', 'On', false, 11);
            lg('444');
        } elseif ($difliving>=$difheater2
            && $d['heater2']['s']!='Off'
            && past('heater2')>90
            || $d['el']['s']>8500
        ) {
            sw('heater2', 'Off', false, 12);
        }
        if ($difliving<$difheater3
            && $d['heater3']['s']!='On'
            && past('heater3')>90
            && $d['el']['s']<7000
        ) {
            sw('heater3', 'On', false, 13);
        } elseif ($difliving>=$difheater3
            && $d['heater3']['s']!='Off'
            && past('heater3')>30
            || $d['el']['s']>8000
        ) {
            sw('heater3', 'Off', false, 14);
        }
        if ($difliving<$difheater4
            && $d['heater4']['s']!='On'
            && past('heater4')>90
            && $d['el']['s']<6000
        ) {
            sw('heater4', 'On', false, 15);
        } elseif ($difliving>=$difheater4
            && $d['heater4']['s']!='Off'
            && past('heater4')>30
            || $d['el']['s']>7000
        ) {
            sw('heater4', 'Off', false, 16);
        }
    } elseif ($d['heating']['s']==1) {
        //Cooling
        if ($d['heater4']['s']!='Off') {
            sw('heater4', 'Off', false, 17);
        }
        if ($d['heater3']['s']!='Off') {
            sw('heater3', 'Off', false, 18);
        }
        if ($d['heater2']['s']!='Off') {
            sw('heater2', 'Off', false, 19);
        }
    }
} else {
    //Niet thuis of slapen
    if ($d['heater4']['s']!='Off') {
        sw('heater4', 'Off', false, 20);
    }
    if ($d['heater3']['s']!='Off') {
        sw('heater3', 'Off', false, 21);
    }
    if ($d['heater2']['s']!='Off') {
        sw('heater2', 'Off', false, 22);
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
                ${'RSet'.$kamer}=18.0;
            } elseif ($d[$kamer.'_temp']['s']<16) {
                ${'RSet'.$kamer}=17.0;
            } elseif ($d[$kamer.'_temp']['s']<17) {
                ${'RSet'.$kamer}=16.0;
            }
        } elseif ($kamer=='tobi' && $d['gcal']['s']) {
            if ($d[$kamer.'_temp']['s']<15) {
                ${'RSet'.$kamer}=18.0;
            } elseif ($d[$kamer.'_temp']['s']<16) {
                ${'RSet'.$kamer}=17.0;
            } elseif ($d[$kamer.'_temp']['s']<17) {
                ${'RSet'.$kamer}=16.0;
            }
        }
    }
    if (round($d[$kamer.'Z']['s'], 1)!=round(${'RSet'.$kamer}, 1)) {
        //store('Tset'.$kamer.'Z',TIME);
        ud($kamer.'Z', 0, ${'RSet'.$kamer}.'.0');
    }
}
//lg('bigdif='.$bigdif.'|brander='.$brander.'|timebrander='.past('brander'));
if ($d['heating']['s']==3) {
    if ($bigdif<=-0.2
        && $d['brander']['s']=="Off"
        && past('brander')>180
    ) {
        sw(
            'brander',
            'On',
            false,
            'brander ON dif = '.$bigdif.' was off for '.convertToHours(
                past('brander')
            )
        );
    } elseif ($bigdif<=-0.1
        && $d['brander']['s']=="Off"
        && past('brander')>300
    ) {
        sw(
            'brander',
            'On',
            false,
            'brander ON dif = '.$bigdif.' was off for '.convertToHours(
                past('brander')
            )
        );
    } elseif ($bigdif<= 0
        && $d['brander']['s']=="Off"
        && past('brander')>600
    ) {
        sw(
            'brander',
            'On',
            false,
            'brander ON dif = '.$bigdif.' was off for '.convertToHours(
                past('brander')
            )
        );
    } elseif ($bigdif>= 0
        && $d['brander']['s']=="On"
        && past('brander')>180
    ) {
        sw(
            'brander',
            'Off',
            false,
            'brander OFF dif = '.$bigdif.' was on for '.convertToHours(
                past('brander')
            )
        );
    } elseif ($bigdif>=-0.1
        && $d['brander']['s']=="On"
        && past('brander')>300
    ) {
        sw(
            'brander',
            'Off',
            false,
            'brander OFF dif = '.$bigdif.' was on for '.convertToHours(
                past('brander')
            )
        );
    } elseif ($bigdif>=-0.2
        && $d['brander']['s']=="On"
        && past('brander')>900
    ) {
        sw(
            'brander',
            'Off',
            false,
            'brander OFF dif = '.$bigdif.' was on for '.convertToHours(
                past('brander')
            )
        );
    }
} elseif ($d['brander']['s']=='On') {
    sw('brander', 'Off', false, 'Brander OFF, heating < 3');
}
if ($bigdif!=$d['heating']['m']) {
    storemode('heating', $bigdif);
}

if ($d['deurbadkamer']['s']=='Open'
    && $d['badkamer_set']['s']!=10
    && (past('deurbadkamer')>57
    || $d['lichtbadkamer']['s']==0)
) {
    store('badkamer_set', 10);
    $d['badkamer_set']['s']=10.0;
} elseif ($d['deurbadkamer']['s']=='Closed') {
    $b7=past('8badkamer-7');
    $b7b=past('8Kamer-7');
    if ($b7b<$b7) {
        $b7=$b7b;
    }
    if ($d['buiten_temp']['s']<21
        && $d['lichtbadkamer']['s']>0
        && $d['badkamer_set']['s']!=22.5
        && ($b7>900
        && $d['heating']['s']>=2
        && (TIME>strtotime('5:00')
        && TIME<strtotime('10:00')))
    ) {
        store('badkamer_set', 22.5);
        $d['badkamer_set']['s']=22.5;
    } elseif ($b7>900
        && $d['lichtbadkamer']['s']==0
        && $d['buiten_temp']['s']<21
        && $d['Weg']['s']<2
    ) {
        if ($d['heating']['s']>1) {
            if (TIME>=strtotime('6:00') && TIME<=strtotime('6:30')) {
                $x=20;
                if ($d['badkamer_set']['s']!=$x) {
                    store('badkamer_set', $x);
                    $d['badkamer_set']['s']=$x;
                }
            } elseif (TIME>=strtotime('5:45') && TIME<=strtotime('6:30')) {
                $x=19.5;
                if ($d['badkamer_set']['s']!=$x) {
                    store('badkamer_set', $x);
                    $d['badkamer_set']['s']=$x;
                }
            } elseif (TIME>=strtotime('5:30') && TIME<=strtotime('6:30')) {
                $x=19;
                if ($d['badkamer_set']['s']!=$x) {
                    store('badkamer_set', $x);
                    $d['badkamer_set']['s']=$x;
                }
            } elseif (TIME>=strtotime('5:15') && TIME<=strtotime('6:30')) {
                $x=18.5;
                if ($d['badkamer_set']['s']!=$x) {
                    store('badkamer_set', $x);
                    $d['badkamer_set']['s']=$x;
                }
            } elseif (TIME>=strtotime('5:00') && TIME<=strtotime('6:30')) {
                $x=18;
                if ($d['badkamer_set']['s']!=$x) {
                    store('badkamer_set', $x);
                    $d['badkamer_set']['s']=$x;
                }
            } elseif (TIME>=strtotime('4:45') && TIME<=strtotime('6:30')) {
                $x=17.5;
                if ($d['badkamer_set']['s']!=$x) {
                    store('badkamer_set', $x);
                    $d['badkamer_set']['s']=$x;
                }
            } elseif (TIME>=strtotime('4:30') && TIME<=strtotime('6:30')) {
                $x=17;
                if ($d['badkamer_set']['s']!=$x) {
                    store('badkamer_set', $x);
                    $d['badkamer_set']['s']=$x;
                }
            } elseif (TIME>=strtotime('4:15') && TIME<=strtotime('6:30')) {
                $x=16.5;
                if ($d['badkamer_set']['s']!=$x) {
                    store('badkamer_set', $x);
                    $d['badkamer_set']['s']=$x;
                }
            } elseif (TIME>=strtotime('4:00') && TIME<=strtotime('6:30')) {
                $x=16;
                if ($d['badkamer_set']['s']!=$x) {
                    store('badkamer_set', $x);
                    $d['badkamer_set']['s']=$x;
                }
            } elseif (TIME>=strtotime('3:45') && TIME<=strtotime('6:30')) {
                $x=15.5;
                if ($d['badkamer_set']['s']!=$x) {
                    store('badkamer_set', $x);
                    $d['badkamer_set']['s']=$x;
                }
            } elseif (TIME>=strtotime('3:30') && TIME<=strtotime('6:30')) {
                $x=15;
                if ($d['badkamer_set']['s']!=$x) {
                    store('badkamer_set', $x);
                    $d['badkamer_set']['s']=$x;
                }
            } elseif (TIME>=strtotime('3:15') && TIME<=strtotime('6:30')) {
                $x=14.5;
                if ($d['badkamer_set']['s']!=$x) {
                    store('badkamer_set', $x);
                    $d['badkamer_set']['s']=$x;
                }
            } elseif (TIME>=strtotime('3:00') && TIME<=strtotime('6:30')) {
                $x=14;
                if ($d['badkamer_set']['s']!=$x) {
                    store('badkamer_set', $x);
                    $d['badkamer_set']['s']=$x;
                }
            }
        } elseif ($d['badkamer_set']['s']!=10) {
            store('badkamer_set', 10);
            $d['badkamer_set']['s']=10.0;
        }
    } elseif ($b7>900
        && ($d['lichtbadkamer']['s']==0
        && $d['badkamer_set']['s']!=10)
        || ($d['Weg']['s']==2
        && $d['badkamer_set']['s']!=10)
    ) {
        store('badkamer_set', 10);
        $d['badkamer_set']['s']=10.0;
    } elseif ($d['lichtbadkamer']['s']==0 && $d['badkamer_set']['s']!=10) {
        store('badkamer_set', 10);
        $d['badkamer_set']['s']=10.0;
    }
}
$difbadkamer=$d['badkamer_temp']['s']-$d['badkamer_set']['s'];
if ($difbadkamer<=-1) {
    if ($d['deurbadkamer']['s']=='Closed'
        && $d['badkamervuur1']['s']!='On'
        && past('badkamervuur1')>30
        && $d['el']['s']<7200
    ) {
        sw('badkamervuur1', 'On');
    }
    if ($d['deurbadkamer']['s']=='Closed'
        && $d['badkamervuur2']['s']!='On'
        && past('badkamervuur2')>30
        && $d['lichtbadkamer']['s']>0
        && $d['el']['s']<6800
    ) {
        sw('badkamervuur2', 'On');
    }
} elseif ($difbadkamer<= 0) {
    if ($d['deurbadkamer']['s']=='Closed'
        && $d['badkamervuur1']['s']!='On'
        && past('badkamervuur1')>30
        && $d['el']['s']<7200
    ) {
        sw('badkamervuur1', 'On');
    }
    if (($d['badkamervuur2']['s']!='Off'
        && past('badkamervuur2')>30)
        || $d['el']['s']>7500
    ) {
        sw('badkamervuur2', 'Off');
    }
} else {
    if (($d['badkamervuur2']['s']!='Off'
        && past('badkamervuur2')>30)
        || $d['el']['s']>7500
    ) {
        sw('badkamervuur2', 'Off');
    }
    if (($d['badkamervuur1']['s']!='Off'
        && past('badkamervuur1')>30)
        || $d['el']['s']>8200
    ) {
        sw('badkamervuur1', 'Off');
    }
}
if ($d['minmaxtemp']['m']>19) {
    if ($d['zolder_set']['s']>4) {
        $d['zolder_set']['s']=4;
        store('zolder_set', 4);
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
    sw(
        'zoldervuur',
        'On',
        false,
        'zoldervuur1 ON dif = '.$difzolder.' was off for '.
        convertToHours(past('zoldervuur')).', verbruik: '.$d['el']['s']
    );
    lg('>>>>>>>>>> difzolder = '.$difzolder);
} elseif ($difzolder<=-0.1
    && $d['zoldervuur']['s']!="On"
    && past('zoldervuur')>90
    && $d['el']['s']<4800
    && $d['heating']['s']>=2
    && $d['Weg']['s']==0
) {
    sw(
        'zoldervuur',
        'On',
        false,
        'zoldervuur2 ON dif = '.$difzolder.' was off for '.
        convertToHours(past('zoldervuur')).', verbruik: '.$d['el']['s']
    );
    lg('>>>>>>>>>> difzolder = '.$difzolder);
} elseif ($difzolder<= 0
    && $d['zoldervuur']['s']!="On"
    && past('zoldervuur')>180
    && $d['el']['s']<4800
    && $d['heating']['s']>=2
    && $d['Weg']['s']==0
) {
    sw(
        'zoldervuur',
        'On',
        false,
        'zoldervuur3 ON dif = '.$difzolder.' was off for '.
        convertToHours(past('zoldervuur')).', verbruik: '.$d['el']['s']
    );
    lg('>>>>>>>>>> difzolder = '.$difzolder);
} elseif (($difzolder>= 0
    && $d['zoldervuur']['s']!="Off"
    && past('zoldervuur')>30)
    || ($d['zoldervuur']['s']!="Off"
    && ($d['el']['s']>6600 || $d['Weg']['s']>0))
) {
    sw(
        'zoldervuur',
        'Off',
        false,
        'zoldervuur4 OFF dif = '.$difzolder.' was on for '.
        convertToHours(past('zoldervuur')).', verbruik: '.$d['el']['s']
    );
    lg('>>>>>>>>>> difzolder = '.$difzolder);
} elseif (($difzolder>=-0.3
    && $d['zoldervuur']['s']!="Off"
    && past('zoldervuur')>120)
    || ($d['zoldervuur']['s']!="Off"
    && ($d['el']['s']>6600 || $d['Weg']['s']>0))
) {
    sw(
        'zoldervuur',
        'Off',
        false,
        'zoldervuur5 OFF dif = '.$difzolder.' was on for '.
        convertToHours(past('zoldervuur')).', verbruik: '.$d['el']['s']
    );
    lg('>>>>>>>>>> difzolder = '.$difzolder);
} elseif (($difzolder>=-0.5
    && $d['zoldervuur']['s']!="Off"
    && past('zoldervuur')>180)
    || ($d['zoldervuur']['s']!="Off"
    && ($d['el']['s']>6600 || $d['Weg']['s']>0))
) {
    sw(
        'zoldervuur',
        'Off',
        false,
        'zoldervuur6 OFF dif = '.$difzolder.' was on for '.
        convertToHours(past('zoldervuur')).', verbruik: '.$d['el']['s']
    );
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