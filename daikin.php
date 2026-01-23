<?php
$logDir = '/var/www/csv';
$pattern = $logDir . '/trend_living-*.csv';

$files = glob($pattern);
if (!$files) {
    die('Geen trend_living bestanden gevonden');
}

usort($files, function ($a, $b) {
    return strcmp($a, $b);
});

if (!empty($_GET['datum'])) {
    $selectedDate = $_GET['datum'];
    $csvFile = $logDir . '/trend_living-' . $selectedDate . '.csv';

    if (!is_readable($csvFile)) {
        die('Geselecteerd bestand bestaat niet');
    }
} else {
    /* laatste bestand nemen */
    $csvFile = end($files);

    /* datum extraheren voor formulier */
    if (preg_match('/trend_living-(\d{4}-\d{2}-\d{2})\.csv$/', $csvFile, $m)) {
        $selectedDate = $m[1];
    }
}


$fh = fopen($csvFile,'r');
$firstLine = fgets($fh);
$separator = (strpos($firstLine,';')!==false)?';':"\t";
rewind($fh);

// Headers lezen
$headers = fgetcsv($fh,0,$separator);
if (!$headers) die("CSV headers niet gevonden");

// CSV data inlezen
$data = [];
while(($row=fgetcsv($fh,0,$separator))!==false){
    $entry = [];
    foreach($headers as $i=>$h){
        $val = $row[$i] ?? null;
        if (is_string($val)) $val = str_replace(',', '.', $val);
        $entry[$h] = is_numeric($val)?(float)$val:$val;
    }
    $data[] = $entry;
}
fclose($fh);

// Basis arrays
$labels = array_map(fn($d)=>$d['timestamp']??'', $data);
$livingTarget = array_map(fn($d)=>$d['Living target']??0,$data);
$livingTemp   = array_map(fn($d)=>$d['Living temp']??0,$data);
$set          = array_map(fn($d)=>$d['set']??0,$data);
$setRounded   = array_map(fn($d)=>$d['setrounded']??0,$data);
$daikinpower    = array_map(fn($d)=>$d['daikinpower'],$data);


