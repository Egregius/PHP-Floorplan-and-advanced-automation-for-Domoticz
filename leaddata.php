<?php
$pathBath = '/var/www/leadDataBath.json';
$pathLiving = '/var/www/leadDataLiving.json';
function readJsonFile($path) {
    if (file_exists($path)) {
        return file_get_contents($path);
    }
    return '{}';
}
$modes=[
	1=>"Warmtepomp",
	2=>"Hybride (WP + Gas)",
	3=>"Gasbrander"
];
$jsonBath = readJsonFile($pathBath);
$jsonLiving = readJsonFile($pathLiving);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/styles/leaddata.css?v=<?=filemtime('styles/floorplan.css.gz')?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container-fluid">
    <div class="row g-5">
    	<div class="col-12 col-xl-6">
            <div class="card h-100">
            	<div class="card-header bg-primary text-white">
                    <h5>🚿 Badkamer</h5>
                </div>
                <div class="card-body">
                    <div class="chart-wrapper">
                        <canvas id="chartBath"></canvas>
                    </div>
                    <hr>
                    <div class="table-scroll" id="tableBathContainer"></div>
                    <div>
<?php
$leadDataBath=json_decode($jsonBath,true);
foreach($leadDataBath as $mode=>$temp) {
	$points=array_map("count", $temp);
	echo $modes[$mode].' '.array_sum($points).' metingen over '.count($points).' temperaturen.<br>';
}
?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5>🛋️ Woonkamer</h5>
                </div>
                <div class="card-body">
                    <div class="chart-wrapper">
                        <canvas id="chartLiving"></canvas>
                    </div>
                    <hr>
                    <div class="table-scroll" id="tableLivingContainer"></div>
                     <div>
<?php
$leadDataLiving=json_decode($jsonLiving,true);
foreach($leadDataLiving as $mode=>$temp) {
	$points=array_map("count", $temp);
	echo $modes[$mode].' '.array_sum($points).' metingen over '.count($points).' temperaturen.<br>';
}
?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const rawDataBath = <?php echo $jsonBath; ?>;
    const rawDataLiving = <?php echo $jsonLiving; ?>;
    const methods = {
        "1": { label: "Warmtepomp", color: "#28a745" },
        "2": { label: "Hybride (WP + Gas)", color: "#fd7e14" },
        "3": { label: "Gasbrander", color: "#dc3545" }
    };
    function processData(jsonData) {
        let datasets = [];
        let allPoints = [];
        for (const [methodKey, tempData] of Object.entries(jsonData)) {
            let rawPoints = [];
            let smoothPoints = [];
            const sortedTemps = Object.keys(tempData).map(Number).sort((a, b) => a - b);
            const methodInfo = methods[methodKey] || { label: `Methode ${methodKey}`, color: "#333" };
            for (let i = 0; i < sortedTemps.length; i++) {
                const temp = sortedTemps[i];
                const tempKey = String(temp);
                const values = tempData[tempKey];
                const avg = values.reduce((a, b) => a + b, 0) / values.length;
                rawPoints.push({ x: temp, y: avg });
                let combinedValues = [...values];
                if (i > 0) {
                    combinedValues = combinedValues.concat(tempData[String(sortedTemps[i - 1])]);
                }
                if (i < sortedTemps.length - 1) {
                    combinedValues = combinedValues.concat(tempData[String(sortedTemps[i + 1])]);
                }
                const smoothedAvg = combinedValues.reduce((a, b) => a + b, 0) / combinedValues.length;
                smoothPoints.push({ x: temp, y: smoothedAvg });
                allPoints.push({
                    method: methodInfo.label,
                    temp: temp,
                    avg: avg.toFixed(2),
                    smoothed: smoothedAvg.toFixed(2),
                    raw: values.join(', ')
                });
            }
            datasets.push({
                label: methodInfo.label + " (+ buren)",
                data: smoothPoints,
                borderColor: methodInfo.color,
                backgroundColor: methodInfo.color,
                borderWidth: 5,
                tension: 0.4,
                pointRadius: 10,
                pointHoverRadius: 15,
                order: 1
            });
            datasets.push({
                label: methodInfo.label,
                data: rawPoints,
                borderColor: methodInfo.color,
                backgroundColor: methodInfo.color,
                borderWidth: 2,
                borderDash: [5, 5],
                pointRadius: 2,
                pointHoverRadius: 5,
                pointStyle: 'circle',
                tension: 0,
                order: 2,
                hidden: false
            });
        }
        allPoints.sort((a, b) => a.temp - b.temp);
        return { datasets, allPoints };
    }
    function createTable(points, containerId) {
        let html = `
        <table class="table table-striped table-sm text-center align-middle">
            <thead>
                <tr>
                    <th>Buiten (°C)</th>
                    <th>Methode</th>
                    <th>Min/°C<br><small>(Gem)</small></th>
                    <th>Min/°C<br><small>(Trend)</small></th>
                    <th>Metingen</th>
                </tr>
            </thead>
            <tbody>`;

        points.forEach(p => {
            html += `<tr>
                <td>${p.temp}</td>
                <td class="text-nowrap">${p.method}</td>
                <td class="text-muted">${p.avg}</td>
                <td class="fw-bold text-dark">${p.smoothed}</td>
                <td class="text-muted small text-break" style="font-size: 0.85em;">${p.raw}</td>
            </tr>`;
        });

        html += `</tbody></table>`;
        document.getElementById(containerId).innerHTML = html;
    }
    function initChart(canvasId, processedData) {
        new Chart(document.getElementById(canvasId), {
            type: 'scatter',
            data: {
                datasets: processedData.datasets.map(ds => ({
                    ...ds,
                    showLine: true
                }))
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y.toFixed(2) + ' min/°C';
                                }
                                return label;
                            }
                        }
                    },
                    legend: {
						display: false
					}
                },
                scales: {
                    x: {
                        type: 'linear',
                        position: 'bottom',
                        title: { display: true, text: 'Buitentemperatuur (°C)' }
                    },
                    y: {
                        title: { display: true, text: 'Minuten per °C opwarming' },
                        beginAtZero: false
                    }
                }
            }
        });
    }
    const bathData = processData(rawDataBath);
    const livingData = processData(rawDataLiving);
    initChart('chartBath', bathData);
    initChart('chartLiving', livingData);
    createTable(bathData.allPoints, 'tableBathContainer');
    createTable(livingData.allPoints, 'tableLivingContainer');
</script>
</body>
</html>
