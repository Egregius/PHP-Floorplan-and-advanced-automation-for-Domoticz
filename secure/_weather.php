<?php
/**
 * Pass2PHP functions
 * php version 7.3.4-2
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
$user='weather';
$prevwind=$d['wind']['s'];
$prevbuien=$d['buien']['s'];
$prevbuitentemp=$d['buiten_temp']['s'];
$buiten_temp=$d['buiten_temp']['s'];
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
            $maxtemp=round($maxtemp, 1);
            if ($d['max']['m']!=$maxrain) {
	            storemode('max', $maxrain, basename(__FILE__).':'.__LINE__, 1);
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
        if ($d['icon']['m']!=$ow['weather'][0]['id']) {
	        storemode('icon', $ow['weather'][0]['id'], basename(__FILE__).':'.__LINE__);
	    }
	    if ($d['icon']['s']!=$ow['weather'][0]['icon']) {
	        store('icon', $ow['weather'][0]['icon'], basename(__FILE__).':'.__LINE__);
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
    }
    $mintemp=round($mintemp, 1);
    if ($buiten_temp<$mintemp) {
        $mintemp=round($buiten_temp, 1);
    }
    if ($d['minmaxtemp']['m']!=$maxtemp) {
    	storemode('minmaxtemp', $maxtemp, basename(__FILE__).':'.__LINE__);
    }
    if ($d['minmaxtemp']['s']!=$mintemp) {
	    store('minmaxtemp', $mintemp, basename(__FILE__).':'.__LINE__);
	}
}
$buiten_temp=round($buiten_temp,1);
if ($d['buiten_temp']['s']!=$buiten_temp) {
	store('buiten_temp', $buiten_temp, basename(__FILE__).':'.__LINE__);
}
$db=new PDO("mysql:host=localhost;dbname=domotica;", 'domotica', 'domotica');
$result=$db->query("SELECT AVG(temp) as AVG FROM (SELECT buiten as temp FROM `temp` ORDER BY `temp`.`stamp` DESC LIMIT 0,20) as A");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	$avg=$row['AVG'];
}
if ($prevbuitentemp>$avg+0.5) {
	if ($d['buiten_temp']['icon']!='red5') {
		storeicon('buiten_temp', 'red5', basename(__FILE__).':'.__LINE__);
	}
} elseif ($prevbuitentemp>$avg+0.4) {
    if ($d['buiten_temp']['icon']!='red4') {
		storeicon('buiten_temp', 'red4', basename(__FILE__).':'.__LINE__);
	}
} elseif ($prevbuitentemp>$avg+0.3) {
    if ($d['buiten_temp']['icon']!='red3') {
		storeicon('buiten_temp', 'red3', basename(__FILE__).':'.__LINE__);
	}
} elseif ($prevbuitentemp>$avg+0.2) {
    if ($d['buiten_temp']['icon']!='red') {
		storeicon('buiten_temp', 'red', basename(__FILE__).':'.__LINE__);
	}
} elseif ($prevbuitentemp>$avg+0.1) {
    if ($d['buiten_temp']['icon']!='up') {
		storeicon('buiten_temp', 'up', basename(__FILE__).':'.__LINE__);
	}
} elseif ($prevbuitentemp<$avg-0.5) {
    if ($d['buiten_temp']['icon']!='blue5') {
		storeicon('buiten_temp', 'blue5', basename(__FILE__).':'.__LINE__);
	}
} elseif ($prevbuitentemp<$avg-0.4) {
    if ($d['buiten_temp']['icon']!='blue4') {
		storeicon('buiten_temp', 'blue4', basename(__FILE__).':'.__LINE__);
	}
} elseif ($prevbuitentemp<$avg-0.3) {
    if ($d['buiten_temp']['icon']!='blue3') {
		storeicon('buiten_temp', 'blue3', basename(__FILE__).':'.__LINE__);
	}
} elseif ($prevbuitentemp<$avg-0.2) {
    if ($d['buiten_temp']['icon']!='blue') {
		storeicon('buiten_temp', 'blue', basename(__FILE__).':'.__LINE__);
	}
} elseif ($prevbuitentemp<$avg-0.1) {
    if ($d['buiten_temp']['icon']!='down') {
		storeicon('buiten_temp', 'down', basename(__FILE__).':'.__LINE__);
	}
} else {
    if ($d['buiten_temp']['icon']!='') {
		storeicon('buiten_temp', '', basename(__FILE__).':'.__LINE__);
	}
}

if (isset($prevwind)&&isset($owwind)&&isset($dswind)) {
    $wind=round(($prevwind+$owwind+$dswind)/3,1);
} elseif (isset($prevwind)&&isset($owwind)) {
    $wind=round(($prevwind+$owwind)/2,1);
} elseif (isset($prevwind)&&isset($dswind)) {
    $wind=round(($prevwind+$dswind)/2,1);
} elseif (isset($owwind)&&isset($dswind)) {
    $wind=round(($owwind+$dswind)/2,1);
} elseif (isset($owwind)) {
    $wind=round($owwind,1);
} elseif (isset($dswind)) {
    $wind=round($dswind,1);
}
if ($wind!=$prevwind) {
    store('wind', $wind, basename(__FILE__).':'.__LINE__);
}
$windhist=json_decode($d['wind']['m']);
$windhist[]=round($wind, 2);
$windhist=array_slice($windhist, -4);
storemode('wind', json_encode($windhist), basename(__FILE__).':'.__LINE__);
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
if (isset($d['buien']['s'])&&isset($dsbuien)&&isset($buienradar)) {
    $newbuien=($d['buien']['s']+$dsbuien+$buienradar)/3;
} elseif (isset($d['buien']['s'])&&isset($buienradar)) {
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
if ($newbuien<1) {
	$newbuien=0;
}
$buien=round($newbuien, 0);
if ($d['buien']['s']!=$buien) {
	store('buien', $buien, basename(__FILE__).':'.__LINE__);
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
if ($buienradar>0||$dsbuien>0||$buien>0) {
	lg('Buienradar:'.$buienradar.' dsbuien:'.$dsbuien.' buien:'.$buien);
}

if ($d['achterdeur']['s']=='Closed') {
	$stmt=$db->query("SELECT MAX(`buiten`) AS max FROM temp;");
	while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
		$watertime=$row['max']*15;
	}
	if (TIME>=strtotime('21:30')
		&&$d['zon']['s']==0
		&&past('zon')>1800
		&&past('water')>72000
	) {
		$db=new PDO("mysql:host=localhost;dbname=domotica;", 'domotica', 'domotica');
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt=$db->query("SELECT SUM(`buien`) AS buien FROM regen;");
		while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
			$rainpast=$row['buien'];
		}
		$msg="Regen check:
			__Laatste 48u:$rainpast
			__Volgende 48u: $maxrain
			__Automatisch tuin water geven gestart voor $watertime sec.";
		if ($rainpast<1000&&$maxrain<1) {
			sw('water', 'On', basename(__FILE__).':'.__LINE__);
			storemode('water', $watertime, basename(__FILE__).':'.__LINE__);
			telegram($msg, 2);
		}
	}
}