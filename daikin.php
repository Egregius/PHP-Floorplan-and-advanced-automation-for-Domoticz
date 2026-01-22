<?php
// fancy_dashboard_correct.php
$csvFile = '/temp/trend_living.csv';
if (!file_exists($csvFile)) die("CSV file not found");

// Open CSV en detecteer separator
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
$livingTrend   = array_map(fn($d)=>$d['trend']/10+$d['Living target']??0,$data);
$set          = array_map(fn($d)=>$d['set']+1??0,$data);
$setRounded   = array_map(fn($d)=>$d['setrounded']+1??0,$data);
$adjLiving          = array_map(fn($d)=>$d['Living target']+$d['adjLiving']??0,$data);
$daikinpower    = array_map(fn($d)=>$d['daikinpower'],$data);
$daikintargetpower    = array_map(fn($d)=>$d['daikinpower']-100,$data);


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
</style>
</head>
<body>
<canvas id="chart1" height="90"></canvas>
<canvas id="chart2" height="60"></canvas>

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
const livingTarget = <?php echo json_encode($livingTarget); ?>;
const livingTemp   = <?php echo json_encode($livingTemp); ?>;
/*const Trend   = <?php echo json_encode($livingTrend); ?>;*/
const set          = <?php echo json_encode($set); ?>;
const setRounded   = <?php echo json_encode($setRounded); ?>;
const adjLiving    = <?php echo json_encode($adjLiving); ?>;
const daikintargetpower = <?php echo json_encode(array_values($daikinpower)); ?>;
const daikinpower = <?php echo json_encode(array_values($daikinpower)); ?>;
new Chart(document.getElementById('chart1'), {
    type:'line',
    data:{
        labels:labels,
        datasets:[
            {label:'Temperature', data:livingTemp, backgroundColor:'red', borderColor:'red', fill:false, tension:0.2, pointRadius:0,borderWidth:8},
/*            {label:'Trend', data:Trend, backgroundColor:'tomato', borderColor:'tomato', fill:false, borderDash:[3,3], tension:0.2, pointRadius:0,borderWidth:4},*/
            {label:'Set', data:set, backgroundColor:'blue', borderColor:'blue', borderDash:[4,4], fill:false, tension:0.2, pointRadius:0,borderWidth:4},
            {label:'adjLiving', data:adjLiving, backgroundColor:'magenta', borderColor:'magenta', borderDash:[4,4], fill:false, tension:0.2, pointRadius:0,borderWidth:4},
            {label:'Setpoint', data:setRounded, backgroundColor:'green', borderColor:'green', /*borderDash:[1,1], */fill:false, tension:0.2, pointRadius:0,borderWidth:6},
            {label:'Target', data:livingTarget, backgroundColor:'orange', borderColor:'orange', fill:false, tension:0.2, pointRadius:0,borderWidth:8},
        ]
    },
    options:{
    	responsive:true,
    	animation: false,
    	interaction:{
    		mode:'index',
    		intersect:false
    	},
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
					  const min = 350;
					  const max = 1000;
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
        },

    }
});
(() => {
    let lastReloadSecond = null;
    function checkReload() {
        const now = new Date();
        const sec = now.getSeconds();
        if ((sec === 1 || sec === 21 || sec === 42) && sec !== lastReloadSecond) {
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
