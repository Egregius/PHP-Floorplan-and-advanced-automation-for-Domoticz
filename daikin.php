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
$set          = array_map(fn($d)=>$d['set']??0,$data);
$setRounded   = array_map(fn($d)=>$d['setrounded']??0,$data);
$adj          = array_map(fn($d)=>$d['adj']/10??0,$data);
$adjLiving          = array_map(fn($d)=>$d['adjLiving']/10??0,$data);
$daikinpower    = array_map(fn($d)=>$d['daikinpower']/1000,$data);


?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<title>Living Trend</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body {font-family:sans-serif; background:#f5f5f5; margin:20px;}
canvas{background:#fff;border:1px solid #ccc;margin-bottom:40px;}
h1,h2{margin-top:30px;}
.info-box{background:#fff;border:1px solid #999;padding:15px;margin-bottom:30px;}
</style>
</head>
<body>
<canvas id="chart1" height="100"></canvas>
<canvas id="chart2" height="100"></canvas>

<script>
const labels = <?php echo json_encode($labels); ?>;
const livingTarget = <?php echo json_encode($livingTarget); ?>;
const livingTemp   = <?php echo json_encode($livingTemp); ?>;
const set          = <?php echo json_encode($set); ?>;
const setRounded   = <?php echo json_encode($setRounded); ?>;
const adj          = <?php echo json_encode($adj); ?>;
const adjLiving    = <?php echo json_encode($adjLiving); ?>;
const daikinpower = <?php echo json_encode(array_values($daikinpower)); ?>;
new Chart(document.getElementById('chart1'), {
    type:'line',
    data:{
        labels:labels,
        datasets:[
            {label:'Target', data:livingTarget, backgroundColor:'yellow', borderColor:'yellow', fill:false, tension:0.1, pointRadius:0},
            {label:'Termperature', data:livingTemp, backgroundColor:'red', borderColor:'red', fill:false, tension:0.1, pointRadius:0},
            {label:'Set', data:set, backgroundColor:'blue', borderColor:'blue', borderDash:[2,5], fill:false, tension:0.1, pointRadius:0},
            {label:'Setpoint', data:setRounded, backgroundColor:'green', borderColor:'green', /*borderDash:[1,1], */fill:false, tension:0.1, pointRadius:0},
        ]
    },
    options:{responsive:true, interaction:{mode:'index', intersect:false}}
});
new Chart(document.getElementById('chart2'), {
    type:'line',
    data:{
        labels:labels,
        datasets:[
            {label:'Adj', data:adj, backgroundColor:'orange', borderColor:'orange', /*borderDash:[2,5], */fill:false, tension:0.2, pointRadius:0},
            {label:'AdjLiving', data:adjLiving, backgroundColor:'red', borderColor:'red', /*borderDash:[2,5], */fill:false, tension:0.2, pointRadius:0},
            {label:'Power', data:daikinpower, backgroundColor:'black', borderColor:'black', /*borderDash:[2,5], */fill:false, tension:0.1, pointRadius:0},
        ]
    },
    options:{responsive:true, interaction:{mode:'index', intersect:false}}
});
(() => {
    let lastReloadSecond = null;
    function checkReload() {
        const now = new Date();
        const sec = now.getSeconds();
        if ((sec === 1 || sec === 31) && sec !== lastReloadSecond) {
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
