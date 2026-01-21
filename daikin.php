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
$adj          = array_map(fn($d)=>$d['adj']??0,$data);
$adjLiving          = array_map(fn($d)=>$d['adjLiving']??0,$data);
$daikinpower    = array_map(fn($d)=>$d['daikinpower']/1000,$data);

// Analyse overshoot: Living temp tov gewenste target
$overshootTarget = [];
$overshootSet    = [];
$avgAdj = [];
$oscillations=0;
for($i=0;$i<count($data);$i++){
    $diffTarget = $livingTemp[$i]-$livingTarget[$i];
    $diffSet    = $livingTemp[$i]-$set[$i];
    $overshootTarget[] = $diffTarget;
    $overshootSet[] = $diffSet;
    $avgAdj[] = abs($adj[$i]);
}

// Statistieken
$absOvershootTarget = array_map('abs',$overshootTarget);
$absOvershootSet = array_map('abs',$overshootSet);

$maxOvershootTarget = count($absOvershootTarget)?max($absOvershootTarget):0;
$avgOvershootTarget = count($absOvershootTarget)?array_sum($absOvershootTarget)/count($absOvershootTarget):0;

$maxOvershootSet = count($absOvershootSet)?max($absOvershootSet):0;
$avgOvershootSet = count($absOvershootSet)?array_sum($absOvershootSet)/count($absOvershootSet):0;

$avgAdjVal    = count($avgAdj)?array_sum($avgAdj)/count($avgAdj):0;

// Overshoot statistiek
$overshootGT05Target = count(array_filter($overshootTarget, fn($v)=>abs($v)>0.5));
$overshootGT1Target  = count(array_filter($overshootTarget, fn($v)=>abs($v)>1));

$percentOvershootGT05Target = count($overshootTarget)?round($overshootGT05Target/count($overshootTarget)*100,1):0;
$percentOvershootGT1Target  = count($overshootTarget)?round($overshootGT1Target/count($overshootTarget)*100,1):0;

// Aanbevelingen op basis van data
$recommended = [];
// k_factor
if($maxOvershootTarget>2.5) $recommended['k_factor']=0.5;
elseif($maxOvershootTarget>1.5) $recommended['k_factor']=0.8;
else $recommended['k_factor']=1.0;
// trend_factor
$recommended['trend_factor'] = ($avgAdjVal>0.5)?3.0:2.0;
// stepPerLoop
$recommended['stepPerLoop'] = ($avgAdjVal<0.05)?0.15:0.1;

// Extra slimme adviezen
$smartAdvice = [];
if($percentOvershootGT1Target>10) $smartAdvice[]="Verminder k_factor om overshoot >1°C tov target te beperken";
if($avgAdjVal<0.05) $smartAdvice[]="Verhoog stepPerLoop voor snellere reactie";
if($oscillations>5) $smartAdvice[]="Pas trend_factor aan om oscillaties in accumAdjLiving te verminderen";

// Markers voor grafieken
$overshootMarkers = array_map(fn($v,$i)=>abs($v)>0.5?['x'=>$i,'y'=>$v]:null,$overshootTarget,array_keys($overshootTarget));
$overshootMarkers = array_filter($overshootMarkers);

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
<div class="info-box">
<h2>Parameter Aanbevelingen</h2>
<p><b>k_factor:</b> <?php echo $recommended['k_factor']; ?></p>
<p><b>trend_factor:</b> <?php echo $recommended['trend_factor']; ?></p>
<p><b>stepPerLoop:</b> <?php echo $recommended['stepPerLoop']; ?></p>
<p><b>Max overshoot tov target:</b> <?php echo round($maxOvershootTarget,2); ?>°C</p>
<p><b>Gemiddelde overshoot tov target:</b> <?php echo round($avgOvershootTarget,3); ?>°C</p>
<p><b>Max overshoot tov set:</b> <?php echo round($maxOvershootSet,2); ?>°C</p>
<p><b>Gemiddelde overshoot tov set:</b> <?php echo round($avgOvershootSet,3); ?>°C</p>
<p><b>Overshoot >0.5°C:</b> <?php echo $percentOvershootGT05Target; ?>%</p>
<p><b>Overshoot >1°C:</b> <?php echo $percentOvershootGT1Target; ?>%</p>
<p><b>Gemiddelde adj per loop:</b> <?php echo round($avgAdjVal,3); ?></p>
<h3>Extra aanbevelingen:</h3>
<ul>
<?php foreach($smartAdvice as $a) echo "<li>$a</li>"; ?>
</ul>
</div>

<canvas id="chart1" height="100"></canvas>
<canvas id="chart2" height="100"></canvas>

<script>
const labels = <?php echo json_encode($labels); ?>;
const livingTarget = <?php echo json_encode($livingTarget); ?>;
const livingTemp   = <?php echo json_encode($livingTemp); ?>;
const set          = <?php echo json_encode($set); ?>;
const setRounded   = <?php echo json_encode($setRounded); ?>;
const adj          = <?php echo json_encode($adj); ?>;
const adjLiving          = <?php echo json_encode($adjLiving); ?>;
const overshootMarkers = <?php echo json_encode(array_values($overshootMarkers)); ?>;
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
</script>
</body>
</html>
