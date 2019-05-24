<?php
/**
 * Pass2PHP
 * php version 7.3.3-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
//lg('               __CRON60__');
$user='cron60';
if ($d['auto']['s']=='On') {
    if ($d['Weg']['s']==0){//Thuis
        $items=array(
            'living_temp',
            'kamer_temp',
            'tobi_temp',
            'alex_temp',
            'zolder_temp'
        );
        $avg=0;
        foreach ($items as $item) {
            $avg=$avg+$d[$item]['s'];
        }
        $avg=$avg/6;
        foreach ($items as $item) {
            if ($d[$item]['s']>$avg+5&&$d[$item]['s']>25) {
                $msg='T '.$item.'='.$d[$item]['s'].'°C. AVG='.round($avg, 1).'°C';
                    alert(
                        $item,
                        $msg,
                        3600,
                        false,
                        true
                    );
            }
            if (past($item)>43150) {
                alert(
                    $item,
                    $item.' not updated since '.
                    strftime("%k:%M:%S", $d[$item]['t']),
                    7200
                );
            }
        }
        $devices=array('tobiZ','alexZ',/*'livingZ','livingZZ',*/'kamerZ');
        foreach ($devices as $device) {
            if (past($device)>43150) {
                alert(
                    $device,
                    $device.' geen communicatie sinds '.
                    strftime("%k:%M:%S", $d[$device]['t']),
                    14400
                );
            }
        }
    }
    if ($d['Weg']['s']<=1) {//Thuis of slapen

    }
    if ($d['Weg']['s']==1) {//Slapen

    }
    if ($d['Weg']['s']>=1) {//Slapen of weg

    }
    if ($d['Weg']['s']==2) {//Weg

    }
    //Altijd
    if (past('diepvries_temp')>7200) {
        alert(
            'diepvriestemp',
            'Diepvries temp not updated since '.
            strftime("%k:%M:%S", $d['diepvries_temp']['t']),
            7200
        );
    }
        $timefrom=TIME-86400;
    $chauth = curl_init(
        'https://app1pub.smappee.net/dev/v1/oauth2/token?grant_type=password&client_id='.
        $smappeeclient_id.'&client_secret='.
        $smappeeclient_secret.'&username='.
        $smappeeusername.'&password='.
        $smappeepassword.''
    );
    curl_setopt($chauth, CURLOPT_AUTOREFERER, true);
    curl_setopt($chauth, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($chauth, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($chauth, CURLOPT_VERBOSE, 0);
    curl_setopt($chauth, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($chauth, CURLOPT_SSL_VERIFYPEER, false);
    $objauth=json_decode(curl_exec($chauth));
    if (!empty($objauth)) {
        $access=$objauth->{'access_token'};
        curl_close($chauth);
        $chconsumption=curl_init('');
        curl_setopt($chconsumption, CURLOPT_HEADER, 0);
        $headers=array('Authorization: Bearer '.$access);
        curl_setopt($chconsumption, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($chconsumption, CURLOPT_AUTOREFERER, true);
        curl_setopt(
            $chconsumption,
            CURLOPT_URL,
            'https://app1pub.smappee.net/dev/v1/servicelocation/'.
            $smappeeserviceLocationId.'/consumption?aggregation=3&from='.
            $timefrom.'000&to='.
            TIME.'000'
        );
        curl_setopt($chconsumption, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($chconsumption, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($chconsumption, CURLOPT_VERBOSE, 0);
        curl_setopt($chconsumption, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($chconsumption, CURLOPT_SSL_VERIFYPEER, false);
        $data=json_decode(curl_exec($chconsumption), true);
        if (!empty($data['consumptions'])) {
            $vv=$data['consumptions'][0]['consumption']/1000;
            storemode('el', round($vv, 1));
            $zonvandaag=$data['consumptions'][0]['solar']/1000;
            store('zonvandaag', round($zonvandaag, 1));
            $gas=$d['gasvandaag']['s']/100;
            $water=$d['watervandaag']['s']/1000;

            @file_get_contents(
                $vurl."verbruik=$vv&gas=$gas&water=$water&zon=$zonvandaag"
            );
        }
        curl_close($chconsumption);
    }
}
/*--------------------- OUDE CRON ---------------------------------------------------*/
$items=array('eettafel','zithoek','tobi','kamer','alex');
foreach ($items as $item) {
    if ($d[$item]['s']!=0) {
        if ($d[$item]['m']==1) {
            $level=floor($d[$item]['s']*0.95);
            if ($level<2) {
                $level=0;
            }
            if ($level==20) {
                $level=19;
            }
            sl($item, $level);
            if ($level==0) {
                storemode($item, 0);
            }
        } elseif ($d[$item]['m']==2) {
            $level=$d[$item]['s']+1;
            if ($level==20) {
                $level=21;
            }
            if ($level>50) {
                $level=50;
            }
            sl($item, $level);
            if ($level==50) {
                storemode($item, 0);
            }
        }
    } elseif ($d[$item]['s']==0&&$item=='alex') {
        if ($d[$item]['m']==3) {
            if ($d['raamalex']['s']=='Open') {
                storemode('alex', 0);
            } else {
                if (past($item)>10800) {
                    sl('alex', 2);
                    storemode($item, 2);
                }
            }
        }
    }
}
if ($d['kamer']['m']==2) {
    $items=array('RkamerR','RkamerL');
    if ($d['kamer']['s']==3) {
        foreach ($items as $i) {
            if ($d[$i]['s']>70) {
                sl($i, 65);
            }
        }
    } elseif ($d['kamer']['s']==10) {
        foreach ($items as $i) {
            if ($d[$i]['s']>55) {
                sl($i, 30);
            }
        }
    } elseif ($d['kamer']['s']>=15) {
        foreach ($items as $i) {
            if ($d[$i]['s']>0) {
                sl($i, 0);
                storemode($i, 0);
            }
        }
    }
}

if ($d['living_temp']['s']>0&&$d['badkamer_temp']['s']>0) {
    $stamp=sprintf("%s", date("Y-m-d H:i"));
    $items=array('buiten','living','badkamer','kamer','tobi','alex','zolder');
    foreach ($items as $i) {
        ${$i.'_temp'}=$d[$i.'_temp']['s'];
    }
    $query="INSERT IGNORE INTO `temp`
        (
            `stamp`,
            `buiten`,
            `living`,
            `badkamer`,
            `kamer`,
            `tobi`,
            `alex`,
            `zolder`
        )
		VALUES (
		    '$stamp',
		    '$buiten_temp',
		    '$living_temp',
		    '$badkamer_temp',
		    '$kamer_temp',
		    '$tobi_temp',
		    '$alex_temp',
		    '$zolder_temp'
		);";
    $db = new mysqli('localhost', 'domotica', 'domotica', 'domotica');
    if ($db->connect_errno>0) {
        die('Unable to connect to database ['.$db->connect_error.']');
    }
    if (!$result = $db->query($query)) {
        die('There was an error running the query ['.$query.' - '.$db->error.']');
    }
}

if ($d['denon']['s']=='On') {
    $denonmain=json_decode(
        json_encode(
            simplexml_load_string(
                @file_get_contents(
                    'http://192.168.2.6/goform/formMainZone_MainZoneXml.xml?_='.TIME,
                    false,
                    $ctx
                )
            )
        ),
        true
    );
    if (!empty($denonmain)) {
        if ($denonmain['InputFuncSelect']['value']!=$d['denon']['m']) {
            storemode('denon', $denonmain['InputFuncSelect']['value']);
        }
        if ($denonmain['ZonePower']['value']!=$d['denonpower']['s']) {
            store('denonpower', $denonmain['ZonePower']['value']);
        }
        $denonsec=json_decode(
            json_encode(
                simplexml_load_string(
                    @file_get_contents(
                        'http://192.168.2.6/goform/formZone2_Zone2XmlStatusLite.xml?_='.TIME,
                        false,
                        $ctx
                    )
                )
            ),
            true
        );
        if ($denonmain['ZonePower']['value']=='ON'
            &&$denonsec['Power']['value']=='OFF'
        ) {
            denon('Z2ON');
        } elseif ($denonmain['ZonePower']['value']=='OFF'
            &&$denonsec['Power']['value']=='ON'
        ) {
            denon('Z2OFF');
        }
    }
}
if ($d['diepvries']['s']!='On'
    &&$d['diepvries_temp']['s']>$d['diepvries_temp']['m']
    &&past('diepvries')>1780
) {
    sw('diepvries', 'On', false, 'Diepvries On '.$d['diepvries_temp']['s'].'°C');
} elseif ($d['diepvries']['s']!='Off'
    &&$d['diepvries_temp']['s']<=$d['diepvries_temp']['m']
    &&past('diepvries')>280
) {
    sw('diepvries', 'Off', false, 'Diepvries Off '.$d['diepvries_temp']['s'].'°C');
} elseif ($d['diepvries']['s']!='Off'
    &&past('diepvries')>7200
) {
    sw(
        'diepvries',
        'Off',
        false,
        'Diepvries Off '.$d['diepvries_temp']['s'].'°C, was aan voor meer dan 2 uur'
    );
}

if ($d['auto']['s']=='On') {
    if ((    ($d['garage']['s']=='On'
        &&past('garage')>180)
        ||($d['pirgarage']['s']=='On'
        &&past('pirgarage')>180)    )
        &&TIME>strtotime('7:00')
        &&TIME<strtotime('23:00')
        &&$d['poort']['s']=='Closed'
        &&$d['achterdeur']['s']=='Closed'
    ) {
        if ($d['dampkap']['s']=='Off') {
            double('dampkap', 'On');
        }
    } elseif (($d['garage']['s']=='Off'
        &&past('garage')>270
        &&$d['pirgarage']['s']=='Off'
        &&past('pirgarage')>270)
        ||$d['poort']['s']=='Open'
        ||$d['achterdeur']['s']=='Open'
    ) {
        if ($d['dampkap']['s']=='On') {
            $tdampkap=past('dampkap');
            if ($d['dampkap']['m']==1) {
                if ($tdampkap>1200) {
                    double('dampkap', 'Off');
                    storemode('dampkap', 0);
                }
            } elseif (past('dampkap')>350) {
                double('dampkap', 'Off');
            }
        }
    }
    if ($d['wc']['s']=='On'
        && past('wc')>480
    ) {
        sw('wc', 'Off');
    }
    //Bose
    if ($d['pirliving']['s']=='Off'
        &&$d['pirgarage']['s']=='Off'
        &&past('bose101')>90
        &&past('bose102')>90
        &&past('bose103')>90
        &&past('bose104')>90
        &&past('bose105')>90
        &&$d['bose101']['s']=='On'
        &&$d['bose102']['s']=='Off'
        &&$d['bose103']['s']=='Off'
        &&$d['bose104']['s']=='Off'
        &&$d['bose105']['s']=='Off'
        &&$d['Weg']['s']>0
    ) {
        $status=json_decode(
            json_encode(
                simplexml_load_string(
                    @file_get_contents(
                        "http://192.168.2.101:8090/now_playing"
                    )
                )
            ),
            true
        );
        if (!empty($status)) {
            if (isset($status['@attributes']['source'])) {
                if ($status['@attributes']['source']!='STANDBY') {
                    bosekey("POWER", 0, 101);
                    sw('bose101', 'Off');
                    sw('bose102', 'Off');
                    sw('bose103', 'Off');
                    sw('bose104', 'Off');
                    sw('bose105', 'Off');
                }
            }
        }
    } elseif ($d['pirliving']['s']=='Off'
        &&$d['pirgarage']['s']=='Off'
        &&past('pirliving')>90
        &&past('pirgarage')>90
        &&past('bose101')>90
        &&past('bose102')>90
        &&past('bose103')>90
        &&past('bose104')>90
        &&past('bose105')>90
        &&$d['bose101']['s']=='On'
        &&$d['bose102']['s']=='Off'
        &&$d['bose103']['s']=='Off'
        &&$d['bose104']['s']=='Off'
        &&$d['bose105']['s']=='Off'
    ) {
        $volume=json_decode(
            json_encode(
                simplexml_load_string(
                    @file_get_contents(
                        "http://192.168.2.101:8090/volume"
                    )
                )
            ),
            true
        );
        $cv=$volume['actualvolume'];
        if ($cv==0) {
            sw('bose101', 'Off');
            bosekey("POWER", 0, 101);
        }
    } elseif ($d['achterdeur']['s']=='Closed'
        &&$d['pirgarage']['s']=='Off'
        &&past('pirgarage')>90
        &&past('bose105')>90
        &&$d['bose105']['s']=='On'
    ) {
        $status=json_decode(
            json_encode(
                simplexml_load_string(
                    @file_get_contents(
                        "http://192.168.2.105:8090/now_playing"
                    )
                )
            ),
            true
        );
        if (!empty($status)) {
            if (isset($status['@attributes']['source'])) {
                if ($status['@attributes']['source']!='STANDBY') {
                    bosekey("POWER", 0, 105);
                    sw('bose105', 'Off');
                }
            }
        }
    }
    if (past('deurbadkamer')>3600
        && $d['bose102']['s']=='0n'
    ) {
        $status=json_decode(
            json_encode(
                simplexml_load_string(
                    @file_get_contents(
                        "http://192.168.2.102:8090/now_playing"
                    )
                )
            ),
            true
        );
        if (!empty($status)) {
            if (isset($status['@attributes']['source'])) {
                if ($status['@attributes']['source']!='STANDBY') {
                    sw('bose102', 'Off');
                    bosekey("POWER", 0, 102);
                }
            }
        }
    }
    if ($d['garage']['s']=='Off'
        &&$d['pirgarage']['s']=='Off'
        &&past('pirgarage')>90
        &&past('bose104')>90
        &&$d['poort']['s']=='Closed'
        &&$d['deurgarage']['s']=='Closed'
        &&$d['achterdeur']['s']=='Closed'
        &&$d['bose104']['s']=='On'
    ) {
        $status=json_decode(
            json_encode(
                simplexml_load_string(
                    @file_get_contents(
                        "http://192.168.2.104:8090/now_playing"
                    )
                )
            ),
            true
        );
        if (!empty($status)) {
            if (isset($status['@attributes']['source'])) {
                if ($status['@attributes']['source']!='STANDBY') {
                    sw('bose104', 'Off');
                    bosekey("POWER", 0, 104);
                }
            }
        }
    }
    $items=array(101,102,103,104,105);
    foreach ($items as $i) {
        $status=json_decode(
            json_encode(
                simplexml_load_string(
                    @file_get_contents(
                        "http://192.168.2.$i:8090/now_playing"
                    )
                )
            ),
            true
        );
        if (!empty($status)) {
            if (isset($status['@attributes']['source'])) {
                if ($status['@attributes']['source']=='STANDBY') {
                    if ($d['bose'.$i]['s']!='Off') {
                        store('bose'.$i, 'Off');
                    }
                } else {
                    if ($d['bose'.$i]['s']!='On') {
                        store('bose'.$i, 'On');
                    }
                }
            }
        }
    }
    //End Bose

    if ($d['kamer']['s']>0
        &&$d['zon']['s']>0
        &&$d['RkamerL']['s']==0
        &&$d['RkamerR']['s']==0
        &&past('kamer')>900
    ) {
        sl('kamer', 0);
    }
    if ($d['tobi']['s']>0
        &&$d['zon']['s']>0
        &&$d['Rtobi']['s']==0
        &&past('tobi')>900
    ) {
        storemode('tobi', 2);
    }
    if ($d['alex']['s']>0
        &&$d['zon']['s']>0
        &&$d['Ralex']['s']==0
        &&past('alex')>900
    ) {
        storemode('alex', 2);
    }
    if ($d['eettafel']['s']>0
        &&$d['Rbureel']['s']==0
        &&$d['Rliving']['s']==0
        &&$d['zon']['s']>100
        &&past('eettafel')>2700
    ) {
        sl('eettafel', 0);
    }
    if ($d['Rliving']['s']>60&&$d['achterdeur']['s']=='Closed') {
        if ($d['tuin']['s']=='On') {
            sw('tuin', 'Off');
        }
        if ($d['terras']['s']>0) {
            sl('terras', 0);
        }
    }
}
if ($d['heater1']['s']!='Off'
    &&$d['heater2']['s']=='Off'
    &&$d['heater3']['s']=='Off'
    &&$d['heater4']['s']=='Off'
    &&past('heater1')>120
    &&past('heater2')>90
    &&past('heater3')>90
    &&past('heater4')>90
) {
    sw('heater1', 'Off');
}

if ($d['water']['s']=='On') {
    if (past('water')>$d['water']['m']) {
        double('water', 'Off');
    }
}
if (TIME>$d['civil_twilight']['s']
    &&TIME<$d['civil_twilight']['m']
) {
    echo 'zonop';
    if ($d['auto']['m']!=true) {
        storemode('auto', true);
        $d['auto']['m']=true;
    }
} else {
    echo 'zononder';
    if ($d['auto']['m']!=false ) {
        storemode('auto', false);
        $d['auto']['m']=false;
    }
}
/*
if (pingport('diskstation',1598)==1){
    if ($d['check_diskstation_1598']['s']>0)store('check_diskstation_1598',0);
    if ($d['nas']['s']!='On')store('nas','On');
}else{
    $check=$d['check_diskstation_1598']['s']+1;
    if ($check>0)store('check_diskstation_1598',$check);
    if ($check>=3&&$d['nas']['s']!='Off')store('nas','Off');
}*/

ping('192.168.2.101');
ping('192.168.2.102');
ping('192.168.2.103');
ping('192.168.2.104');
ping('192.168.2.11');
ping('192.168.2.12');
ping('192.168.2.13');
ping('192.168.2.14');
ping('192.168.2.15');
ping('192.168.2.224');

$ctx=stream_context_create(array('http'=>array('timeout' =>15)));
$relay=new SimpleXMLElement(
    @file_get_contents('http://192.168.2.224/status.xml', false, $ctx)
);
if (!empty($relay)) {
    if ($relay->RELAYS->RLY1=='on'&&$d['heater1']['s']!='On') {
        store('heater1', 'On');
    } elseif ($relay->RELAYS->RLY1=='off'&&$d['heater1']['s']!='Off') {
        store('heater1', 'Off');
    }
    if ($relay->RELAYS->RLY2=='on'&&$d['heater2']['s']!='On') {
        store('heater2', 'On');
    } elseif ($relay->RELAYS->RLY2=='off'&&$d['heater2']['s']!='Off') {
        store('heater2', 'Off');
    }
    if ($relay->RELAYS->RLY3=='on'&&$d['heater3']['s']!='On') {
        store('heater3', 'On');
    } elseif ($relay->RELAYS->RLY3=='off'&&$d['heater3']['s']!='Off') {
        store('heater3', 'Off');
    }
    if ($relay->RELAYS->RLY4=='on'&&$d['heater4']['s']!='On') {
        store('heater4', 'On');
    } elseif ($relay->RELAYS->RLY4=='off'&&$d['heater4']['s']!='Off') {
        store('heater4', 'Off');
    }
}