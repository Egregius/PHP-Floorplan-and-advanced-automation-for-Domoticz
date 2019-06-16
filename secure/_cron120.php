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
$prevbuien=(float)$d['buien']['s'];
$prevbuitentemp=(float)$d['buiten_temp']['s'];
$buiten_temp=(float)$d['buiten_temp']['s'];
$wind=$prevwind;
$maxtemp=1;
$mintemp=100;
$maxrain=-1;
$ds=@file_get_contents('https://api.darksky.net/forecast/'.$dsapikey.'/'.$lat.','.$lon.'?units=si');
if (isset($ds)) {
    file_put_contents('/temp/ds.json', $ds);
    $ds=@json_decode($ds, true);
    if (isset($ds['currently'])) {
        if (isset($ds['currently']['temperature'])) {
            $dstemp=$ds['currently']['temperature'];
            if ($dstemp>$buiten_temp+0.5) {
                $dstemp=$buiten_temp+0.5;
            } elseif ($dstemp<$buiten_temp-0.5) {
                $dstemp=$buiten_temp-0.5;
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
            $dswind=$dswind * 1.609344;
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
            $mintemp=round($mintemp, 1);
            if ($d['minmaxtemp']['s']!=$mintemp) {
            	store('minmaxtemp', $mintemp);
            }
            $maxtemp=round($maxtemp, 1);
            if ($d['minmaxtemp']['m']!=$maxtemp) {
	            storemode('minmaxtemp', $maxtemp);
	        }
	        if ($d['max']['m']!=$maxrain) {
	            storemode('max', $maxrain, 1);
	        }
        }
    }
}
$ow=@file_get_contents('https://api.openweathermap.org/data/2.5/weather?id='.$owid.'&units=metric&APPID='.$owappid);
if (isset($ow)) {
    file_put_contents('/temp/ow.json', $ow);
    $ow=@json_decode($ow, true);
    if (isset($ow['main']['temp'])) {
        $owtemp=$ow['main']['temp'];
        if ($owtemp>$buiten_temp+0.5) {
            $owtemp=$buiten_temp+0.5;
        } elseif ($owtemp<$buiten_temp-0.5) {
            $owtemp=$buiten_temp-0.5;
        }
        $owwind=$ow['wind']['speed'] * 3.6;
        if ($d['icon']['m']!=$ow['main']['humidity']) {
	        storemode('icon', $ow['main']['humidity']);
	    }
	    if ($d['icon']['s']!=$ow['weather'][0]['icon']) {
	        store('icon', $ow['weather'][0]['icon']);
	    }
    }
}
$buienradar=0;
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
    $buienradar=round($totalrain/7, 0);
    if ($buienradar>100) {
        $buienradar=100;
    }
    if ($buienradar>20) {
        $maxrain=$buienradar;
    }
}

