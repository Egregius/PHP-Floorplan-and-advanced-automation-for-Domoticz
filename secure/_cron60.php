<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
$user='cron60';
if ($d['auto']['s']=='On') {
    /* -------------------------------------------- THUIS ----------------------------*/
    if ($d['Weg']['s']==0){
        if ($d['pirkeuken']['s']=='Off') {
            $uit=300;
            if (past('pirkeuken')>$uit) {
                $items=array('keuken','wasbak','kookplaat','werkblad1');
                foreach ($items as $item) {
                    if ($d[$item]['s']!='Off') {
                        if (past($item)>$uit) {
                            sw($item, 'Off', basename(__FILE__).':'.__LINE__);
                        }
                    }
                }
            }
        }
        if ($d['pirliving']['s']=='Off') {
            $uit=7200;
            if (past('pirliving')>$uit) {
                $items=array('bureel');
                foreach ($items as $item) {
                    if ($d[$item]['s']!='Off') {
                        if (past($item)>$uit) {
                            sw($item, 'Off', basename(__FILE__).':'.__LINE__);
                        }
                    }
                }
                $items=array('eettafel','zithoek');
                foreach ($items as $item) {
                    if ($d[$item]['s']>0) {
                        if (past($item)>$uit) {
                            sl($item, 0, basename(__FILE__).':'.__LINE__);
                        }
                    }
                }
            }
            $uit=10800;
            if (past('pirliving')>$uit) {
                $items=array('tvled','kristal','jbl');
                foreach ($items as $item) {
                    if ($d[$item]['s']!='Off') {
                        if (past($item)>$uit) {
                            sw($item, 'Off', basename(__FILE__).':'.__LINE__);
                        }
                    }
                }
            }
            /*$uit=10800;
            if (past('pirliving')>$uit) {
                if ($d['denon']['s']=='On'||$d['lgtv']['s']=='On') {
                    ud('miniliving4l', 1, 'On');
                    lg('miniliving4l pressed omdat er al 3 uur geen beweging is');
                }
            }*/
        }
        if (past('Xlight')>300&&$d['Xlight']['s']!='Off') {
            sw('Xlight', 'Off', basename(__FILE__).':'.__LINE__);
        }
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
        $avg=$avg/count($items);
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
    }
    /* -------------------------------------------- THUIS OF SLAPEN --------------*/
    if ($d['Weg']['s']<=1) {
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
                    sl($item, $level, basename(__FILE__).':'.__LINE__);
                    if ($level==0) {
                        storemode($item, 0, basename(__FILE__).':'.__LINE__);
                    }
                } elseif ($d[$item]['m']==2) {
                    $level=$d[$item]['s']+1;
                    if ($level==20) {
                        $level=21;
                    }
                    if ($level>50) {
                        $level=50;
                    }
                    sl($item, $level, basename(__FILE__).':'.__LINE__);
                    if ($level==50) {
                        storemode($item, 0, basename(__FILE__).':'.__LINE__);
                    }
                }
            } elseif ($d[$item]['s']==0&&$item=='alex') {
                if ($d[$item]['m']==3) {
                    if ($d['raamalex']['s']=='Open') {
                        storemode('alex', 0, basename(__FILE__).':'.__LINE__);
                    } else {
                        if (past($item)>10800) {
                            sl('alex', 2, basename(__FILE__).':'.__LINE__);
                            storemode($item, 2, basename(__FILE__).':'.__LINE__);
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
                        sl($i, 65, basename(__FILE__).':'.__LINE__);
                    }
                }
            } elseif ($d['kamer']['s']==7) {
                foreach ($items as $i) {
                    if ($d[$i]['s']>55) {
                        sl($i, 30, basename(__FILE__).':'.__LINE__);
                    }
                }
            } elseif ($d['kamer']['s']>=11) {
                foreach ($items as $i) {
                    if ($d[$i]['s']>0) {
                        sl($i, 0, basename(__FILE__).':'.__LINE__);
                        storemode($i, 0, basename(__FILE__).':'.__LINE__);
                    }
                }
            }
        }

    }
    /* -------------------------------------------- SLAPEN --------------------------*/
    if ($d['Weg']['s']==1) {
        $uit=600;
        $items=array('pirgarage','pirkeuken','pirliving','pirinkom');
        foreach ($items as $item) {
            if ($d[$item]['s']!='Off') {
                ud($item, 0, 'Off');
                lg($item.' uitgeschakeld omdat we slapen');
            }
        }
        $items=array(
            'hall',
            'bureel',
            'denon',
            'kristal',
            'garage',
            'terras',
            'tuin',
            'voordeur',
            'keuken',
            'werkblad1',
            'wasbak',
            'kookplaat',
            'zolderg',
            'dampkap',
            'Xlight'
        );
        foreach ($items as $item) {
            if ($d[$item]['s']!='Off') {
                if (past($item)>$uit) {
                    sw($item, 'Off', basename(__FILE__).':'.__LINE__);
                    lg($item.' uitgeschakeld omdat we slapen');
                }
            }
        }
        $items=array('eettafel','zithoek');
        foreach ($items as $item) {
            if ($d[$item]['s']>0) {
                if (past($item)>$uit) {
                    sl($item, 0, basename(__FILE__).':'.__LINE__);
                    lg($item.' uitgeschakeld omdat we slapen');
                }
            }
        }
    }
    /* -------------------------------------------- SLAPEN OF WEG ---------------*/
    if ($d['Weg']['s']>=1) {
    }
    /* -------------------------------------------- WEG ----------------------------*/
    if ($d['Weg']['s']==2) {
        $uit=600;
        $items=array('pirgarage','pirkeuken','pirliving','pirinkom','pirhall');
        foreach ($items as $item) {
            if ($d[$item]['s']!='Off') {
                ud($item, 0, 'Off');
                lg($item.' uitgeschakeld omdat we weg zijn');
            }
        }
        $items=array(
            'garage',
            'denon',
            'bureel',
            'kristal',
            'terras',
            'tuin',
            'voordeur',
            'hall',
            'inkom',
            'keuken',
            'werkblad1',
            'wasbak',
            'kookplaat',
            'badkamervuur2',
            'badkamervuur1',
            'zolderg',
            'Xlight'
        );
        foreach ($items as $item) {
            if ($d[$item]['s']!='Off') {
                if (past($item)>$uit) {
                    sw($item, 'Off', basename(__FILE__).':'.__LINE__);
                    lg($item.' uitgeschakeld omdat we weg zijn');
                }
            }
        }
        $items=array(
            'eettafel',
            'zithoek',
            'kamer',
            'tobi',
            'alex',
            'lichtbadkamer'
        );
        foreach ($items as $item) {
            if ($d[$item]['s']>0) {
                if (past($item)>$uit) {
                    sl($item, 0, basename(__FILE__).':'.__LINE__);
                    lg($item.' uitgeschakeld omdat we weg zijn');
                }
            }
        }
    }

    /* -------------------------------------------- ALTIJD BIJ AUT0----------------------------*/
    if (past('diepvries_temp')>7200) {
        alert(
                'diepvriestemp',
                'Diepvries temp not updated since '.
                strftime("%k:%M:%S", $d['diepvries_temp']['t']),
                7200
            );
        }
        if ($d['voordeur']['s']=='On'&&past('voordeur')>598) {
        sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__);
    }

    if (past('deurbadkamer')>1200&&past('lichtbadkamer')>600) {
        if ($d['lichtbadkamer']['s']>0) {
            $new=round($d['lichtbadkamer']['s'] * 0.85, 0);
            if ($new<15) {
                $new=0;
            }
            sl('lichtbadkamer', $new, basename(__FILE__).':'.__LINE__);
        }
    }
    $items=array('living_set','badkamer_set','kamer_set','tobi_set','alex_set');
    foreach ($items as $i) {
        if ($d[$i]['m']!=0&&past($i)>7200) {
            storemode($i, 0, basename(__FILE__).':'.__LINE__);
        }
    }
    $items=array(
        'Rliving',
        'Rbureel',
        'RkeukenL',
        'RkeukenR',
        'RkamerL',
        'RkamerR',
        'Rtobi',
        'Ralex'
    );
    
    if (TIME<=strtotime('0:02')) {
        store('gasvandaag', 0, null, true);
        store('watervandaag', 0, null, true);
    } elseif (TIME>=strtotime('10:00')&&TIME<strtotime('10:05')) {
        $items=array('RkamerL','RkamerR','Rtobi','Ralex');
        foreach ($items as $i) {
            if ($d[$i]['m']!=0) {
            	storemode($i, 0, basename(__FILE__).':'.__LINE__);
            }
        }
    }
    if ($d['lgtv']['s']=='Off') {
        if (past('lgtv')>600) {
            if ($d['denon']['s']=='On'&&$d['denonpower']['s']=='OFF'&&past('denon')>600) {
                sw('denon', 'Off', basename(__FILE__).':'.__LINE__);
            }
            if ($d['nvidia']['s']=='On'&&$d['nvidia']['m']=='Off'&&past('nvidia')>600) {
                sw('nvidia', 'Off', basename(__FILE__).':'.__LINE__);
            }
            if ($d['tv']['s']=='On'&&past('tv')>3600&&past('lgtv')>3600) {
                sw('tv', 'Off', basename(__FILE__).':'.__LINE__);
            }
        }
    }
    if ($d['poort']['s']=='Closed'
        &&past('poort')>120
        &&past('poortrf')>120
        &&$d['poortrf']['s']=='On'
        &&(TIME<strtotime('8:00')||TIME>strtotime('8:40'))
    ) {
        sw('poortrf', 'Off', basename(__FILE__).':'.__LINE__);
    }
    if ($d['auto']['m']) {
        if ($d['Rliving']['s']<30&&$d['Rbureel']['s']<30&&$d['zon']['s']>40) {
            if ($d['jbl']['s']!='Off') {
                sw('jbl', 'Off', basename(__FILE__).':'.__LINE__);
            }
            if ($d['bureel']['s']!='Off') {
                sw('bureel', 'Off', basename(__FILE__).':'.__LINE__);
            }
            if ($d['kristal']['s']!='Off') {
                sw('kristal', 'Off', basename(__FILE__).':'.__LINE__);
            }
        }
    }
	if ((    
			($d['garage']['s']=='On'&&past('garage')>180)
			||
			($d['pirgarage']['s']=='On'&&past('pirgarage')>180)
        )
        &&TIME>strtotime('7:00')
        &&TIME<strtotime('23:00')
        &&$d['poort']['s']=='Closed'
        &&$d['achterdeur']['s']=='Closed'
    ) {
        if ($d['dampkap']['s']=='Off') {
            double('dampkap', 'On');
        }
    } elseif (
    	($d['garage']['s']=='Off'&&past('garage')>270&&$d['pirgarage']['s']=='Off'&&past('pirgarage')>270)
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
    if ($d['wc']['s']=='On' && past('wc')>540) {
        sw('wc', 'Off', basename(__FILE__).':'.__LINE__);
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
			if ($denonmain['ZonePower']['value']=='ON'&&$denonsec['Power']['value']=='OFF') {
				denon('Z2ON');
			} elseif ($denonmain['ZonePower']['value']=='OFF'&&$denonsec['Power']['value']=='ON') {
				denon('Z2OFF');
			}
		}
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
                    sw('bose101', 'Off', basename(__FILE__).':'.__LINE__);
                    sw('bose102', 'Off', basename(__FILE__).':'.__LINE__);
                    sw('bose103', 'Off', basename(__FILE__).':'.__LINE__);
                    sw('bose104', 'Off', basename(__FILE__).':'.__LINE__);
                    sw('bose105', 'Off', basename(__FILE__).':'.__LINE__);
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
                    sw('bose102', 'Off', basename(__FILE__).':'.__LINE__);
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
                    sw('bose104', 'Off', basename(__FILE__).':'.__LINE__);
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
        &&$d['zon']['s']>200
        &&$d['RkamerL']['s']==0
        &&$d['RkamerR']['s']==0
    ) {
        if (TIME>strtotime('6:00')&&TIME<strtotime('8:00')) {
			sl('kamer', 0, basename(__FILE__).':'.__LINE__);
        } elseif (past('kamer')>900) {
        	storemode('kamer', 1);
        }
    }
    if ($d['tobi']['s']>0
        &&$d['zon']['s']>200
        &&$d['Rtobi']['s']==0
        &&past('tobi')>900
    ) {
        storemode('tobi', 1);
    }
    if ($d['alex']['s']>0
        &&$d['zon']['s']>200
        &&$d['Ralex']['s']==0
        &&past('alex')>900
    ) {
        storemode('alex', 1);
    }
    if ($d['eettafel']['s']>0
        &&$d['Rbureel']['s']==0
        &&$d['Rliving']['s']==0
        &&$d['zon']['s']>100
        &&past('eettafel')>2700
    ) {
        storemode('eettafel', 1);
    }
    if ($d['zithoek']['s']>0
        &&$d['Rbureel']['s']==0
        &&$d['Rliving']['s']==0
        &&$d['zon']['s']>100
        &&past('zithoek')>2700
    ) {
        storemode('zithoek', 1);
    }
    if ($d['Rliving']['s']>60&&$d['achterdeur']['s']=='Closed') {
        if ($d['tuin']['s']=='On') {
            sw('tuin', 'Off', basename(__FILE__).':'.__LINE__);
        }
        if ($d['terras']['s']>0) {
            sl('terras', 0, basename(__FILE__).':'.__LINE__);
        }
    }
    if ($d['luifel']['s']==0&&$d['ledluifel']['s']>0) {
        sl('ledluifel', 0, basename(__FILE__).':'.__LINE__);
    }
}
    /* -------------------------------------------- ALTIJD ----------------------------*/
