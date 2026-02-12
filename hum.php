<?php
require 'secure/functions.php';
require '/var/www/authentication.php';

// Exacte tijdstippen voor 24u, week en 100 dagen
$dag = date("Y-m-d H:i:s", strtotime("-24 hours"));
$week = date("Y-m-d H:i:s", strtotime("-7 days"));
$maand = date("Y-m-d H:i:s", strtotime("-100 days"));

$db = new mysqli('192.168.2.23', $dbuser, $dbpass, $dbname);
if ($db->connect_errno > 0) die('Unable to connect to database [' . $db->connect_error . ']');

$sensors = array(
    'living_hum'   => array('Naam' => 'Living', 'Color' => '#FF1111'),
    'kamer_hum'    => array('Naam' => 'Kamer', 'Color' => '#44FF44'),
    'alex_hum'     => array('Naam' => 'Alex', 'Color' => '#00EEFF'),
    'badkamer_hum' => array('Naam' => 'Badk', 'Color' => '#6666FF'),
);

// --- LOGICA VOOR SENSOR SELECTIE ---
if (!empty(array_intersect_key($_GET, $sensors))) {
    foreach ($sensors as $k => $v) {
        $_SESSION['sensors_hum'][$k] = isset($_GET[$k]);
    }
} elseif (!isset($_SESSION['sensors_hum'])) {
    $_SESSION['sensors_hum'] = array('living_hum' => true, 'kamer_hum' => true, 'alex_hum' => true, 'badkamer_hum' => true);
}