if (isset($buiten_temp)
    &&isset($dstemp)
    &&isset($owtemp)
) {
    $buiten_temp=($buiten_temp+$dstemp+$owtemp)/3;
} elseif (isset($buiten_temp)&&isset($dstemp)) {
    $buiten_temp=($buiten_temp+$dstemp)/2;
} elseif (isset($owtemp)&&isset($dstemp)) {
    $buiten_temp=($owtemp+$dstemp)/2;
} elseif (isset($owtemp)) {
    $buiten_temp=$owtemp;
} elseif (isset($dstemp)) {
    $buiten_temp=$dstemp;
}
if (isset($ds['hourly']['data'])) {
	$maxtemp=round($maxtemp, 1);
    if ($buiten_temp>$maxtemp) {
        $maxtemp=round($buiten_temp, 1);
        storemode('minmaxtemp', $maxtemp);
    }
    $mintemp=round($mintemp, 1);
    if ($buiten_temp<$mintemp) {
        $mintemp=round($buiten_temp, 1);
        store('minmaxtemp', $mintemp);
    }
}
$buiten_temp=round($buiten_temp,1);
if ($d['buiten_temp']['s']!=$buiten_temp) {
	store('buiten_temp', $buiten_temp);
}
$db=new PDO("mysql:host=localhost;dbname=domotica;", 'domotica', 'domotica');
$result=$db->query("SELECT AVG(temp) as AVG FROM (SELECT buiten as temp FROM `temp` ORDER BY `temp`.`stamp` DESC LIMIT 0,10) as A");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	$avg=$row['AVG'];
}
if ($prevbuitentemp>$avg+0.5) {
	storeicon('buiten_temp', 'red5');
} elseif ($prevbuitentemp>$avg+0.4) {
    storeicon('buiten_temp', 'red4');
} elseif ($prevbuitentemp>$avg+0.3) {
    storeicon('buiten_temp', 'red3');
} elseif ($prevbuitentemp>$avg+0.2) {
    storeicon('buiten_temp', 'red');
} elseif ($prevbuitentemp>$avg+0.1) {
    storeicon('buiten_temp', 'up');
} elseif ($prevbuitentemp<$avg-0.5) {
    storeicon('buiten_temp', 'blue5');
} elseif ($prevbuitentemp<$avg-0.4) {
    storeicon('buiten_temp', 'blue4');
} elseif ($prevbuitentemp<$avg-0.3) {
    storeicon('buiten_temp', 'blue3');
} elseif ($prevbuitentemp<$avg-0.2) {
    storeicon('buiten_temp', 'blue');
} elseif ($prevbuitentemp<$avg-0.1) {
    storeicon('buiten_temp', 'down');
} else {
    storeicon('buiten_temp', '');
}

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
if (isset($d['buien']['m'])&&isset($dsbuien)&&isset($buienradar)) {
    $newbuien=($d['buien']['s']+$dsbuien+$buienradar)/3;
} elseif (isset($d['buien']['s'])&&isset($newbuien)) {
    $newbuien=($d['buien']['s']+$buienradar)/2;
} elseif (isset($d['buien']['s'])&&isset($dsbuien)) {
    $newbuien=($d['buien']['s']+$dsbuien)/2;
} elseif (isset($dsbuien)) {
    $newbuien=$dsbuien;
}
if (isset($newbuien)&&$newbuien>100) {
    $newbuien=100;
}
if (isset($dsbuien)&&$dsbuien>100) {
    $dsbuien=100;
}
$buien=round($newbuien, 0);
if ($d['buien']['s']!=$buien) {
	store('buien', $buien);
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
        ('$buienradar','$dsbuien','$buien');"
);
lg('Buienradar:'.$buienradar.' dsbuien:'.$dsbuien.' buien:'.$buien);
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
        &&$d['achterdeur']['s']=='Closed'
        &&past('zon')>1800
        &&past('water')>72000
    ) {
        $msg="Regen check:
            __Laatste 48u:$rainpast
            __Volgende 48u: $maxrain
            __Automatisch tuin water geven gestart.";
        if ($rainpast<1000&&$maxrain<1) {
            sw('water', 'On');
            storemode('water', 300);
            telegram($msg, 2);
        }
    }
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
        &&$buien<5
        &&$d['zon']['s']>1500
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
        &&$buien<5
        &&$d['living_temp']['s']>22
        &&$d['buiten_temp']['s']>17
        &&$d['zon']['s']>1500
        &&$d['luifel']['m']==0
        &&past('luifel')>600
        &&$wind<$windhist
        &&TIME>strtotime("10:00")
    ) {
        if ($d['luifel']['m']==0) {
            sl('luifel', $maxluifel);
        }
    } elseif (($buien>5
        ||(($d['zon']['s']==0
        ||$d['living_temp']['s']<19)
        &&$d['luifel']['m']==0))
        &&$d['luifel']['s']>0
    ) {
        sl('luifel', 0);
    }
}
$items=array('buiten_temp', 'living_temp', 'badkamer_temp', 'kamer_temp', 'tobi_temp', 'alex_temp', 'zolder_temp');
foreach ($items as $i) {
    if (past($i)>1800) {
        storeicon($i, '');
    }
}