?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<title>Living Trend</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body {font-family:sans-serif; background:#FFF; margin:20px;color:#000;}
canvas{background:#FFF;border:0px solid #000;margin-bottom:40px;}
th{text-align:right;padding-right:8px;}
td{text-align:right;}
</style>
</head>
<body>

<form method="get">
    <label for="datum">Datum:</label>
    <select name="datum" id="datum" onchange="this.form.submit()">
        <?php
        foreach ($files as $file) {
            if (preg_match('/trend_living-(\d{4}-\d{2}-\d{2})\.csv$/', $file, $m)) {
                $date = $m[1];
                $sel  = ($date === $selectedDate) ? 'selected' : '';
                echo "<option value=\"$date\" $sel>$date</option>";
            }
        }
        ?>
    </select> 	&nbsp; 	<a href="/daikin.php">Vandaag</a>
</form>
<canvas id="chart1" height="90"></canvas>
<canvas id="chart2" height="60"></canvas>
<div class="stats">
	<table>
		<tbody>
			<tr><th>Target</th><td><?= number_format((array_sum($livingTarget)/count($livingTarget)),2,',','');?> °C</td></tr>
			<tr><th>Temperatuur</th><td><?= number_format((array_sum($livingTemp)/count($livingTemp)),2,',','');?> °C</td></tr>
			<tr><th>Max temperatuur</th><td><?= number_format(max($livingTemp),1,',','');?> °C</td></tr>
			<tr><th>Min temperatuur</th><td><?= number_format(min($livingTemp),1,',','');?> °C</td></tr>
			<tr><th>Dif temperatuur</th><td><?= number_format(max($livingTemp)-min($livingTemp),1,',','');?> °C</td></tr>
			<tr><th>Set</th><td><?= number_format((array_sum($set)/count($set)),2,',','');?> °C</td></tr>
			<tr><th>SetRouned</th><td><?= number_format((array_sum($setRounded)/count($setRounded)),2,',','');?> °C</td></tr>
			<tr><th>Vermogen</th><td><?= number_format((array_sum($daikinpower)/count($daikinpower)),0,',','');?> W</td></tr>
			<tr><th>Tijd aan</th><td><?= number_format(count(array_filter($daikinpower, fn($v) => $v > 50))/3,1,',','');?> min</td></tr>
			<tr><th>Tijd uit</th><td><?= number_format(count(array_filter($daikinpower, fn($v) => $v < 50))/3,1,',','');?> min</td></tr>
		</tbody>
	</table>

</div>
<script>
function clamp(v, min, max) {
  return Math.min(max, Math.max(min, v));
}
function lerp(a, b, t) {
  return a + (b - a) * t;
}
function colorLerp(c1, c2, t) {
  return [
    Math.round(lerp(c1[0], c2[0], t)),
    Math.round(lerp(c1[1], c2[1], t)),
    Math.round(lerp(c1[2], c2[2], t))
  ];
}
const labels = <?php echo json_encode($labels); ?>;
const livingTemp   = <?php echo json_encode($livingTemp); ?>;
const set          = <?php echo json_encode($set); ?>;
const setRounded   = <?php echo json_encode($setRounded); ?>;
const livingTarget = <?php echo json_encode($livingTarget); ?>;
const daikinpower = <?php echo json_encode(array_values($daikinpower)); ?>;
new Chart(document.getElementById('chart1'), {
    type:'line',
    data:{
        labels:labels,
        datasets:[
            {label:'Temperature', data:livingTemp, backgroundColor:'red', borderColor:'red', fill:false, tension:0.2, pointRadius:0,borderWidth:8},
            {label:'Set', data:set, backgroundColor:'orange', borderColor:'orange', borderDash:[4,4], fill:false, tension:0.2, pointRadius:0,borderWidth:4},
            {label:'Setpoint', data:setRounded, backgroundColor:'orange', borderColor:'orange', /*borderDash:[1,1], */fill:false, tension:0.2, pointRadius:0,borderWidth:6},
            {label:'Target', data:livingTarget, backgroundColor:'green', borderColor:'green', fill:false, tension:0.2, pointRadius:0,borderWidth:8},
        ]
    },
    options:{
        responsive: true,
        animation: false,
        interaction: {
            mode: 'index',
            intersect: false
        },
        plugins: {
            legend: {
                display: false
            }
        },
		scales: {
			y: {
				min: 17,
				max: 21
			}
		}

    }
});
const ctx = document.getElementById('chart2').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [
             {
                label: 'Power',
                data: daikinpower,
                borderColor: 'red',
                borderWidth: 0,
                fill: true,
                pointRadius: 0,
                tension: 0.2,
                segment: {
					backgroundColor: ctx => {
					  const v = ctx.p0.parsed.y;
					  if (v == null) return 'rgba(0,0,0,0)';
					  if (v < 50) return 'rgba(0,0,0,0.4)';
					  const min = 400;
					  const max = 900;
					  const t = clamp((v - min) / (max - min), 0, 1);
					  const tc = Math.pow(t, 0.6);
					  const GREEN  = [0, 180, 0];
					  const YELLOW = [255, 190, 0];
					  const RED    = [200, 0, 0];
					  let rgb;
					  if (tc < 0.5) {
						rgb = colorLerp(GREEN, YELLOW, tc / 0.5);
					  } else {
						rgb = colorLerp(YELLOW, RED, (tc - 0.5) / 0.5);
					  }
					  const alpha = lerp(0.45, 0.95, tc);
					  return `rgba(${rgb[0]},${rgb[1]},${rgb[2]},${alpha})`;
					},
					borderWidth: 0,
					borderColor: 'red'
				  }
            }
        ]
    },
    options: {
        responsive: true,
        animation: false,
        interaction: {
            mode: 'index',
            intersect: false
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
(() => {
    let lastReloadSecond = null;
    function checkReload() {
        const now = new Date();
        const sec = now.getSeconds();
        if ((sec === 2 || sec === 22 || sec === 42) && sec !== lastReloadSecond) {
            lastReloadSecond = sec;
            location.reload();
        }
        if (sec !== lastReloadSecond) {
            lastReloadSecond = null;
        }
    }
    setInterval(checkReload, 1000);
})();
</script>
</body>
</html>
