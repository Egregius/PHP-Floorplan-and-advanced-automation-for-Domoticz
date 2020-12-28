<?php
/**
 * Pass2PHP
 * php version 7.3
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
$user='cron3600';
$sql="SELECT id,date,value
    FROM battery t1
    WHERE t1.date = (
        SELECT t2.date
        FROM battery t2
        WHERE t2.id = t1.id
        ORDER BY t2.date DESC
        LIMIT 1
    );";
if (!isset($db)) {
	$db=new PDO("mysql:host=localhost;dbname=$dbname;", $dbuser, $dbpass);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
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
                '/domoticz/Config/ozwcache_0xe9238f6e.xml'
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
//                echo $id.' '.$name.' '.$value.'<br><pre>';print_r($cmd);echo '</pre>';
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
    store('jaarteller', $data['jaarteller'], basename(__FILE__).':'.__LINE__);
    if ($data['zonpercent']!=$d['zonvandaag']['m']) {
        storemode('zonvandaag', $data['zonpercent'], basename(__FILE__).':'.__LINE__);
    }
}

$data=json_decode(file_get_contents('http://192.168.2.2:8080/json.htm?type=devices&rid=1'), true);
if (isset($data['CivTwilightStart'])) {
	$time=TIME;
	$name='civil_twilight';
	$status=strtotime($data['CivTwilightStart']);
	$mode=strtotime($data['CivTwilightEnd']);
	$db->query("INSERT INTO devices (n,s,m,t) VALUES ('$name','$status','$mode','$time') ON DUPLICATE KEY UPDATE s='$status', m='$mode', t='$time';");
	$name='Sun';
	$status=strtotime($data['Sunrise']);
	$mode=strtotime($data['Sunset']);
	$icon=strtotime($data['SunAtSouth']);
	$db->query("INSERT INTO devices (n,s,m,icon,t) VALUES ('$name', '$status', '$mode', '$icon', '$time') ON DUPLICATE KEY UPDATE s='$status', m='$mode', icon='$icon', t='$time';");
	if (TIME>$status&&TIME<$mode) {
		$uv=json_decode(
			shell_exec(
				"curl -X GET 'https://api.openuv.io/api/v1/uv?lat=".$lat."&lng=".$lon."' -H 'x-access-token: ".$openuv."'"
			),
			true
		);
		if (isset($uv['result'])) {
			if ($uv['result']['uv']!=$d['uv']['s']) {
				store('uv', round($uv['result']['uv'], 1), basename(__FILE__).':'.__LINE__);
			}
			if ($uv['result']['uv_max']!=$d['uv']['m']) {
				storemode('uv', round($uv['result']['uv_max'], 1), basename(__FILE__).':'.__LINE__);
			}
		}
	}
}

//Update and clean SQL database

$limit=86400000;
//Putting temps min,avg,max into temp_hour
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


//Putting buiten temp to verbruik.egregius.be
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



if ($d['buiten_temp']['s']>2&&$d['buiten_temp']['s']<30) {
	$low=40;
	$high=40;
} elseif ($d['buiten_temp']['s']< -5||$d['buiten_temp']['s']>35) {
	$low=60;
	$high=100;
} else {
	$low=50;
	$high=70;
}
$daikin=json_decode($d['daikinliving']['s']);
if ($daikin->adv == '') {
	$powermode=0;
} else if (strstr($daikin->adv, '/')) {
	$advs=explode("/", $daikin->adv);
	if ($advs[0]==2) $powermode=2;
	else if ($advs[0]==12) $powermode=1;
	else $powermode=0;
} else {
	if ($daikin->adv==13)  $powermode=0; //Normal
	else if ($daikin->adv==12)  $powermode=1; // Eco
	else if ($daikin->adv==2)  $powermode=2; // Power
	else if ($daikin->adv=='')  $powermode=0;
}
if ($powermode<2) {
	if (TIME>=strtotime('5:00')&&TIME<strtotime('20:00')) {
		file_get_contents('http://192.168.2.111/aircon/set_demand_control?type=1&en_demand=1&mode=2&max_pow='.$high.'&scdl_per_day=4&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0');
	} else {
		file_get_contents('http://192.168.2.111/aircon/set_demand_control?type=1&en_demand=1&mode=2&max_pow='.$low.'&scdl_per_day=4&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0');
	}
}
$daikin=json_decode($d['daikinkamer']['s']);
if ($daikin->adv == '') {
	$powermode=0;
} else if (strstr($daikin->adv, '/')) {
	$advs=explode("/", $daikin->adv);
	if ($advs[0]==2) $powermode=2;
	else if ($advs[0]==12) $powermode=1;
	else $powermode=0;
} else {
	if ($daikin->adv==13)  $powermode=0; //Normal
	else if ($daikin->adv==12)  $powermode=1; // Eco
	else if ($daikin->adv==2)  $powermode=2; // Power
	else if ($daikin->adv=='')  $powermode=0;
}
if ($powermode<2) {
	if (TIME>=strtotime('5:00')&&TIME<strtotime('21:00')) {
		file_get_contents('http://192.168.2.112/aircon/set_demand_control?type=1&en_demand=1&mode=2&max_pow='.$low.'&scdl_per_day=4&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0');
	} else {
		file_get_contents('http://192.168.2.112/aircon/set_demand_control?type=1&en_demand=1&mode=2&max_pow='.$high.'&scdl_per_day=4&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0');
	}
}
$daikin=json_decode($d['daikinalex']['s']);
if ($daikin->adv == '') {
	$powermode=0;
} else if (strstr($daikin->adv, '/')) {
	$advs=explode("/", $daikin->adv);
	if ($advs[0]==2) $powermode=2;
	else if ($advs[0]==12) $powermode=1;
	else $powermode=0;
} else {
	if ($daikin->adv==13)  $powermode=0; //Normal
	else if ($daikin->adv==12)  $powermode=1; // Eco
	else if ($daikin->adv==2)  $powermode=2; // Power
	else if ($daikin->adv=='')  $powermode=0;
}
if ($powermode<2) {
	if (TIME>=strtotime('5:00')&&TIME<strtotime('19:00')) {
		file_get_contents('http://192.168.2.113/aircon/set_demand_control?type=1&en_demand=1&mode=2&max_pow='.$low.'&scdl_per_day=4&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0');
	} else {
		file_get_contents('http://192.168.2.113/aircon/set_demand_control?type=1&en_demand=1&mode=2&max_pow='.$high.'&scdl_per_day=4&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0');
	}
}

foreach (array('living', 'kamer', 'alex') as $k) {
	if ($k=='living') $ip=111;
	elseif ($k=='kamer') $ip=112;
	elseif ($k=='alex') $ip=113;
	sleep(2);
	$data=file_get_contents('http://192.168.2.'.$ip.'/aircon/get_day_power_ex');
	$data=explode(',', $data);
	if ($data[0]=='ret=OK') {
		$curr_day_heat=explode('=', $data[1]);
		${$k.'heat'}=array_sum(explode('/', $curr_day_heat[1]));
		$prev_1day_heat=explode('=', $data[2]);
		${$k.'prevheat'}=array_sum(explode('/', $prev_1day_heat[1]));
		$curr_day_cool=explode('=', $data[3]);
		${$k.'cool'}=array_sum(explode('/', $curr_day_cool[1]));
		$prev_1day_cool=explode('=', $data[4]);
		${$k.'prevcool'}=array_sum(explode('/', $prev_1day_cool[1]));
	}
//print_r($data);
}
$date=strftime('%F', TIME);
$db=dbconnect();
$db->query("INSERT INTO daikin (date,livingheat,livingcool,kamerheat,kamercool,alexheat,alexcool) VALUES ('$date','$livingheat','$livingcool','$kamerheat','$kamercool','$alexheat','$alexcool') ON DUPLICATE KEY UPDATE date='$date',livingheat='$livingheat',livingcool='$livingcool',kamerheat='$kamerheat',kamercool='$kamercool',alexheat='$alexheat',alexcool='$alexcool';");
$date=strftime('%F', TIME-86400);
$db->query("INSERT INTO daikin (date,livingheat,livingcool,kamerheat,kamercool,alexheat,alexcool) VALUES ('$date','$livingprevheat','$livingprevcool','$kamerprevheat','$kamerprevcool','$alexprevheat','$alexprevcool') ON DUPLICATE KEY UPDATE date='$date',livingheat='$livingprevheat',livingcool='$livingprevcool',kamerheat='$kamerprevheat',kamercool='$kamerprevcool',alexheat='$alexprevheat',alexcool='$alexprevcool';");

foreach (array('living', 'kamer', 'alex') as $k) {
	file_get_contents('http://192.168.2.'.$ip.'/aircon/set_special_mode?en_streamer=0');
	sleep(2);
}

if (TIME<strtotime('3:00')) {
	for ($x=5;$x>=0;$x--) {
		$date=date("Y-m-d", (TIME-($x*86400)));
		$query="INSERT IGNORE INTO `pluvio` (`date`, `rain`) VALUES ('$date', '0');";
		lg($query);
		if(!$result=$db->query($query)){lg($db->error);die('There was an error running the query ['.$query.'-'.$db->error.']');}
	}
}
/* Clean old database records */
$remove=strftime("%F %T", TIME-90000);
$stmt=$db->query("delete from temp where stamp < '$remove'");
//lg(' Deleted '.$stmt->rowCount().' records from temp');
$remove=strftime("%F %T", TIME-200000);
$stmt=$db->query("delete from regen where stamp < '$remove'");
//lg(' Deleted '.$stmt->rowCount().' records from regen');

RefreshZwave(128);
