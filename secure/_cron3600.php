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
$sql="SELECT id,date,value
    FROM battery t1
    WHERE t1.date = (
        SELECT t2.date
        FROM battery t2
        WHERE t2.id = t1.id
        ORDER BY t2.date DESC
        LIMIT 1
    );";
if (!$result=$db->query($sql)) {
    die('There was an error running the query ['.$sql.' - '.$db->error.']');
}
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $batterydevices[]=$row['id'];
    $items[$row['id']]=$row;
}
$date=strftime("%F", TIME);
$xml=json_decode(
    json_encode(
        simplexml_load_string(
            file_get_contents(
                '/domoticz/Config/zwcfg_0xe9238f6e.xml'
            ),
            "SimpleXMLElement",
            LIBXML_NOCDATA
        )
    ),
    true
);
foreach ($xml['Node'] as $node) {
    foreach ($node['CommandClasses']['CommandClass'] as $cmd) {
        if (isset($cmd['Value']['@attributes']['label'])) {
            if ($cmd['Value']['@attributes']['label']=='Battery Level') {
                $id=$node['@attributes']['id'];
                $name=$node['@attributes']['name'];
                $value=$cmd['Value']['@attributes']['value'];
                if ($value>100) {
                    $value=100;
                }
                if (!in_array($id, $batterydevices)) {
                    $query="INSERT INTO `batterydevices` (`id`,`name`)
                        VALUES ('$id','$name')
                        ON DUPLICATE KEY UPDATE `name`='$name';";
                    if (!$result=$db->query($query)) {
                        die(
                            'There was an error running the query ['
                            .$query.'-'.$db->error.']'
                        );
                    }
                }
                if (isset($items[$id]['value'])&&$items[$id]['value']!=$value) {
                    if ($value<50) {
                        alert(
                            'Batterij'.$name,
                            'Batterij '.$name.' '.$value.'%',
                            43200
                        );
                    }
                    $query="INSERT INTO `battery` (`date`,`id`,`value`)
                        VALUES ('$date','$id','$value')
                        ON DUPLICATE KEY UPDATE `value`='$value';";
                    if (!$result=$db->query($query)) {
                        die(
                            'There was an error running the query ['
                            .$query.'-'.$db->error.']'
                        );
                    }
                }
            }
        }
    }
}
unset($xml);
$ctx=stream_context_create(array('http'=>array('timeout'=>10)));
$data=json_decode(
    file_get_contents(
        'https://verbruik.egregius.be/tellerjaar.php',
        false,
        $ctx
    ),
    true
);
if (!empty($data)) {
    store('jaarteller', $data['jaarteller']);
    if ($data['zonpercent']!=$d['zonvandaag']['m']) {
        storemode('zonvandaag', $data['zonpercent']);
    }
}
if (date('I', TIME) == 1) {
    if ($d['auto']['m']==false) {
        storemode('auto', true);
        $d['auto']['m']=true;
    }
} else {
    if ($d['auto']['m']==true) {
        storemode('auto', false);
        $d['auto']['m']=false;
    }
}
$sunrise=json_decode(
    file_get_contents(
        'http://api.sunrise-sunset.org/json?lat='
        .$lat.'&lng='
        .$lon.'&date=today&formatted=0'
    ),
    true
);
if (isset($sunrise['results']['civil_twilight_begin'])) {
    if (strtotime($sunrise['results']['civil_twilight_begin'])!=$d['civil_twilight']['s']) {
        store('civil_twilight', strtotime($sunrise['results']['civil_twilight_begin']));
    }
    if (strtotime($sunrise['results']['civil_twilight_end'])!=$d['civil_twilight']['m']) {
        storemode(
            'civil_twilight',
            strtotime(
                $sunrise['results']['civil_twilight_end']
            )
        );
    }
    if (TIME>$d['civil_twilight']['s']&&TIME<$d['civil_twilight']['m']) {
        if ($d['auto']['m']!=true) {
            storemode('auto', true);
            $d['auto']['m']=true;
        }
        $uv=json_decode(
            shell_exec(
                "curl -X GET 'https://api.openuv.io/api/v1/uv?lat=".
                $lat."&lng=".
                $lon."' -H 'x-access-token: 3ede211d20c3fac5d9d1df3b5282ebf2'"
            ),
            true
        );
        if (isset($uv['result'])) {
            if ($uv['result']['uv']!=$d['uv']['s']) {
                store('uv', round($uv['result']['uv'], 1));
            }
            if ($uv['result']['uv_max']!=$d['uv']['m']) {
                storemode('uv', round($uv['result']['uv_max'], 1));
            }
        }
    } else {
        if ($d['auto']['m']!=false ) {
            storemode('auto', false);
            $d['auto']['m']=false;
        }
    }
}
//Update and clean SQL database

