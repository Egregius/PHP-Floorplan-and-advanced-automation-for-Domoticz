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
	1=>"Airco",
	2=>"Airco + Gas",
	3=>"Gas"
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
    // Data injecteren vanuit PHP
    const rawDataBath = <?php echo $jsonBath; ?>;
    const rawDataLiving = <?php echo $jsonLiving; ?>;

    // Mapping voor de verwarmingsmethodes
    const methods = {
        "1": { label: "Airco", color: "#28a745" },       // Groen
        "2": { label: "Airco + gas", color: "#fd7e14" }, // Oranje
        "3": { label: "Gas", color: "#dc3545" }          // Rood
    };

    /**
     * Verwerk data naar 3 datasets per methode:
     * 1. Trend (Smoothed Line)
     * 2. Average (Dashed Line)
     * 3. Raw (Scatter Points)
     */
    function processData(jsonData) {
        let datasets = [];
        let allPoints = []; // Voor de tabel

        // Loop door de methodes (1, 2, 3)
        for (const [methodKey, tempData] of Object.entries(jsonData)) {
            let individualPoints = []; // Alle losse metingen
            let avgPoints = [];        // Exacte gemiddelden
            let smoothPoints = [];     // De berekende trendlijn (buren)

            // 1. Sorteer temperaturen
            const sortedTemps = Object.keys(tempData).map(Number).sort((a, b) => a - b);
            const methodInfo = methods[methodKey] || { label: `Mode ${methodKey}`, color: "#333333" };

            // Loop door de gesorteerde temperaturen
            for (let i = 0; i < sortedTemps.length; i++) {
                const temp = sortedTemps[i];
                const tempKey = String(temp);
                const values = tempData[tempKey];

                // A. Alle losse punten (Scatter)
                values.forEach(val => {
                    individualPoints.push({ x: temp, y: val });
                });

                // B. Exact gemiddelde (Line)
                const avg = values.reduce((a, b) => a + b, 0) / values.length;
                avgPoints.push({ x: temp, y: avg });

                // C. Gemiddelde met buren (Trend Line)
                let combinedValues = [...values];
                if (i > 0) combinedValues = combinedValues.concat(tempData[String(sortedTemps[i - 1])]);
                if (i < sortedTemps.length - 1) combinedValues = combinedValues.concat(tempData[String(sortedTemps[i + 1])]);

                const smoothedAvg = combinedValues.reduce((a, b) => a + b, 0) / combinedValues.length;
                smoothPoints.push({ x: temp, y: smoothedAvg });

                // D. Data voor tabel
                allPoints.push({
                    method: methodInfo.label,
                    temp: temp,
                    avg: avg.toFixed(2),
                    smoothed: smoothedAvg.toFixed(2),
                    raw: values.join(', ')
                });
            }

            // --- Dataset 1: De Trendlijn (Smoothed) ---
            datasets.push({
                type: 'line',
                label: methodInfo.label + " (+buren)",
                data: smoothPoints,
                borderColor: methodInfo.color,
                backgroundColor: methodInfo.color,
                borderWidth: 8,             // Dikke lijn
                tension: 0.4,               // Vloeiend
                pointRadius: 8,             // Geen punten op de lijn
                order: 1
            });

            // --- Dataset 2: Het Exacte Gemiddelde (Average) ---
            datasets.push({
                type: 'line',
                label: methodInfo.label + " (avg)",
                data: avgPoints,
                borderColor: methodInfo.color,
                backgroundColor: methodInfo.color,
                borderWidth: 2,             // Iets dunner
                borderDash: [4,4],         // Stippellijn
                tension: 0.2,                 // Rechte lijnen (minder vloeiend)
                pointRadius: 6,             // Kleine puntjes op het gemiddelde
                pointStyle: 'rectRot',      // Ruitjesvorm om te onderscheiden
                fill: false,
                order: 2
            });

            // --- Dataset 3: De Individuele Punten (Scatter) ---
            datasets.push({
                type: 'scatter',
                label: methodInfo.label + " (Metingen)",
                data: individualPoints,
                borderColor: methodInfo.color,
                backgroundColor: methodInfo.color + '50', // Transparant
                pointRadius: 6,
                order: 3
            });
        }

        // Sorteer tabel data op temperatuur
        allPoints.sort((a, b) => a.temp - b.temp);

        return { datasets, allPoints };
    }

    /**
     * Genereer HTML Tabel (Ongewijzigd)
     */
    function createTable(points, containerId) {
        let html = `
        <table class="table table-striped table-sm text-center align-middle">
            <thead>
                <tr>
                    <th>Buiten (°C)</th>
                    <th>Methode</th>
                    <th>Min/°C<br><small>(+buren)</small></th>
                    <th>Min/°C<br><small>(avg)</small></th>
                    <th>Metingen</th>
                </tr>
            </thead>
            <tbody>`;

        points.forEach(p => {
            html += `<tr>
                <td>${p.temp}</td>
                <td class="text-nowrap">${p.method}</td>
                <td class="text-dark fw-bold">${p.smoothed}</td>
                <td class="text-muted">${p.avg}</td>
                <td class="text-muted small text-break" style="font-size: 0.75em;">${p.raw}</td>
            </tr>`;
        });

        html += `</tbody></table>`;
        document.getElementById(containerId).innerHTML = html;
    }

    /**
     * Initialiseer Grafiek
     */
    function initChart(canvasId, processedData) {
        new Chart(document.getElementById(canvasId), {
            data: {
                datasets: processedData.datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                let val = context.parsed.y;
                                if(val !== null) val = val.toFixed(2);
                                return `${label}: ${val} min/°C`;
                            }
                        }
                    },
                    legend: {
                    	display: false,
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 15 }
                    }
                },
                scales: {
                    x: {
                        type: 'linear',
                        position: 'bottom',
                        title: { display: false, text: 'Buitentemperatuur (°C)' }
                    },
                    y: {
                        title: { display: true, text: 'Minuten per °C opwarming' },
                        beginAtZero: false
                    }
                }
            }
        });
    }

    // --- Uitvoeren ---
    const bathData = processData(rawDataBath);
    const livingData = processData(rawDataLiving);

    initChart('chartBath', bathData);
    initChart('chartLiving', livingData);

    createTable(bathData.allPoints, 'tableBathContainer');
    createTable(livingData.allPoints, 'tableLivingContainer');

</script>
</body>
</html>
