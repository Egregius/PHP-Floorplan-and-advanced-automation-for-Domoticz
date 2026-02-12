<?php
require 'secure/functions.php';
require '/var/www/authentication.php';

$sensor_id = isset($_REQUEST['sensor']) ? $_REQUEST['sensor'] : 998;
$dag = date("Y-m-d H:i:s", strtotime("-24 hours"));

$db = new mysqli('192.168.2.23', $dbuser, $dbpass, $dbname);
if ($db->connect_errno > 0) die('Unable to connect to database [' . $db->connect_error . ']');

// Identieke sensor-definities als in temp.php
$sensors = array(
    'living'   => array('id' => 147, 'Naam' => 'Living',  'Color' => '#FF1111'),
    'badkamer' => array('id' => 246, 'Naam' => 'Badkamr', 'Color' => '#6666FF'),
    'kamer'    => array('id' => 278, 'Naam' => 'Kamer',   'Color' => '#44FF44'),
    'alex'     => array('id' => 244, 'Naam' => 'Alex',    'Color' => '#00EEFF'),
    'waskamer' => array('id' => 356, 'Naam' => 'Waskamr', 'Color' => '#EEEE00'),
    'zolder'   => array('id' => 293, 'Naam' => 'Zolder',  'Color' => '#EE33EE'),
    'buiten'   => array('id' => 329, 'Naam' => 'Buiten',  'Color' => '#FFFFFF'),
);

$active_keys = [];
if ($sensor_id == 999) {
    $active_keys = array_keys($sensors);
    $titel = "Alles";
} elseif ($sensor_id == 998) {
    $active_keys = ['living', 'badkamer', 'kamer', 'waskamer', 'alex'];
    $titel = "Binnen";
} else {
    foreach ($sensors as $key => $val) {
        if ($val['id'] == $sensor_id) {
            $active_keys = [$key];
            $titel = $val['Naam'];
            break;
        }
    }
}
if (empty($active_keys)) $active_keys = ['living'];

$query = "SELECT stamp, DATE_FORMAT(stamp, '%H:%i') as tijdlabel, " . implode(',', $active_keys) . "
          FROM `temp`
          WHERE stamp >= '$dag'
          ORDER BY stamp ASC";
$result = $db->query($query);

$labels = [];
$datasets = [];
$min_val = 100;
$max_val = -100;

foreach ($active_keys as $key) {
    $datasets[$key] = [
        'label' => $sensors[$key]['Naam'],
        'borderColor' => $sensors[$key]['Color'],
        'borderWidth' => 4,
        'pointRadius' => 0,
        'tension' => 0.1,
        'data' => []
    ];
}

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['tijdlabel'];
    foreach ($active_keys as $key) {
        if ($row[$key] !== null) {
            $val = (float)$row[$key];
            $datasets[$key]['data'][] = $val;
            if ($val < $min_val) $min_val = $val;
            if ($val > $max_val) $max_val = $val;
        } else {
            $datasets[$key]['data'][] = null;
        }
    }
}

$min_val = ($min_val == 100) ? 15 : floor($min_val - 1);
$max_val = ($max_val == -100) ? 25 : ceil($max_val + 1);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Big Temp - <?php echo $titel; ?></title>
    <link href="/styles/temp.css?v=6" rel="stylesheet" type="text/css"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #000; color: #fff; font-family: sans-serif; margin: 0; overflow: hidden; }

        /* Navigatie: horizontaal scrollbaar op kleine schermen indien nodig */
        .nav-container {
            position: absolute; top: 0; left: 0; width: 100%; z-index: 10;
            background: rgba(0,0,0,0.8); padding: 12px 5px;
            text-align: center; white-space: nowrap; overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .nav-container a {
            text-decoration: none; font-size: 13px; margin: 0 6px; font-weight: bold;
            display: inline-block;
        }
        .nav-active { border-bottom: 2px solid #fff; padding-bottom: 2px; }

        .chart-wrapper { width: 100vw; height: 100vh; padding-top: 45px; box-sizing: border-box; }
        .time-label { position: absolute; bottom: 8px; right: 12px; color: #444; font-size: 11px; font-family: monospace; }
    </style>
</head>
<body>

<div class="nav-container">
    <?php
    foreach ($sensors as $key => $val) {
        $active_class = ($sensor_id == $val['id']) ? 'class="nav-active"' : '';
        echo '<a href="tempbig.php?sensor='.$val['id'].'" style="color:'.$val['Color'].'" '.$active_class.'>'.$val['Naam'].'</a>';
    }
    ?>
    <a href="tempbig.php?sensor=998" style="color:#fff" <?php echo ($sensor_id==998?'class="nav-active"':''); ?>>Binnen</a>
    <a href="tempbig.php?sensor=999" style="color:#fff" <?php echo ($sensor_id==999?'class="nav-active"':''); ?>>Alles</a>
</div>

<div class="chart-wrapper">
    <canvas id="bigChart"></canvas>
</div>

<div class="time-label"><?php echo date("H:i:s"); ?></div>

<script>
Chart.defaults.animation = false;
new Chart(document.getElementById("bigChart"), {
    type: "line",
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: <?php echo json_encode(array_values($datasets)); ?>
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: { padding: { bottom: 20 } },
        scales: {
            x: {
                ticks: { color: "#555", autoSkip: true, maxTicksLimit: 12, font: { size: 11 } },
                grid: { display: false }
            },
            y: {
                min: <?php echo $min_val; ?>,
                max: <?php echo $max_val; ?>,
                ticks: {
                    color: "#888",
                    font: { size: 14 },
                    callback: v => v + "°"
                },
                grid: { color: "#222" }
            }
        },
        plugins: {
            legend: { display: false },
            tooltip: {
                mode: 'index',
                intersect: false,
                backgroundColor: 'rgba(0,0,0,0.8)',
                titleFont: { size: 14 },
                bodyFont: { size: 14 }
            }
        }
    }
});

setTimeout(() => { window.location.reload(); }, <?php echo (61 - date("s")) * 1000; ?>);
</script>
</body>
</html>
<?php $db->close(); ?>
