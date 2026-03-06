<?php
$logDir = '/temp/ping/';
$files = glob($logDir . 'ping_*.csv');

$analytics = [];
$globalMax = 0; // De variabele om de schaal te bepalen
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

            // Bepaal de hoogste waarde voor de grafiekschaal
            if ($avg > $globalMax) $globalMax = $avg;
        }
        fclose($handle);
    }
}

// Voeg een beetje marge toe aan de globalMax voor de look (10% extra)
$chartScaleMax = $globalMax * 1.1;
if ($chartScaleMax < 20) $chartScaleMax = 20; // Minimum schaal van 20ms
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Router Stress Benchmark - Global Scale</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #0f172a; color: #f1f5f9; margin: 0; padding: 20px; }
        h1 { text-align: center; color: #38bdf8; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 30px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; max-width: 100%; margin: 0 auto; }
        .card { background: #1e293b; border-radius: 12px; padding: 20px; border: 1px solid #334155; display: flex; flex-direction: column; }
        .device-title { font-size: 1.3rem; margin: 0 0 15px 0; color: #f8fafc; border-bottom: 1px solid #334155; padding-bottom: 10px; }
        .stats-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 0.85rem; background: #0f172a; border-radius: 8px; overflow: hidden; }
        .stats-table th { text-align: left; padding: 8px; background: #111827; color: #94a3b8; font-size: 0.7rem; }
        .stats-table td { padding: 8px; border-bottom: 1px solid #334155; }
        .router-name-cell { font-weight: bold; border-left: 4px solid; }
        .stat-val-max { color: #f87171; }
        .stat-val-stab { font-weight: bold; }
        canvas { min-height: 200px; width: 100% !important; }
    </style>
</head>
<body>
    <h1>🛰️ WiFi Stress Benchmark (Global Scale: <?php echo round($chartScaleMax); ?>ms)</h1>

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
                        <td><?php echo $avgLat; ?> ms</td>
                        <td class="stat-val-max"><?php echo $maxLat; ?> ms</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div style="flex-grow: 1;"><canvas id="chart_<?php echo md5($device); ?>"></canvas></div>
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
                        yAxisID: 'y', tension: 0.3, pointRadius: 0, borderWidth: 2
                    },
                    {
                        type: 'bar',
                        label: '<?php echo $rName; ?> Spikes',
                        data: <?php echo json_encode(array_values($rData['spikes'])); ?>,
                        backgroundColor: '<?php echo $routerColors[$rName]['bar'] ?? "rgba(255,255,255,0.1)"; ?>',
                        yAxisID: 'y1', barThickness: 4
                    },
                    <?php endforeach; ?>
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        position: 'left',
                        beginAtZero: true,
                        max: <?php echo $chartScaleMax; ?>, // HIER WORDT DE SCHAAL GEFORCEERD
                        title: { display: true, text: 'ms', color: '#64748b' },
                        grid: { color: '#334155' }
                    },
                    y1: { position: 'right', display: false, beginAtZero: true, max: 50 }
                },
                plugins: { legend: { display: false } }
            }
        });
        </script>
    <?php endforeach; ?>
    </div>
</body>
</html>
