<?php
/**
 * Pass2PHP functions
 * php version 7.3.5-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
//lg('               __CRON120__');
$user='cron120';
$prevwind=(float)$d['wind']['s'];
$prevbuien=(float)$d['buiten_temp']['m'];
$wind=$prevwind;
$maxtemp=1;
$mintemp=100;
$maxrain=-1;
$ds=@json_decode(
    @file_get_contents(
        'https://api.darksky.net/forecast/'.$dsapikey.'/'.$lat.','.$lon.'?units=si'
    ),
    true
);
if (isset($ds['currently'])) {
    if (isset($ds['currently']['temperature'])) {
        $dstemp=$ds['currently']['temperature'];
        if ($dstemp>$d['buiten_temp']['s']+0.5) {
            $dstemp=$d['buiten_temp']['s']+0.5;
        } elseif ($dstemp<$d['buiten_temp']['s']-0.5) {
            $dstemp=$d['buiten_temp']['s']-0.5;
        }
    }
    if (isset($ds['currently']['windSpeed'])) {
        $dswind=$ds['currently']['windSpeed'];
    }
    if (isset($ds['currently']['windGust'])) {
        if ($ds['currently']['windGust']>$dswind) {
            $dswind=$ds['currently']['windGust'];
        }
    }
    if (isset($dswind)) {
        $dswind=$dswind / 0.621371192;
    }
    if (isset($ds['minutely']['data'])) {
        $dsbuien=0;
        foreach ($ds['minutely']['data'] as $i) {
            if ($i['time']>TIME&&$i['time']<TIME+1800) {
                if ($i['precipProbability']*50>$dsbuien) {
                    $dsbuien=$i['precipProbability']*35;
                }
            }
        }
    }
    if (isset($ds['hourly']['data'])) {
        foreach ($ds['hourly']['data'] as $i) {
            if ($i['time']>TIME&&$i['time']<TIME+3600*6) {
                if ($i['temperature']>$maxtemp) {
                    $maxtemp=$i['temperature'];
                }
                if ($i['temperature']<$mintemp) {
                    $mintemp=$i['temperature'];
                }
            }
            if ($i['precipIntensity']>$maxrain) {
                $maxrain=$i['precipIntensity'];
            }
        }
        store('minmaxtemp', round($mintemp, 1));
        storemode('minmaxtemp', round($maxtemp, 1));
        storemode('max', $maxrain);
    }
}
$ow=@json_decode(
    @file_get_contents(
        'https://api.openweathermap.org/data/2.5/weather?id='.
        $owid.'&units=metric&APPID='.$owappid
    ),
    true
);
if (isset($ow['main']['temp'])) {
    $owtemp=$ow['main']['temp'];
    if ($owtemp>$d['buiten_temp']['s']+0.5) {
        $owtemp=$d['buiten_temp']['s']+0.5;
    } elseif ($owtemp<$d['buiten_temp']['s']-0.5) {
        $owtemp=$d['buiten_temp']['s']-0.5;
    }
    $owwind=$ow['wind']['speed'];
    storemode('icon', $ow['main']['humidity']);
    store('icon', $ow['weather'][0]['icon']);
}

$rains=@file_get_contents(
    'http://gadgets.buienradar.nl/data/raintext/?lat='.$lat.'&lon='.$lon
);
if (!empty($rains)) {
    $rains=str_split($rains, 11);
    $totalrain=0;
    $aantal=0;
    foreach ($rains as $rain) {
        $aantal=$aantal+1;
        $totalrain=$totalrain+substr($rain, 0, 3);
        if ($aantal==7) {
            break;
        }
    }
    $newbuien=$totalrain/7;
    if ($newbuien>100) {
        $newbuien=100;
    }
    if ($newbuien>20) {
        $maxrain=$newbuien;
    }
}

if (isset($d['buiten_temp']['s'])
    &&isset($dstemp)
    &&isset($owtemp)
) {
    $d['buiten_temp']['s']=($d['buiten_temp']['s']+$dstemp+$owtemp)/3;
} elseif (isset($d['buiten_temp']['s'])&&isset($dstemp)) {
    $d['buiten_temp']['s']=($d['buiten_temp']['s']+$dstemp)/2;
} elseif (isset($owtemp)&&isset($dstemp)) {
    $d['buiten_temp']['s']=($owtemp+$dstemp)/2;
} elseif (isset($owtemp)) {
    $d['buiten_temp']['s']=$owtemp;
} elseif (isset($dstemp)) {
    $d['buiten_temp']['s']=$dstemp;
}
if (isset($ds['hourly']['data'])) {
    if ($d['buiten_temp']['s']>$maxtemp) {
        $maxtemp=$d['buiten_temp']['s'];
        storemode('minmaxtemp', round($maxtemp, 1));
    }
    if ($d['buiten_temp']['s']<$mintemp) {
        $mintemp=$d['buiten_temp']['s'];
        store('minmaxtemp', round($mintemp, 1));
    }
}
store('buiten_temp', round($d['buiten_temp']['s'],1));

if (isset($prevwind)&&isset($owwind)&&isset($dswind)) {
    $wind=($prevwind+$owwind+$dswind)/3;
} elseif (isset($prevwind)&&isset($owwind)) {
    $wind=($prevwind+$owwind)/2;
} elseif (isset($prevwind)&&isset($dswind)) {
    $wind=($prevwind+$dswind)/2;
} elseif (isset($owwind)&&isset($dswind)) {
    $wind=($owwind+$dswind)/2;
} elseif (isset($owwind)) {
    $wind=$owwind;
} elseif (isset($dswind)) {
    $wind=$dswind;
}
if ($wind!=$prevwind) {
    store('wind', round($wind, 2));
}

$windhist=json_decode($d['wind']['m']);
$windhist[]=round($wind, 3);
$windhist=array_slice($windhist, -4);
echo json_encode($windhist).'<hr>';
storemode('wind', json_encode($windhist));
$msg='Buiten temperaturen : ';
if (isset($dstemp)) {
    $msg.='Darksky = '.round($dstemp, 1).'°C ';
}
if (isset($owtemp)) {
    $msg.='Openweathermap = '.round($owtemp, 1).'°C ';
}
if (isset($d['buiten_temp']['s'])) {
    $msg.='buiten_temp = '.round($d['buiten_temp']['s'], 1).'°C';
}
//lg($msg);
if (isset($d['buiten_temp']['m'])&&isset($dsbuien)&&isset($newbuien)) {
    $newbuien=($d['buiten_temp']['m']+$dsbuien+$newbuien)/3;
} elseif (isset($d['buiten_temp']['m'])&&isset($newbuien)) {
    $newbuien=($d['buiten_temp']['m']+$newbuien)/2;
} elseif (isset($d['buiten_temp']['m'])&&isset($dsbuien)) {
    $newbuien=($d['buiten_temp']['m']+$dsbuien)/2;
} elseif (isset($dsbuien)) {
    $newbuien=$dsbuien;
}
if (isset($newbuien)&&$newbuien>100) {
    $newbuien=100;
}
if (isset($dsbuien)&&$dsbuien>100) {
    $dsbuien=100;
}
if (isset($buien)&&$buien>100) {
    $buien=100;
}
$buien=round($newbuien, 0);
storemode('buiten_temp', $buien);
if (!isset($owbuien)) {
    $owbuien=0;
}
if (!isset($dsbuien)) {
    $dsbuien=0;
}
if (!isset($newbuien)) {
    $newbuien=0;
}
$db->query(
    "INSERT IGNORE INTO `regen`
        (`buienradar`,`darksky`,`buien`)
    VALUES
        ('$newbuien','$dsbuien','$buien');"
);


/*checkport('192.168.2.11',80);
checkport('192.168.2.12',80);
checkport('192.168.2.13',80);
checkport('192.168.2.14');
checkport('192.168.2.15',80);
checkport('192.168.2.224',80);
checkport('192.168.2.9',8080);
*/

