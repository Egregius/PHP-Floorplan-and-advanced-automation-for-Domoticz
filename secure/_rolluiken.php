<?php
/**
 * Pass2PHP rolluiken
 * php version 7.3.3-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
$msg='Rolluiken__';
$boven=array(
    'Rtobi',
    'Ralex',
    'RkamerL',
    'RkamerR'
);
$beneden=array(
    'Rbureel',
    'RkeukenL',
    'RkeukenR'
);
$benedena=array(
    'Rliving',
    'Rbureel',
    'RkeukenL',
    'RkeukenR'
);

if ($d['heating']['s']>=2) {
    $msg.='Heating__';
    if (TIME<strtotime('6:00')
        ||TIME>=strtotime('22:00')
    ) {
        $dag='nacht';
    }
    if (TIME>=strtotime('6:00')
        &&TIME<strtotime('8:30')
    ) {
        $dag='ochtend';
    }
    if (TIME>=strtotime('8:30')
        &&TIME<strtotime('12:30')
    ) {
        $dag='AM';
    }
    if (TIME>=strtotime('12:30')
        &&TIME<strtotime('17:00')
    ) {
        $dag='PM';
    }
    if (TIME>=strtotime('17:00')
        &&TIME<strtotime('22:00')
    ) {
        $dag='avond';
    }
    $msg.=$dag.'__';
    if ($d['Weg']['s']==0) {
        $msg.='Thuis__';
        if ($dag=='nacht') {

        } elseif ($dag=='ochtend'
            &&past('pirliving')<7200
        ) {
            if ($d['auto']['m']
                &&$d['zon']['s']==0
            ) {
                $msg.='ZonOP && Zon = 0__';
                if ($d['Rliving']['m']==0
                    && $d['Rliving']['s']>0
                    && past('Rliving')>900
                ) {
                    sl('Rliving', 0);
                    $msg.='Rliving open__';
                    sleep(1);
                }
                foreach ($beneden as $i) {
                    if ($d[$i]['m']==0
                        && $d[$i]['s']>27
                        && past('T'.$i)>900
                    ) {
                         sl($i, 27);
                         $msg.=$i.' half open__';
                    }
                }
            } elseif ($d['auto']['m']==true) {
                $msg.='ZonOP && Zon = '.$d['zon']['s'].'__';
                foreach ($benedena as $i) {
                    if ($d[$i]['m']==0
                        && $d[$i]['s']>0
                        && past($i)>120
                    ) {
                        sl($i, 0);
                        $msg.=$i.' open__';
                    }
                }
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0
                        && $d[$i]['s']>31
                        && past($i)>900
                    ) {
                         sl($i, 31);
                         $msg.=$i.' half open__';
                    }
                }
            }
        } elseif ($dag=='AM') {
            foreach ($benedena as $i) {
                if ($d[$i]['m']==0
                    && $d[$i]['s']>0
                    && past($i)>900
                ) {
                    sl($i, 0);
                    $msg.=$i.' open__';
                }
            }
            foreach ($boven as $i) {
                if ($i=='RkamerL'||$i=='RkamerR') {
                    $temp=$d[substr($i, 1, -1).'_temp']['s'];
                } else {
                    $temp=$d[substr($i, 1).'_temp']['s'];
                }
                if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                    sl($i, 0);
                    $msg.=$i.' open__';
                }
            }
        } elseif ($dag=='PM') {

        } elseif ($dag=='avond') {
            if ($d['auto']['m']&&$d['zon']['s']<50) {
                $msg.='zonOP, zon < 50 : '.$d['zon']['s'].'__';
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<70 && past($i)>900) {
                        sl($i, 100);
                        $msg.=$i.' Dicht__';
                    }
                }
                if ($d['zon']['s']==0&&past('zon')>900) {
                    foreach ($beneden as $i) {
                        if ($d[$i]['m']==0 && $d[$i]['s']<27 && past($i)>900) {
                            sl($i, 27);
                            $msg.=$i.' 27__';
                        }
                    }
                }
            } elseif ($d['auto']['m']) {
                $msg.='zonOP, zon = '.$d['zon']['s'].'__';
            } else {
                $msg.='Zononder __';
                foreach ($beneden as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                        sl($i, 100);
                        $msg.=$i.' Dicht__';
                    }
                }
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                         sl($i, 100);
                         $msg.=$i.' Dicht__';
                    }
                }
            }
        }
    } elseif ($d['Weg']['s']==1) {
        $msg.='Slapen__';

    } elseif ($d['Weg']['s']==2) {
        $msg.='Weg__';
        if ($dag=='nacht') {
            foreach ($benedena as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                    sl($i, 100);
                    $msg.=$i.' Dicht__';
                }
            }
            foreach ($boven as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                    sl($i, 100);
                    $msg.=$i.' Dicht__';
                }
            }
        } elseif ($dag=='ochtend') {
            if ($d['auto']['m']&&$d['zon']['s']==0) {
                $msg.='ZonOP && Zon = 0__';
                foreach ($benedena as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']!=30 && past($i)>900) {
                        sl($i, 30);
                        $msg.=$i.' half open__';
                    }
                }
            } elseif ($d['auto']['m']) {
                $msg.='ZonOP && Zon = '.$d['zon']['s'].'__';
                foreach ($benedena as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                        sl($i, 0);
                        $msg.=$i.' open__';
                    }
                }
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                         sl($i, 0);
                         $msg.=$i.' open__';
                    }
                }
            }
        } elseif ($dag=='AM') {
            foreach ($benedena as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                    sl($i, 0);
                    $msg.=$i.' open__';
                }
            }
            foreach ($boven as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                    sl($i, 0);
                    $msg.=$i.' open__';
                }
            }
        } elseif ($dag=='PM') {

        } elseif ($dag=='avond') {
            if ($d['auto']['m']&&$d['zon']['s']==0&&past('zon')>600) {
                $msg.='zonOP && Zon = 0__';
                foreach ($benedena as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<31 && past($i)>900) {
                        sl($i, 40);
                        $msg.=$i.' half dicht__';
                    }
                }
            } elseif ($d['auto']['m']&&$d['zon']['s']<50) {
                $msg.='zonOP && Zon < 50 : '.$d['zon']['s'].'__';
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<70 && past($i)>900) {
                        sl($i, 100);
                        $msg.=$i.' Dicht__';
                    }
                }
            } elseif ($d['auto']['m']) {
                $msg.='zonOP, zon = '.$d['zon']['s'].'__';
            } else {
                $msg.='Zononder __';
                foreach ($benedena as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                        sl($i, 100);
                        $msg.=$i.' Dicht__';
                    }
                }
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                        sl($i, 100);
                        $msg.=$i.' Dicht__';
                    }
                }
            }
        }
    }
} elseif ($d['heating']['s']==0) {
    $msg.='Neutral__';
    if (TIME<strtotime('6:00')||TIME>=strtotime('22:00')) {
        $dag='nacht';
    }
    if (TIME>=strtotime('6:00')&&TIME<strtotime('8:30')) {
        $dag='ochtend';
    }
    if (TIME>=strtotime('8:30')&&TIME<strtotime('10:30')) {
        $dag='AM';
    }
    if (TIME>=strtotime('10:30')&&TIME<strtotime('18:00')) {
        $dag='PM';
    }
    if (TIME>=strtotime('18:00')&&TIME<strtotime('22:00')) {
        $dag='avond';
    }
    $msg.=$dag.'__';
    if ($d['Weg']['s']==0) {
        $msg.='Thuis__';
        if ($dag=='nacht') {
            foreach ($benedena as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                    sl($i, 100);
                    $msg.=$i.' Dicht__';
                }
            }
            foreach ($boven as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                    sl($i, 100);
                    $msg.=$i.' Dicht__';
                }
            }
        } elseif ($dag=='ochtend'&&past('pirliving')<4000) {
            if ($d['auto']['m']&&$d['zon']['s']==0) {
                $msg.='ZonOP && Zon = 0__';
                if ($d['Rliving']['m']==0  && $Rliving>0 && $TRliving>$kwartier) {
                    sl('Rliving', 0);$msg.='Rliving open__';
                }
                foreach ($beneden as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>27 && past($i)>900) {
                         sl($i, 27);
                         $msg.=$i.' half open__';
                    }
                }

            } elseif ($d['auto']['m']) {
                $msg.='ZonOP && Zon = '.$d['zon']['s'].'__';
                foreach ($benedena as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                        sl($i, 0);
                        $msg.=$i.' open__';
                    }
                }
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                         sl($i, 0);
                         $msg.=$i.' open__';
                    }
                }
            }
        } elseif ($dag=='AM') {
            foreach ($benedena as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                    sl($i, 0);
                    $msg.=$i.' open__';
                }
            }
            foreach ($boven as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900 ) {
                    sl($i, 0);
                    $msg.=$i.' open__';
                }
            }
        } elseif ($dag=='PM') {

        } elseif ($dag=='avond') {

        }
    } elseif ($d['Weg']['s']==1) {
        $msg.='Slapen__';
        foreach ($benedena as $i) {
            if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                sl($i, 100);
                $msg.=$i.' Dicht__';
            }
        }
        foreach ($boven as $i) {
            if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                sl($i, 100);
                $msg.=$i.' Dicht__';
            }
        }
    } elseif ($d['Weg']['s']==2) {
        $msg.='Weg__';
        if ($dag=='nacht') {
            foreach ($benedena as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                    sl($i, 100);
                    $msg.=$i.' Dicht__';
                }
            }
            foreach ($boven as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                    if ($i=='Rtobi'||$i=='RkamerR') {
                        sl($i, 85);
                        $msg.=$i.' 85';
                    } else {
                        sl($i, 100);
                        $msg.=$i.' 100__';
                    }
                }
            }
        } elseif ($dag=='ochtend') {
            if ($d['auto']['m']&&$d['zon']['s']==0) {
                $msg.='ZonOP && Zon = 0__';
                foreach ($benedena as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>27 && past($i)>900) {
                        sl($i, 27);
                        $msg.=$i.' half open__';
                    }
                }
            } elseif ($d['auto']['m']) {
                $msg.='ZonOP && Zon = '.$d['zon']['s'].'__';
                foreach ($benedena as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                        sl($i, 0);
                        $msg.=$i.' open__';
                    }
                }
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>27 && past($i)>900) {
                         sl($i, 27);
                         $msg.=$i.' half open__';
                    }
                }
            }
        } elseif ($dag=='AM') {
            foreach ($benedena as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                    sl($i, 0);
                    $msg.=$i.' open__';
                }
            }
            foreach ($boven as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                    sl($i, 0);
                    $msg.=$i.' open__';
                }
            }
        } elseif ($dag=='PM') {

        } elseif ($dag=='avond') {
            if ($d['auto']['m']&&$d['zon']['s']==0&&past('zon')>600) {
                $msg.='zonOP && Zon = 0__';
                foreach ($benedena as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<31 && past($i)>900) {
                        sl($i, 31);
                        $msg.=$i.' half toe__';
                    }
                }
            } elseif ($d['auto']['m']&&$d['zon']['s']<50) {
                $msg.='zonOP && Zon < 50 : '.$d['zon']['s'].'__';
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                        if ($i=='Rtobi'||$i=='RkamerR') {
                            sl($i, 85);
                            $msg.=$i.' 85';
                        } else {
                            sl($i, 100);
                            $msg.=$i.' 100__';
                        }
                    } elseif ($d[$i]['m']==0 && $d[$i]['s']==100 && past($i)>900) {
                        if ($i=='RkamerL'||$i=='RkamerR') {
                            $temp=${substr($i, 1, -1).'_temp'};
                        } else {
                            $temp=${substr($i, 1).'_temp'};
                        }
                        if ($temp>19&&$temp>$buiten_temp+1) {
                            sl($i, 80);$msg.=$i.' 78 om af te koelen__';
                        }
                    }
                }
            } elseif ($d['auto']['m']) {
                $msg.='zonOP, zon = '.$d['zon']['s'].'__';
            } else {
                $msg.='Zononder __';
                foreach ($benedena as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                        sl($i, 100);
                        $msg.=$i.' Dicht__';
                    }
                }
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                        if ($i=='Rtobi'||$i=='RkamerR') {
                            sl($i, 85);
                            $msg.=$i.' 85';
                        } else {
                            sl($i, 100);
                            $msg.=$i.' 100__';
                        }
                    } elseif ($d[$i]['m']==0 && $d[$i]['s']==100 && past($i)>900) {
                        if ($i=='RkamerL'||$i=='RkamerR') {
                            $temp=${substr($i, 1, -1).'_temp'};
                        } else {
                            $temp=${substr($i, 1).'_temp'};
                        }
                        if ($temp>19&&$temp>$buiten_temp+1) {
                            sl($i, 80);
                            $msg.=$i.' 78 om af te koelen__';
                        }
                    }
                }
            }
        }
    }
} elseif ($d['heating']['s']==1) {
    $msg.='Cooling__';
    if (TIME<strtotime('6:00')||TIME>=strtotime('22:00')) {
        $dag='nacht';
    }
    if (TIME>=strtotime('6:00')&&TIME<strtotime('8:30')) {
        $dag='ochtend';
    }
    if (TIME>=strtotime('8:30')&&TIME<strtotime('10:30')) {
        $dag='AM';
    }
    if (TIME>=strtotime('10:30')&&TIME<strtotime('20:00')) {
        $dag='PM';
    }
    if (TIME>=strtotime('20:00')&&TIME<strtotime('22:00')) {
        $dag='avond';
    }
    $msg.=$dag.'__';
    if ($d['Weg']['s']==0) {
        $msg.='Thuis__';
        if ($dag=='nacht') {

        } elseif ($dag=='ochtend'&&past('pirliving')<4000) {
            if ($d['auto']['m']&&$d['zon']['s']==0) {
                $msg.='ZonOP && Zon = 0__';
                foreach ($beneden as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>27 && past($i)>900) {
                        sl($i, 27);$msg.=$i.' half open__';
                    }
                }
                if ($d['Rliving']['m']==0
                    && $d['Rliving']['s']==0 >0
                    && $TRliving>$kwartier
                ) {
                    sl('Rliving', 0);$msg.='Rliving open__';
                }
            } elseif ($d['auto']['m']) {
                $msg.='ZonOP && Zon = '.$d['zon']['s'].'__';
                foreach ($benedena as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                        sl($i, 0);$msg.=$i.' open__';
                    }
                }
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                         sl($i, 0);$msg.=$i.' open__';
                    }
                }
            }
        } elseif ($dag=='AM') {
            foreach ($benedena as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                    sl($i, 0);$msg.=$i.' open__';
                }
            }
            foreach ($boven as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                    sl($i, 0);$msg.=$i.' open__';
                }
            }
        } elseif ($dag=='PM') {

        } elseif ($dag=='avond') {

        }
    } elseif ($d['Weg']['s']==1) {
        $msg.='Slapen__';
    } elseif ($d['Weg']['s']==2) {
        $msg.='Weg__';
        if ($dag=='nacht') {
            foreach ($benedena as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                    sl($i, 100);
                    $msg.=$i.' Dicht__';
                }
            }
            foreach ($boven as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                    if ($i=='Rtobi'||$i=='RkamerR') {
                        sl($i, 90);
                        $msg.=$i.' 90';
                    } else {
                        sl($i, 100);
                        $msg.=$i.' 100__';
                    }
                }
            }
        } elseif ($dag=='ochtend') {
            if ($d['auto']['m']&&$d['zon']['s']==0) {
                $msg.='ZonOP && Zon = 0__';
                foreach ($benedena as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>30 && past($i)>900) {
                        sl($i, 30);$msg.=$i.' half open__';
                    }
                }
            } elseif ($d['auto']['m']) {
                $msg.='ZonOP && Zon = '.$d['zon']['s'].'__';
                foreach ($benedena as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                        sl($i, 0);$msg.=$i.' open__';
                    }
                }
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>30 && past($i)>900) {
                         sl($i, 30);$msg.=$i.' half open__';
                    }
                }
            }
        } elseif ($dag=='AM') {
            foreach ($benedena as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                    sl($i, 0);$msg.=$i.' open__';
                }
            }
            foreach ($boven as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                    sl($i, 0);$msg.=$i.' open__';
                }
            }
        } elseif ($dag=='PM') {

        } elseif ($dag=='avond') {
            if ($d['auto']['m']&&$d['zon']['s']==0&&past('zon')>600) {
                $msg.='zonOP && Zon = 0__';
                foreach ($benedena as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<30 && past($i)>900) {
                        sl($i, 30);$msg.=$i.' half toe__';
                    }
                }
            } elseif ($d['auto']['m']&&$d['zon']['s']<50) {
                $msg.='zonOP && Zon < 50 : '.$d['zon']['s'].'__';
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                        if ($i=='Rtobi'||$i=='RkamerR') {
                            sl($i, 85);
                            $msg.=$i.' 85';
                        } else {
                            sl($i, 100);
                            $msg.=$i.' 100__';
                        }
                    } elseif ($d[$i]['m']==0 && $d[$i]['s']==100 && past($i)>900) {
                        if ($i=='RkamerL'||$i=='RkamerR') {
                            $temp=${substr($i, 1, -1).'_temp'};
                        } else {
                            $temp=${substr($i, 1).'_temp'};
                        }
                        if ($temp>19&&$temp>$buiten_temp+1) {
                            sl($i, 80);$msg.=$i.' 78 om af te koelen__';
                        }
                    }
                }
            } elseif ($d['auto']['m']) {
                $msg.='zonOP, zon = '.$d['zon']['s'].'__';
            } else {
                $msg.='Zononder __';
                foreach ($benedena as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                        sl($i, 100);$msg.=$i.' Dicht__';
                    }
                }
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                        if ($i=='Rtobi'||$i=='RkamerR') {
                            sl($i, 85);
                            $msg.=$i.'85';
                        } else {
                            sl($i, 100);
                            $msg.=$i.' 100__';
                        }
                    } elseif ($d[$i]['m']==0 && $d[$i]['s']==100 && past($i)>900) {
                        if ($i=='RkamerL'||$i=='RkamerR') {
                            $temp=${substr($i, 1, -1).'_temp'};
                        } else {
                            $temp=${substr($i, 1).'_temp'};
                        }
                        if ($temp>19&&$temp>$buiten_temp+1) {
                            sl($i, 80);$msg.=$i.' 78 om af te koelen__';
                        }
                    }
                }
            }
        }
    }
}
echo str_replace('__', ' | ', $msg);
if (strlen($msg)>=60) {
    lg(str_replace('__', ' | ', $msg));
}