$limit=86400000;
echo '<h2>Putting temps min,avg,max into temp_hour</h2>';
$stmt=$db->query(
    "SELECT left(stamp,13) as stamp,
        min(buiten) as buiten_min,
        max(buiten) as buiten_max,
        avg(buiten) as buiten_avg,
        min(living) as living_min,
        max(living) as living_max,
        avg(living) as living_avg,
        min(badkamer) as badkamer_min,
        max(badkamer) as badkamer_max,
        avg(badkamer) as badkamer_avg,
        min(kamer) as kamer_min,
        max(kamer) as kamer_max,
        avg(kamer) as kamer_avg,
        min(tobi) as tobi_min,
        max(tobi) as tobi_max,
        avg(tobi) as tobi_avg,
        min(alex) as alex_min,
        max(alex) as alex_max,
        avg(alex) as alex_avg,
        min(zolder) as zolder_min,
        max(zolder) as zolder_max,
        avg(zolder) as zolder_avg
    FROM temp
    GROUP BY left(stamp,13)"
);
while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
    $stamp=$row['stamp'];
    $buiten_min=$row['buiten_min'];
    $buiten_max=$row['buiten_max'];
    $buiten_avg=$row['buiten_avg'];
    $living_min=$row['living_min'];
    $living_max=$row['living_max'];
    $living_avg=$row['living_avg'];
    $badkamer_min=$row['badkamer_min'];
    $badkamer_max=$row['badkamer_max'];
    $badkamer_avg=$row['badkamer_avg'];
    $kamer_min=$row['kamer_min'];
    $kamer_max=$row['kamer_max'];
    $kamer_avg=$row['kamer_avg'];
    $tobi_min=$row['tobi_min'];
    $tobi_max=$row['tobi_max'];
    $tobi_avg=$row['tobi_avg'];
    $alex_min=$row['alex_min'];
    $alex_max=$row['alex_max'];
    $alex_avg=$row['alex_avg'];
    $zolder_min=$row['zolder_min'];
    $zolder_max=$row['zolder_max'];
    $zolder_avg=$row['zolder_avg'];
    $db->query(
        "INSERT INTO `temp_hour`
            (`stamp`,
                `buiten_min`,
                `buiten_max`,
                `buiten_avg`,
                `living_min`,
                `living_max`,
                `living_avg`,
                `badkamer_min`,
                `badkamer_max`,
                `badkamer_avg`,
                `kamer_min`,
                `kamer_max`,
                `kamer_avg`,
                `tobi_min`,
                `tobi_max`,
                `tobi_avg`,
                `alex_min`,
                `alex_max`,
                `alex_avg`,
                `zolder_min`,
                `zolder_max`,
                `zolder_avg`
            )
        VALUES
            ('$stamp',
            '$buiten_min',
            '$buiten_max',
            '$buiten_avg',
            '$living_min',
            '$living_max',
            '$living_avg',
            '$badkamer_min',
            '$badkamer_max',
            '$badkamer_avg',
            '$kamer_min',
            '$kamer_max',
            '$kamer_avg',
            '$tobi_min',
            '$tobi_max',
            '$tobi_avg',
            '$alex_min',
            '$alex_max',
            '$alex_avg',
            '$zolder_min',
            '$zolder_max',
            '$zolder_avg'
        )
        ON DUPLICATE KEY UPDATE
            `buiten_min`='$buiten_min',
            `buiten_max`='$buiten_max',
            `buiten_avg`='$buiten_avg',
            `living_min`='$living_min',
            `living_max`='$living_max',
            `living_avg`='$living_avg',
            `badkamer_min`='$badkamer_min',
            `badkamer_max`='$badkamer_max',
            `badkamer_avg`='$badkamer_avg',
            `kamer_min`='$kamer_min',
            `kamer_max`='$kamer_max',
            `kamer_avg`='$kamer_avg',
            `tobi_min`='$tobi_min',
            `tobi_max`='$tobi_max',
            `tobi_avg`='$tobi_avg',
            `alex_min`='$alex_min',
            `alex_max`='$alex_max',
            `alex_avg`='$alex_avg',
            `zolder_min`='$zolder_min',
            `zolder_max`='$zolder_max',
            `zolder_avg`='$zolder_avg';"
    );
}


echo '<h2>Putting buiten temp to verbruik.egregius.be</h2>';
$stmt = $db->query(
    "SELECT
        left(stamp,10) as stamp,
        min(buiten_min) as buiten_min,
        max(buiten_max) as buiten_max,
        avg(buiten_avg) as buiten_avg
    FROM temp_hour
    GROUP BY left(stamp,10)
    ORDER BY `stamp` DESC
    LIMIT 0,10"
);
$dbe=new mysqli('95.170.95.33', 'home', 'H0m€', 'verbruik');
if ($dbe->connect_errno>0) {
    die('Unable to connect to database ['.$dbe->connect_error.']');
}
while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
    $stamp=$row['stamp'];
    $buiten_min=$row['buiten_min'];
    $buiten_max=$row['buiten_max'];
    $buiten_avg=$row['buiten_avg'];
    $query = "
        INSERT INTO `temp_buiten`
            (`stamp`,`min`,`max`,`avg`)
        VALUES
            ('$stamp','$buiten_min','$buiten_max','$buiten_avg')
        ON DUPLICATE KEY UPDATE
            `min`='$buiten_min',`max`='$buiten_max',`avg`='$buiten_avg';";
    if (!$result = $dbe->query($query)) {
        die('There was an error running the query ['.$query.'-'.$dbe->error.']');
    }
}

echo '<hr>';
$remove=strftime("%Y-%m-%d", TIME-691200);
$db->query("delete from temp where stamp < '$remove'");

$remove=strftime("%Y-%m-%d %H:%M", TIME-172700);
$db->query("delete from regen where stamp < '$remove'");

//Clean log table

$stmt=$db->query("SELECT count(timestamp) as count FROM `log`");
$data=$stmt->fetch(PDO::FETCH_ASSOC);
lg('Count log = '.$data['count']);
if ($data['count']>1000000) {
    $db->query("DELETE FROM `log` ORDER BY timestamp ASC LIMIT 1000");
}