if ($d['auto']['s']=='On') {
    $db=new PDO("mysql:host=localhost;dbname=domotica;", 'domotica', 'domotica');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt=$db->query("SELECT SUM(`buien`) AS buien FROM regen;");
    while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
        $rainpast=$row['buien'];
    }
    if ($rainpast>64000) {
        $pomppauze=43200;
    } elseif ($rainpast>32000) {
        $pomppauze=86400;
    } elseif ($rainpast>16000) {
        $pomppauze=86400*2;
    } else {
        $pomppauze=86400*28;
    }
    if ($d['regenpomp']['s']=='On'&&past('regenpomp')>57) {
        sw(
            'regenpomp',
            'Off',
            false,
            'Regenpomp off, was on for '.
            convertToHours(past('regenpomp')).' rainpast='.$rainpast
        );
    } elseif ($d['regenpomp']['s']=='Off'
        &&past('regenpomp')>$pomppauze
    ) {
        sw(
            'regenpomp',
            'On',
            false,
            'Regenpomp aan, was uit voor '.
            convertToHours(past('regenpomp')).' rainpast='.$rainpast
        );
        telegram('Regenpomp aan, was uit voor '.
            convertToHours(past('regenpomp')).'__rainpast='.$rainpast);
    }
    if (TIME>=strtotime('21:30')
        &&$d['zon']['s']==0
        &&past('zon')>1800
        &&past('water')>72000
    ) {
        $msg="Regen check:
            __Laatste 48u:$rainpast
            __Volgende 48u: $maxrain
            __Automatisch tuin water geven gestart.";
        if ($rainpast<1000&&$maxrain<0.5) {
            sw('water', 'On');
            storemode('water', 300);
            telegram($msg, 2);
        }
    }
    $zonopen=1500;
    $maxbuien=5;
    $living_temp=$d['living_temp']['s'];
    $x=0;
    foreach ($windhist as $y) {
        $x=$y+$x;
        $windhist=round($x/4, 2);
    }
    if ($d['heating']['s']==0) { //Neutral
        if ($wind>=30) {
            $maxluifel=0;
        } elseif ($wind>=25) {
            $maxluifel=25;
        } elseif ($wind>=20) {
            $maxluifel=30;
        } elseif ($wind>=15) {
            $maxluifel=35;
        } elseif ($wind>=10) {
            $maxluifel=40;
        } else {
            $maxluifel=40;
        }
    } elseif ($d['heating']['s']==1) { //Cooling
        if ($wind>=30) {
            $maxluifel=0;
        } elseif ($wind>=25) {
            $maxluifel=28;
        } elseif ($wind>=20) {
            $maxluifel=36;
        } elseif ($wind>=15) {
            $maxluifel=44;
        } elseif ($wind>=10) {
            $maxluifel=52;
        } else {
            $maxluifel=60;
        }
    } else {
        $maxluifel=0;
    }
    $wind=round($wind, 1);
    if ($d['luifel']['m']==0) {
        if (past('luifel')>3600&&$maxluifel>50) {
            storemode('luifel', 1);
            $d['luifel']['m']=1;
        } elseif (past('luifel')>28800) {
            storemode('luifel', 1);
            $d['luifel']['m']=1;
        }
    }
    if ($d['luifel']['s']>$maxluifel&&$d['luifel']['m']==0) {
        sl('luifel', $maxluifel);
    } elseif ($d['heating']['s']==2
        &&$d['luifel']['s']<$maxluifel
        &&$buien<$maxbuien
        &&$d['zon']['s']>$zonopen
        &&$d['luifel']['m']==0
        &&past('luifel')>600
        &&$wind<$windhist
        &&TIME>strtotime("10:00")
    ) {
        if ($d['luifel']['m']==0) {
            sl('luifel', $maxluifel);
        }
    } elseif ($d['heating']['s']<2
        &&$d['luifel']['s']<$maxluifel
        &&$buien<$maxbuien
        &&$living_temp>22
        &&$d['buiten_temp']['s']>17
        &&$d['zon']['s']>$zonopen
        &&$d['luifel']['m']==0
        &&past('luifel')>600
        &&$wind<$windhist
        &&TIME>strtotime("10:00")
    ) {
        if ($d['luifel']['m']==0) {
            sl('luifel', $maxluifel);
        }
    } elseif (($buien>$maxbuien
        ||(($d['zon']['s']==0
        ||$d['living_temp']['s']<19)
        &&$d['luifel']['m']==0))
        &&$d['luifel']['s']>0
    ) {
        sl('luifel', 0);
    }
}