if ($d['heater1']['s']!='Off'
    &&$d['heater2']['s']=='Off'
    &&$d['heater3']['s']=='Off'
    &&$d['heater4']['s']=='Off'
    &&past('heater1')>120
    &&past('heater2')>90
    &&past('heater3')>90
    &&past('heater4')>90
) {
    sw('heater1', 'Off', basename(__FILE__).':'.__LINE__);
}
if ($d['diepvries']['s']!='On'
    &&$d['diepvries_temp']['s']>$d['diepvries_temp']['m']
    &&past('diepvries')>1780
) {
    sw('diepvries', 'On', basename(__FILE__).':'.__LINE__);
} elseif ($d['diepvries']['s']!='Off'
    &&$d['diepvries_temp']['s']<=$d['diepvries_temp']['m']
    &&past('diepvries')>280
) {
    sw('diepvries', 'Off', basename(__FILE__).':'.__LINE__);
} elseif ($d['diepvries']['s']!='Off'
    &&past('diepvries')>7200
) {
    sw('diepvries', 'Off', basename(__FILE__).':'.__LINE__);
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
if ($d['water']['s']=='On') {
    if (past('water')>$d['water']['m']) {
        double('water', 'Off');
    }
}
if (TIME>$d['civil_twilight']['s']&&TIME<$d['civil_twilight']['m']) {
    if ($d['auto']['m']!=true) {
        storemode('auto', true);
        $d['auto']['m']=true;
    }
} else {
    if ($d['auto']['m']!=false ) {
        storemode('auto', false);
        $d['auto']['m']=false;
    }
}
//SMAPPEE
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
        $vv=round($data['consumptions'][0]['consumption']/1000, 1);
        if ($d['el']['m']!=$vv) {
	        storemode('el', $vv);
	    }
        $zonvandaag=round($data['consumptions'][0]['solar']/1000, 1);
        if ($d['zonvandaag']['s']!=$zonvandaag) {
	        store('zonvandaag', $zonvandaag);
	    }
        $gas=$d['gasvandaag']['s']/100;
        $water=$d['watervandaag']['s']/1000;

        @file_get_contents(
            $vurl."verbruik=$vv&gas=$gas&water=$water&zon=$zonvandaag"
        );
    }
    curl_close($chconsumption);
}
ping('192.168.2.11');
ping('192.168.2.12');
ping('192.168.2.13');
ping('192.168.2.14');
ping('192.168.2.15');
ping('192.168.2.101');
ping('192.168.2.102');
ping('192.168.2.103');
ping('192.168.2.104');
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