<?php
// Bestandspaden (Pas aan indien nodig)
$pathBath = '/var/www/leadDataBath.json';
$pathLiving = '/var/www/leadDataLiving.json';

// Functie om JSON veilig in te lezen, of dummy data te geven als bestand mist
function readJsonFile($path) {
    if (file_exists($path)) {
        return file_get_contents($path);
    }
    // Fallback voor demo/test als bestand niet bestaat
    return '{}';
}

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
        "1": { label: "Warmtepomp (Alleen)", color: "#28a745", borderColor: "#1e7e34" }, // Groen
        "2": { label: "Hybride (WP + Gas)", color: "#fd7e14", borderColor: "#e8590c" },  // Oranje
        "3": { label: "Gasbrander (Alleen)", color: "#dc3545", borderColor: "#b02a37" }   // Rood
    };

    /**
     * Functie om data om te zetten naar Chart.js formaat en Tabel HTML
     */
    function processData(jsonData) {
        let datasets = [];
        let allPoints = []; // Voor de tabel

        // Loop door de methodes (1, 2, 3)
        for (const [methodKey, tempData] of Object.entries(jsonData)) {
            let dataPoints = [];

            // Loop door de temperaturen
            for (const [temp, values] of Object.entries(tempData)) {
                // Bereken gemiddelde als er meerdere waarden zijn (bv [7.2, 8])
                const avg = values.reduce((a, b) => a + b, 0) / values.length;

                dataPoints.push({ x: parseFloat(temp), y: avg });

                // Data opslaan voor de tabel
                allPoints.push({
                    method: methods[methodKey] ? methods[methodKey].label : `Methode ${methodKey}`,
                    temp: parseFloat(temp),
                    avg: avg.toFixed(2),
                    raw: values.join(', ') // Toon ruwe data ook
                });
            }

            // Sorteer op buitentemperatuur (X-as)
            dataPoints.sort((a, b) => a.x - b.x);

            datasets.push({
                label: methods[methodKey] ? methods[methodKey].label : `Methode ${methodKey}`,
                data: dataPoints,
                borderColor: methods[methodKey] ? methods[methodKey].color : "#000",
                backgroundColor: methods[methodKey] ? methods[methodKey].color : "#000",
                tension: 0.3, // Maakt de lijn iets ronder
                pointRadius: 4
            });
        }

        // Sorteer tabel data op temperatuur
        allPoints.sort((a, b) => a.temp - b.temp);

        return { datasets, allPoints };
    }

    /**
     * Genereer HTML Tabel
     */
    function createTable(points, containerId) {
        let html = `
        <table class="table table-striped table-sm text-center">
            <thead>
                <tr>
                    <th>Buiten (°C)</th>
                    <th>Methode</th>
                    <th>Min/°C (Gem)</th>
                    <th>Metingen</th>
                </tr>
            </thead>
            <tbody>`;

        points.forEach(p => {
            html += `<tr>
                <td>${p.temp}</td>
                <td>${p.method}</td>
                <td><strong>${p.avg}</strong></td>
                <td class="text-muted small">${p.raw}</td>
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
        type: 'scatter',
        data: {
            datasets: processedData.datasets.map(ds => ({
                ...ds,
                showLine: true
            }))
        },
        options: {
            responsive: true,
            maintainAspectRatio: true, // <--- VOEG DEZE REGEL TOE
            plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw.y.toFixed(2) + ' min/°C bij ' + context.raw.x + '°C';
                            }
                        }
                    },
                    legend: { position: 'bottom' }
                },
                scales: {
                    x: {
                        type: 'linear',
                        position: 'bottom',
                        title: { display: true, text: 'Buitentemperatuur (°C)' }
                    },
                    y: {
                        title: { display: true, text: 'Minuten per °C opwarming' },
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // --- Uitvoeren ---

    // 1. Verwerk data
    const bathData = processData(rawDataBath);
    const livingData = processData(rawDataLiving);

    // 2. Render Grafieken
    initChart('chartBath', bathData);
    initChart('chartLiving', livingData);

    // 3. Render Tabellen
    createTable(bathData.allPoints, 'tableBathContainer');
    createTable(livingData.allPoints, 'tableLivingContainer');

</script>

</body>
</html>
