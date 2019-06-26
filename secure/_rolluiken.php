<?php
/**
 * Pass2PHP rolluiken
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
$user='rolluiken';
$boven=array('Rtobi','Ralex','RkamerL','RkamerR');
$bovenv=array('RkamerL','RkamerR');
$bovena=array('Rtobi','Ralex');
$beneden=array('Rbureel','RkeukenL','RkeukenR');
$benedenv=array('RkeukenL','RkeukenR');
$benedena=array('Rliving','Rbureel');
$benedenall=array('Rliving','Rbureel','RkeukenL','RkeukenR');

if ($d['heating']['s']>=2) {
    if (TIME<strtotime('6:00')||TIME>=strtotime('22:00')) {
        $dag='nacht';
    }
    if (TIME>=strtotime('6:00')&&TIME<strtotime('8:30')) {
        $dag='ochtend';
    }
    if (TIME>=strtotime('8:30')&&TIME<strtotime('12:30')) {
        $dag='AM';
    }
    if (TIME>=strtotime('12:30')&&TIME<strtotime('17:00')) {
        $dag='PM';
    }
    if (TIME>=strtotime('17:00')&&TIME<strtotime('22:00')) {
        $dag='avond';
    }
    if ($d['Weg']['s']==0) {
        if ($dag=='nacht') {
            foreach ($benedenall as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                    sl($i, 100, basename(__FILE__).':'.__LINE__);
                }
            }
            foreach ($boven as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                    sl($i, 100, basename(__FILE__).':'.__LINE__);
                }
            }
        } elseif ($dag=='ochtend'
            &&past('pirliving')<7200
        ) {
            if ($d['auto']['m']
                &&$d['zon']['s']==0
            ) {
                if ($d['Rliving']['m']==0
                    && $d['Rliving']['s']>0
                    && past('Rliving')>900
                ) {
                    sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
                    sleep(1);
                }
                foreach ($beneden as $i) {
                    if ($d[$i]['m']==0
                        && $d[$i]['s']>27
                        && past('T'.$i)>900
                    ) {
                         sl($i, 27, basename(__FILE__).':'.__LINE__);
                    }
                }
            } elseif ($d['auto']['m']==true) {
                foreach ($benedenall as $i) {
                    if ($d[$i]['m']==0
                        && $d[$i]['s']>0
                        && past($i)>120
                    ) {
                        sl($i, 0, basename(__FILE__).':'.__LINE__);
                    }
                }
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0
                        && $d[$i]['s']>31
                        && past($i)>900
                    ) {
                         sl($i, 31, basename(__FILE__).':'.__LINE__);
                    }
                }
            }
        } elseif ($dag=='AM') {
            foreach ($benedenall as $i) {
                if ($d[$i]['m']==0
                    && $d[$i]['s']>0
                    && past($i)>900
                ) {
                    sl($i, 0, basename(__FILE__).':'.__LINE__);
                }
            }
            foreach ($boven as $i) {
                if ($i=='RkamerL'||$i=='RkamerR') {
                    $temp=$d[substr($i, 1, -1).'_temp']['s'];
                } else {
                    $temp=$d[substr($i, 1).'_temp']['s'];
                }
                if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                    sl($i, 0, basename(__FILE__).':'.__LINE__);
                }
            }
        } elseif ($dag=='PM') {

        } elseif ($dag=='avond') {
            if ($d['auto']['m']&&$d['zon']['s']<50) {
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<70 && past($i)>900) {
                        sl($i, 100, basename(__FILE__).':'.__LINE__);
                    }
                }
                if ($d['zon']['s']==0&&past('zon')>900) {
                    foreach ($beneden as $i) {
                        if ($d[$i]['m']==0 && $d[$i]['s']<27 && past($i)>900) {
                            sl($i, 27, basename(__FILE__).':'.__LINE__);
                        }
                    }
                }
            } elseif ($d['auto']['m']) {
            } else {
                foreach ($beneden as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                        sl($i, 100, basename(__FILE__).':'.__LINE__);
                    }
                }
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                         sl($i, 100, basename(__FILE__).':'.__LINE__);
                    }
                }
            }
        }
    } elseif ($d['Weg']['s']==1) {
    } elseif ($d['Weg']['s']==2) {
        if ($dag=='nacht') {
            foreach ($benedenall as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                    sl($i, 100, basename(__FILE__).':'.__LINE__);
                }
            }
            foreach ($boven as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                    sl($i, 100, basename(__FILE__).':'.__LINE__);
                }
            }
        } elseif ($dag=='ochtend') {
            if ($d['auto']['m']&&$d['zon']['s']==0) {
                foreach ($benedenall as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']!=30 && past($i)>900) {
                        sl($i, 30, basename(__FILE__).':'.__LINE__);
                    }
                }
            } elseif ($d['auto']['m']) {
                foreach ($benedenall as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                        sl($i, 0, basename(__FILE__).':'.__LINE__);
                    }
                }
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                         sl($i, 0, basename(__FILE__).':'.__LINE__);
                    }
                }
            }
        } elseif ($dag=='AM') {
            foreach ($benedenall as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                    sl($i, 0, basename(__FILE__).':'.__LINE__);
                }
            }
            foreach ($boven as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                    sl($i, 0, basename(__FILE__).':'.__LINE__);
                }
            }
        } elseif ($dag=='PM') {

        } elseif ($dag=='avond') {
            if ($d['auto']['m']&&$d['zon']['s']==0&&past('zon')>600) {
                $msg.='zonOP && Zon = 0__';
                foreach ($benedenall as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<31 && past($i)>900) {
                        sl($i, 40, basename(__FILE__).':'.__LINE__);
                        $msg.=$i.' half dicht__';
                    }
                }
            } elseif ($d['auto']['m']&&$d['zon']['s']<50) {
                $msg.='zonOP && Zon < 50 : '.$d['zon']['s'].'__';
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<70 && past($i)>900) {
                        sl($i, 100, basename(__FILE__).':'.__LINE__);
                        $msg.=$i.' Dicht__';
                    }
                }
            } elseif ($d['auto']['m']) {
            } else {
                foreach ($benedenall as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                        sl($i, 100, basename(__FILE__).':'.__LINE__);
                        $msg.=$i.' Dicht__';
                    }
                }
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                        sl($i, 100, basename(__FILE__).':'.__LINE__);
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
    if ($d['Weg']['s']==0) {
        if ($dag=='nacht') {
        } elseif ($dag=='ochtend'&&(past('pirliving')<4000||past('8badkamer-8')<4000)) {
            if ($d['auto']['m']&&$d['zon']['s']==0) {
                if ($d['Rliving']['m']==0  && $d['Rliving']['s']>0 && past('Rliving')>900) {
                    sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
                }
                foreach ($beneden as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>27 && past($i)>900) {
                         sl($i, 27, basename(__FILE__).':'.__LINE__);
                    }
                }

            } elseif ($d['auto']['m']) {
                foreach ($benedenall as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                        sl($i, 0, basename(__FILE__).':'.__LINE__);
                    }
                }
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                         sl($i, 0, basename(__FILE__).':'.__LINE__);
                    }
                }
            }
        } elseif ($dag=='AM') {
            foreach ($benedenall as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                    sl($i, 0, basename(__FILE__).':'.__LINE__);
                }
            }
            foreach ($boven as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900 ) {
                    sl($i, 0, basename(__FILE__).':'.__LINE__);
                }
            }
        } elseif ($dag=='PM') {

        } elseif ($dag=='avond') {

        }
    } elseif ($d['Weg']['s']==1) {
        foreach ($benedenall as $i) {
            if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                sl($i, 100, basename(__FILE__).':'.__LINE__);
            }
        }
    } elseif ($d['Weg']['s']==2) {
        if ($dag=='nacht') {
            foreach ($benedenall as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                    sl($i, 100, basename(__FILE__).':'.__LINE__);
                }
            }
            foreach ($boven as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                    if ($i=='Rtobi'||$i=='RkamerR') {
                        sl($i, 85, basename(__FILE__).':'.__LINE__);
                    } else {
                        sl($i, 100, basename(__FILE__).':'.__LINE__);
                    }
                }
            }
        } elseif ($dag=='ochtend') {
            if ($d['auto']['m']&&$d['zon']['s']==0) {
                foreach ($benedenall as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>27 && past($i)>900) {
                        sl($i, 27, basename(__FILE__).':'.__LINE__);
                    }
                }
            } elseif ($d['auto']['m']) {
                foreach ($benedenall as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                        sl($i, 0, basename(__FILE__).':'.__LINE__);
                    }
                }
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>27 && past($i)>900) {
                         sl($i, 27, basename(__FILE__).':'.__LINE__);
                    }
                }
            }
        } elseif ($dag=='AM') {
            foreach ($benedenall as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                    sl($i, 0, basename(__FILE__).':'.__LINE__);
                }
            }
            foreach ($boven as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                    sl($i, 0, basename(__FILE__).':'.__LINE__);
                }
            }
        } elseif ($dag=='PM') {

        } elseif ($dag=='avond') {
            if ($d['auto']['m']&&$d['zon']['s']==0&&past('zon')>600) {
                foreach ($benedenall as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<31 && past($i)>900) {
                        sl($i, 31, basename(__FILE__).':'.__LINE__);
                    }
                }
            } elseif ($d['auto']['m']&&$d['zon']['s']<50) {
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                        if ($i=='Rtobi'||$i=='RkamerR') {
                            sl($i, 85, basename(__FILE__).':'.__LINE__);
                        } else {
                            sl($i, 100, basename(__FILE__).':'.__LINE__);
                        }
                    } elseif ($d[$i]['m']==0 && $d[$i]['s']==100 && past($i)>900) {
                        if ($i=='RkamerL'||$i=='RkamerR') {
                            $temp=${substr($i, 1, -1).'_temp'};
                        } else {
                            $temp=${substr($i, 1).'_temp'};
                        }
                        if ($temp>19&&$temp>$buiten_temp+1) {
                            sl($i, 80, basename(__FILE__).':'.__LINE__);
                        }
                    }
                }
            } elseif ($d['auto']['m']) {
            } else {
                foreach ($benedenall as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                        sl($i, 100, basename(__FILE__).':'.__LINE__);
                    }
                }
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                        if ($i=='Rtobi'||$i=='RkamerR') {
                            sl($i, 85, basename(__FILE__).':'.__LINE__);
                        } else {
                            sl($i, 100, basename(__FILE__).':'.__LINE__);
                        }
                    } elseif ($d[$i]['m']==0 && $d[$i]['s']==100 && past($i)>900) {
                        if ($i=='RkamerL'||$i=='RkamerR') {
                            $temp=${substr($i, 1, -1).'_temp'};
                        } else {
                            $temp=${substr($i, 1).'_temp'};
                        }
                        if ($temp>19&&$temp>$buiten_temp+1) {
                            sl($i, 80, basename(__FILE__).':'.__LINE__);
                        }
                    }
                }
            }
        }
    }
} elseif ($d['heating']['s']==1) {
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
    if ($d['Weg']['s']==0) {
        if ($dag=='nacht') {
        } elseif ($dag=='ochtend'&&past('pirliving')<4000) {
            if ($d['auto']['m']&&$d['zon']['s']==0) {
                if ($d['Rliving']['m']==0
                    && $d['Rliving']['s']==0
                    && past('Rliving')>120
                ) {
                    sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
                }
                foreach ($benedenv as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>27 && past($i)>900) {
                        sl($i, 27, basename(__FILE__).':'.__LINE__);
                    }
                }
                foreach ($benedena as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                        sl($i, 0, basename(__FILE__).':'.__LINE__);
                    }
                }
            } elseif ($d['auto']['m']) {
                foreach ($benedenall as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                        sl($i, 0, basename(__FILE__).':'.__LINE__);
                    }
                }
                foreach ($bovenv as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>35 && past($i)>900) {
                         sl($i, 35, basename(__FILE__).':'.__LINE__);
                    }
                }
                foreach ($bovena as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                         sl($i, 0, basename(__FILE__).':'.__LINE__);
                    }
                }
            }
        } elseif ($dag=='AM') {
            foreach ($benedenall as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                    sl($i, 0, basename(__FILE__).':'.__LINE__);
                }
            }
            foreach ($bovenv as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                    sl($i, 0, basename(__FILE__).':'.__LINE__);
                }
            }
        } elseif ($dag=='PM') {

        } elseif ($dag=='avond') {

        }
    } elseif ($d['Weg']['s']==1) {
        foreach ($benedenall as $i) {
            if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                sl($i, 85, basename(__FILE__).':'.__LINE__);
            }
        }
    } elseif ($d['Weg']['s']==2) {
        if ($dag=='nacht') {
            foreach ($benedenall as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                    sl($i, 100, basename(__FILE__).':'.__LINE__);
                }
            }
            foreach ($boven as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                    sl($i, 100, basename(__FILE__).':'.__LINE__);
                }
            }
        } elseif ($dag=='ochtend') {
            if ($d['auto']['m']&&$d['zon']['s']==0) {
                foreach ($benedenall as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>30 && past($i)>900) {
                        sl($i, 30, basename(__FILE__).':'.__LINE__);
                    }
                }
            } elseif ($d['auto']['m']) {
                foreach ($benedenall as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                        sl($i, 0, basename(__FILE__).':'.__LINE__);
                    }
                }
                foreach ($bovenv as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>35 && past($i)>900) {
                         sl($i, 35, basename(__FILE__).':'.__LINE__);
                    }
                }
                foreach ($bovena as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                         sl($i, 0, basename(__FILE__).':'.__LINE__);
                    }
                }
            }
        } elseif ($dag=='AM') {
            foreach ($benedenall as $i) {
                if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
                    sl($i, 0, basename(__FILE__).':'.__LINE__);
                }
            }
            foreach ($bovenv as $i) {
				if ($d[$i]['m']==0 && $d[$i]['s']>35 && past($i)>900) {
					 sl($i, 35, basename(__FILE__).':'.__LINE__);
				}
			}
			foreach ($bovena as $i) {
				if ($d[$i]['m']==0 && $d[$i]['s']>0 && past($i)>900) {
					 sl($i, 0, basename(__FILE__).':'.__LINE__);
				}
			}
        } elseif ($dag=='PM') {

        } elseif ($dag=='avond') {
            if ($d['auto']['m']&&$d['zon']['s']==0&&past('zon')>600) {
                foreach ($benedenall as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<30 && past($i)>900) {
                        sl($i, 30, basename(__FILE__).':'.__LINE__);
                    }
                }
            } elseif ($d['auto']['m']&&$d['zon']['s']<50) {
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                        if ($i=='Rtobi'||$i=='RkamerR') {
                            sl($i, 85, basename(__FILE__).':'.__LINE__);
                        } else {
                            sl($i, 100, basename(__FILE__).':'.__LINE__);
                        }
                    } elseif ($d[$i]['m']==0 && $d[$i]['s']==100 && past($i)>900) {
                        if ($i=='RkamerL'||$i=='RkamerR') {
                            $temp=${substr($i, 1, -1).'_temp'};
                        } else {
                            $temp=${substr($i, 1).'_temp'};
                        }
                        if ($temp>19&&$temp>$buiten_temp+1) {
                            sl($i, 80, basename(__FILE__).':'.__LINE__);
                        }
                    }
                }
            } elseif ($d['auto']['m']) {
            } else {
                foreach ($benedenall as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                        sl($i, 100, basename(__FILE__).':'.__LINE__);
                    }
                }
                foreach ($boven as $i) {
                    if ($d[$i]['m']==0 && $d[$i]['s']<70) {
                        if ($i=='Rtobi'||$i=='RkamerR') {
                            sl($i, 85, basename(__FILE__).':'.__LINE__);
                        } else {
                            sl($i, 100, basename(__FILE__).':'.__LINE__);
                        }
                    } elseif ($d[$i]['m']==0 && $d[$i]['s']==100 && past($i)>900) {
                        if ($i=='RkamerL'||$i=='RkamerR') {
                            $temp=${substr($i, 1, -1).'_temp'};
                        } else {
                            $temp=${substr($i, 1).'_temp'};
                        }
                        if ($temp>19&&$temp>$buiten_temp+1) {
                            sl($i, 80, basename(__FILE__).':'.__LINE__);
                        }
                    }
                }
            }
        }
    }
}