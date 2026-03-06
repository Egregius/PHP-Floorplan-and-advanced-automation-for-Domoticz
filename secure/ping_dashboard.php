<?php
$logDir = '/temp/ping/';
$files = glob($logDir . 'ping_*.csv');

$analytics = [];
$globalMax = 0;
$routerColors = [
    'AXT1800' => ['line' => '#38bdf8', 'bar' => 'rgba(56, 189, 248, 0.4)'],
    'Velop'   => ['line' => '#fbbf24', 'bar' => 'rgba(251, 191, 36, 0.4)'],
    'Flint2'  => ['line' => '#34d399', 'bar' => 'rgba(52, 211, 153, 0.4)']
];

foreach ($files as $file) {
    $parts = explode('_', basename($file, '.csv'));
    if (count($parts) < 5) continue;
    $name = $parts[1]; $ip = $parts[2]; $router = $parts[4];
    $devKey = "$name ($ip)";

    if (($handle = fopen($file, "r")) !== FALSE) {
        fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== FALSE) {
            $seq = (int)$row[0];
            $avg = (float)$row[3];
            $max = (float)$row[4];
            $spikes = (int)$row[6];

            $analytics[$devKey][$router]['avg'][$seq] = $avg;
            $analytics[$devKey][$router]['max'][$seq] = $max;
            $analytics[$devKey][$router]['spikes'][$seq] = $spikes;

            if ($avg > $globalMax) $globalMax = $avg;
        }
        fclose($handle);
    }
}

$chartScaleMax = $globalMax * 1.1;
if ($chartScaleMax < 20) $chartScaleMax = 20;
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Router Stress Benchmark</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #0f172a;
            color: #f1f5f9;
            margin: 0;
            padding: 5px;
            height: 100vh;
            overflow: hidden; /* Geen scrollbar */
            display: flex;
            flex-direction: column;
        }

        h1 {
            text-align: center;
            color: #38bdf8;
            font-size: 1rem;
            margin: 2px 0;
            position: absolute;
		    width: 100%;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: repeat(3, 1fr); /* 3 gelijke rijen */
            gap: 8px;
            flex-grow: 1; /* Vult resterende hoogte body */
            height: 100%;
        }

        .card {
            background: #1e293b;
            border-radius: 6px;
            border: 1px solid #334155;
            padding: 4px;
            display: flex;
            flex-direction: column;
            min-height: 0; /* Belangrijk voor flexbox in grid */
        }

        .device-title {
            font-size: 1rem;
            margin: 0 auto;
            color: #f8fafc;
            line-height: 1.2;
        }

        .stats-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.75rem;
            margin-bottom: 2px;
        }

        .stats-table th {
            padding: 2px 4px;
            color: #94a3b8;
            font-size: 0.65rem;
            text-align: center;
        }

        .stats-table td {
            padding: 2px 4px;
            border-bottom: 1px solid #334155;
            text-align: center;
        }

        .router-name-cell {
            font-weight: bold;
            border-left: 12px solid;
            border-radius: 4px;
            text-align: left !important;
            padding-left: 8px !important;
        }

        .stat-val-max { color: #f87171; }
        .stat-val-stab { font-weight: bold; }

        .chart-container {
            flex-grow: 1;
            position: relative;
            min-height: 0;
        }

        canvas { width: 100% !important; height: 100% !important; }
    </style>
</head>
<body>
    <h1>Max AVG: <?php echo round($chartScaleMax); ?>ms</h1>

    <div class="grid">
    <?php foreach ($analytics as $device => $routers): ?>
        <div class="card">
            <h2 class="device-title"><?php echo $device; ?></h2>
            <table class="stats-table">
                <thead>
                    <tr><th>Router</th><th>Stabiel</th><th>Spikes</th><th>Avg</th><th>Max</th></tr>
                </thead>
                <tbody>
                    <?php foreach($routers as $rName => $rData):
                        $total = count($rData['spikes']);
                        if ($total === 0) continue;
                        $clean = count(array_filter($rData['spikes'], function($s) { return $s == 0; }));
                        $stab = round(($clean / $total) * 100, 1);
                        $avgLat = round(array_sum($rData['avg']) / $total, 2);
                        $maxLat = max($rData['max']);
                        $sumSpikes = array_sum($rData['spikes']);
                        $color = $routerColors[$rName]['line'] ?? '#ffffff';
                    ?>
                    <tr>
                        <td class="router-name-cell" style="border-left-color: <?php echo $color; ?>; color: <?php echo $color; ?>"><?php echo $rName; ?></td>
                        <td class="stat-val-stab <?php echo ($stab < 95) ? 'stat-val-max' : ''; ?>"><?php echo $stab; ?>%</td>
                        <td><?php echo $sumSpikes; ?></td>
                        <td><?php echo $avgLat; ?>ms</td>
                        <td class="stat-val-max"><?php echo $maxLat; ?>ms</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="chart-container">
                <canvas id="chart_<?php echo md5($device); ?>"></canvas>
            </div>
        </div>

        <script>
        new Chart(document.getElementById('chart_<?php echo md5($device); ?>'), {
            data: {
                labels: <?php echo json_encode(range(1, 150)); ?>,
                datasets: [
                    <?php foreach ($routers as $rName => $rData): ?>
                    {
                        type: 'line',
                        label: '<?php echo $rName; ?> Avg',
                        data: <?php echo json_encode(array_values($rData['avg'])); ?>,
                        borderColor: '<?php echo $routerColors[$rName]['line'] ?? "#fff"; ?>',
                        yAxisID: 'y', tension: 0.3, pointRadius: 0, borderWidth: 1.5
                    },
                    {
                        type: 'bar',
                        label: '<?php echo $rName; ?> Spikes',
                        data: <?php echo json_encode(array_values($rData['spikes'])); ?>,
                        backgroundColor: '<?php echo $routerColors[$rName]['bar'] ?? "rgba(255,255,255,0.1)"; ?>',
                        yAxisID: 'y1', barThickness: 2
                    },
                    <?php endforeach; ?>
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: <?php echo $chartScaleMax; ?>,
                        ticks: { font: { size: 9 }, color: '#64748b', maxTicksLimit: 10 },
                        grid: { color: '#334155' }
                    },
                    y1: { position: 'right', display: false, beginAtZero: true, max: 50 },
                    x: { ticks: { display: false }, grid: { display: false } }
                },
                plugins: { legend: { display: false } }
            }
        });
        </script>
    <?php endforeach; ?>
    </div>
</body>
</html>