$active_sensors = array_filter($_SESSION['sensors_hum']);
if (count($active_sensors) == 0) {
    $_SESSION['sensors_hum']['living_hum'] = true;
    $active_sensors = array('living_hum' => true);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Temperaturen</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
			background-color: #000;
			color: #fff;
			font-family: sans-serif;
			margin: 0;
			padding: 10px;
			box-sizing: border-box;
			overflow-x: hidden;
			width: 100vw;
		}
        .header-nav { display: flex; width: 100%; gap: 5px; margin-bottom: 20px; }
        .header-nav form { flex: 1; }
        .header-nav .btn { width: 100%; padding: 8px 5px; font-size: 1.1em; text-align: center; cursor: pointer; background: #333; color: #fff; border: 1px solid #444; border-radius: 4px; }
        .header-nav .btna { background: #ffba00;color:#000;}
        .chart-container { width: 100%; margin-bottom: 30px; background: #000; height: 650px; }
        .btn-container { margin: 20px 0; }
        input[type=checkbox] { display: none; }
        .sensor-label { display: inline-block; padding: 8px 26px; margin: 0 2px 8px 0; border-radius: 6px; border: 1px solid #444; cursor: pointer; font-size: 1em; }
        <?php foreach ($sensors as $k => $v) {
            echo "#$k + label { color: $v[Color]; border-color: $v[Color]; opacity: 0.5; }
                  #$k:checked + label { opacity: 1; background-color: $v[Color]; color: #000; }";
        } ?>
        h3 { color: #888; font-weight: normal; font-size: 1.1em; margin: 10px 0; }
    </style>
</head>
<body style="width: 100%; max-width: 1000px; margin: 0 auto;">
<div class="header-nav">
    <form action="floorplan.php"><input type="submit" class="btn" value="Plan"/></form>
    <form action="/temp.php"><input type="submit" class="btn" value="Temp"/></form>
    <form action="/hum.php"><input type="submit" class="btn btna" value="Hum"/></form>
</div>

<div class="btn-container">
    <form method="GET" id="sensorform">
        <?php foreach ($sensors as $k => $v) {
            $checked = (!empty($_SESSION['sensors_hum'][$k]) ? 'checked' : '');
            echo '<input type="checkbox" name="'.$k.'" id="'.$k.'" onChange="this.form.submit()" '.$checked.'><label for="'.$k.'" class="sensor-label">'.$v['Naam'].'</label>';
        } ?>
    </form>
</div>
<?php

function getChartData($db, $query, $sensors, $active_sensors, $isSingle) {
    $result = $db->query($query);
    $data = ['labels' => [], 'datasets' => []];
    if (!$result) return $data;

    if ($isSingle && count($active_sensors) == 1) {
        $data['datasets'][] = ['label' => 'Min', 'borderColor' => '#6666FF', 'data' => [], 'fill' => false, 'borderWidth' => 1, 'pointRadius' => 0, 'tension' => 0.1];
        $data['datasets'][] = ['label' => 'Gem', 'borderColor' => '#00FF00', 'data' => [], 'fill' => false, 'borderWidth' => 3, 'pointRadius' => 0, 'tension' => 0.1];
        $data['datasets'][] = ['label' => 'Max', 'borderColor' => '#FF3333', 'data' => [], 'fill' => false, 'borderWidth' => 1, 'pointRadius' => 0, 'tension' => 0.1];
    } else {
        foreach ($active_sensors as $k => $v) {
            $data['datasets'][] = [
                'label' => $sensors[$k]['Naam'],
                'borderColor' => $sensors[$k]['Color'],
                'data' => [],
                'tension' => 0.1,
                'pointRadius' => 0,
                'borderWidth' => 2
            ];
        }
    }

    while ($row = $result->fetch_assoc()) {
        $data['labels'][] = $row['tijdlabel'];
        if ($isSingle && count($active_sensors) == 1) {
            $data['datasets'][0]['data'][] = (float)$row['MIN'];
            $data['datasets'][1]['data'][] = (float)$row['AVG'];
            $data['datasets'][2]['data'][] = (float)$row['MAX'];
        } else {
            $idx = 0;
            foreach ($active_sensors as $k => $v) {
                $data['datasets'][$idx++]['data'][] = isset($row[$k]) ? (float)$row[$k] : null;
            }
        }
    }
    return $data;
}

$active_keys = array_keys($active_sensors);
$dagData = getChartData($db, "SELECT stamp, DATE_FORMAT(stamp, '%H:%i') as tijdlabel, " . implode(',', $active_keys) . " FROM `temp` WHERE stamp >= '$dag' AND living_hum IS NOT NULL ORDER BY stamp ASC", $sensors, $active_sensors, false);

$q_week = "SELECT MIN(stamp) as s, DATE_FORMAT(stamp, '%a %H:%i') as tijdlabel";
foreach ($active_sensors as $k => $v) {
    if (count($active_sensors) == 1) $q_week .= ", MIN($k) AS MIN, AVG($k) AS AVG, MAX($k) AS MAX";
    else $q_week .= ", AVG($k) AS $k";
}
$q_week .= " FROM `temp` WHERE stamp > '$week' AND living_hum IS NOT NULL GROUP BY UNIX_TIMESTAMP(stamp) DIV 3600 ORDER BY s ASC";
$weekData = getChartData($db, $q_week, $sensors, $active_sensors, true);

$q_maand = "SELECT MIN(stamp) as s, DATE_FORMAT(stamp, '%d/%m') as tijdlabel";
foreach ($active_sensors as $k => $v) {
    if (count($active_sensors) == 1) $q_maand .= ", MIN($k) AS MIN, AVG($k) AS AVG, MAX($k) AS MAX";
    else $q_maand .= ", AVG($k) AS $k";
}
$q_maand .= " FROM `temp` WHERE stamp > '$maand' AND living_hum IS NOT NULL GROUP BY UNIX_TIMESTAMP(stamp) DIV 86400 ORDER BY s ASC";
$maandData = getChartData($db, $q_maand, $sensors, $active_sensors, true);
?>

<div class="chart-container"><canvas id="chartDag"></canvas></div>
<div class="chart-container"><canvas id="chartWeek"></canvas></div>
<div class="chart-container"><canvas id="chartMaand"></canvas></div>

<script>
Chart.defaults.animation = false;

const commonOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        x: { ticks: { color: '#666', autoSkip: true, maxTicksLimit: 10 }, grid: { display: false } },
        y: {
            ticks: {
                color: '#888',
                stepSize: 1,
                precision: 0,
                callback: v => v + '%'
            },
            grid: { color: '#222' }
        }
    },
    plugins: {
        legend: { display: false },
        tooltip: { enabled: false } // <-- Dit schakelt de tekstwolkjes volledig uit
    }
};

function createChart(id, data) {
    if (!data.labels || !data.labels.length) return;
    new Chart(document.getElementById(id), {
        type: 'line',
        data: data,
        options: commonOptions
    });
}

createChart('chartDag', <?php echo json_encode($dagData); ?>);
createChart('chartWeek', <?php echo json_encode($weekData); ?>);
createChart('chartMaand', <?php echo json_encode($maandData); ?>);
</script>

<?php
$ms = ((61 - date("s")) * 1000) + 62000;
echo "<div style='padding:20px; color:#444; font-size: 0.8em;'>$udevice | Update in " . ($ms / 1000) . "s</div>";
echo '<script>setTimeout(() => { window.location.reload(); }, ' . $ms . ');</script>';
$db->close();
?>
</body>
</html>
