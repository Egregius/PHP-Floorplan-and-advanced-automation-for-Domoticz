<?php
$logDir = '/temp/ping/';
$files = glob($logDir . 'ping_*.csv');

$analytics = [];
$allRawRows = []; // Voor de volledige dump
$routerColors = [
    'AXT1800' => ['line' => '#38bdf8', 'bar' => 'rgba(56, 189, 248, 0.4)'],
    'Velop'   => ['line' => '#fbbf24', 'bar' => 'rgba(251, 191, 36, 0.4)'],
    'Flint2'  => ['line' => '#34d399', 'bar' => 'rgba(52, 211, 153, 0.4)']
];

// Stap 1: Data inladen
foreach ($files as $file) {
    $parts = explode('_', basename($file, '.csv'));
    if (count($parts) < 5) continue;
    $name = $parts[1]; $ip = $parts[2]; $router = $parts[4];
    $devKey = "$name ($ip)";

    if (($handle = fopen($file, "r")) !== FALSE) {
        $header = fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== FALSE) {
            $seq = (int)$row[0];
            $analytics[$devKey][$router]['avg'][$seq] = (float)$row[3];
            $analytics[$devKey][$router]['max'][$seq] = (float)$row[4];
            $analytics[$devKey][$router]['spikes'][$seq] = (int)$row[6];
            // Sla de volledige rij op voor de csv export
            $allRawRows[$devKey][$seq][$router] = [
                'min' => $row[2], 'avg' => $row[3], 'max' => $row[4], 'mdev' => $row[5], 'spikes' => $row[6]
            ];
        }
        fclose($handle);
    }
}

// Stap 2: Sync berekenen
$globalMax = 0;
$processedData = [];
$totalSyncSamples = 0;

foreach ($analytics as $device => $routers) {
    $commonSeqs = null;
    foreach ($routers as $rName => $data) {
        $availableSeqs = array_keys($data['avg']);
        if ($commonSeqs === null) { $commonSeqs = $availableSeqs; }
        else { $commonSeqs = array_intersect($commonSeqs, $availableSeqs); }
    }
    if (empty($commonSeqs)) continue;
    $totalSyncSamples = count($commonSeqs);

    foreach ($routers as $rName => $data) {
        $stats = ['avg' => [], 'max' => [], 'spikes' => [], 'sum_spikes' => 0, 'clean_count' => 0];
        foreach ($commonSeqs as $seq) {
            $stats['avg'][$seq] = $data['avg'][$seq];
            $stats['max'][$seq] = $data['max'][$seq];
            $stats['spikes'][$seq] = $data['spikes'][$seq];
            $stats['sum_spikes'] += $data['spikes'][$seq];
            if ($data['spikes'][$seq] == 0) $stats['clean_count']++;
            if ($data['avg'][$seq] > $globalMax) $globalMax = $data['avg'][$seq];
        }
        $processedData[$device][$rName] = [
            'raw' => $stats,
            'stab' => round(($stats['clean_count'] / $totalSyncSamples) * 100, 1),
            'avg_lat' => round(array_sum($stats['avg']) / $totalSyncSamples, 2),
            'max_lat' => max($stats['max']),
            'total_spikes' => $stats['sum_spikes'],
            'seq_labels' => $commonSeqs
        ];
    }
}

$chartScaleMax = ($globalMax > 0) ? $globalMax * 1.1 : 20;
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Router Stress Benchmark</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #0f172a; color: #f1f5f9; font-family: sans-serif; overflow-x: hidden; }
        .viewport-full { height: 100vh; display: flex; flex-direction: column; padding: 5px; overflow: hidden; }
        h1 { text-align: center; color: #38bdf8; font-size: 1rem; margin: 2px 0; position: absolute; width: 100%; pointer-events: none; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; grid-template-rows: repeat(3, 1fr); gap: 8px; flex-grow: 1; height: 100%; }
        .card { background: #1e293b; border-radius: 6px; border: 1px solid #334155; padding: 4px; display: flex; flex-direction: column; min-height: 0; }
        .device-title { font-size: 0.9rem; margin: 0 auto; color: #f8fafc; }
        .stats-table { width: 100%; border-collapse: collapse; font-size: 0.75rem; }
        .stats-table th { padding: 2px; color: #94a3b8; font-size: 0.6rem;text-align:center; }
        td{text-align:center;}
        .router-name-cell { font-weight: bold; border-left: 12px solid; padding-left: 5px; text-align: left; }
        .chart-container { flex-grow: 1; position: relative; min-height: 0; }

        .export-section { background: #020617; padding: 20px; border-top: 2px solid #38bdf8; }
        pre { background: #111827; padding: 15px; border-radius: 5px; font-size: 0.7rem; color: #10b981; overflow-x: auto; border: 1px solid #334155; }
        h3 { color: #38bdf8; margin-bottom: 5px; }
        .raw-export-area {
            background: #020617;
            padding: 20px;
            border-top: 2px solid #38bdf8;
        }
        .raw-box {
            background: #1e293b;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 0.8rem;
            color: #34d399;
            white-space: pre;
            overflow-x: auto;
            border: 1px solid #334155;
        }
    </style>
</head>
<body>

    <div class="viewport-full">
        <h1>Max AVG: <?php echo round($chartScaleMax); ?>ms | Sync: <?php echo $totalSyncSamples; ?> metingen</h1>
        <div class="grid">
        <?php foreach ($processedData as $device => $routers): ?>
            <div class="card">
                <h2 class="device-title"><?php echo $device; ?></h2>
                <table class="stats-table">
                    <thead><tr><th>Router</th><th>Stabiel</th><th>Spikes</th><th>Avg</th><th>Max</th></tr></thead>
                    <tbody>
                        <?php foreach($routers as $rName => $d): ?>
                        <tr>
                            <td class="router-name-cell" style="border-left-color: <?php echo $routerColors[$rName]['line']; ?>"><?php echo $rName; ?></td>
                            <td><b><?php echo $d['stab']; ?>%</b></td>
                            <td><?php echo $d['total_spikes']; ?></td>
                            <td><?php echo $d['avg_lat']; ?>ms</td>
                            <td style="color:#f87171"><?php echo $d['max_lat']; ?>ms</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="chart-container"><canvas id="ch_<?php echo md5($device); ?>"></canvas></div>
            </div>
            <script>
            new Chart(document.getElementById('ch_<?php echo md5($device); ?>'), {
                data: {
                    labels: <?php echo json_encode(array_values(current($routers)['seq_labels'])); ?>,
                    datasets: [
                        <?php foreach ($routers as $rName => $d): ?>
                        { type: 'line', data: <?php echo json_encode(array_values($d['raw']['avg'])); ?>, borderColor: '<?php echo $routerColors[$rName]['line']; ?>', tension: 0.3, pointRadius: 0, borderWidth: 1.5 },
                        { type: 'bar', data: <?php echo json_encode(array_values($d['raw']['spikes'])); ?>, backgroundColor: '<?php echo $routerColors[$rName]['bar']; ?>', yAxisID: 'y1', barThickness: 2 },
                        <?php endforeach; ?>
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, max: <?php echo $chartScaleMax; ?>, ticks: { font: { size: 8 } } }, y1: { display: false, max: 50 }, x: { display: false } }, plugins: { legend: { display: false } } }
            });
            </script>
        <?php endforeach; ?>
        </div>
    </div>
	<div class="raw-export-area">
        <h3 style="margin-top:0;">📋 Raw Data Export</h3>
        <div class="raw-box"><?php
            foreach ($processedData as $device => $routers) {
                echo "DEVICE: $device\n";
                echo str_pad("Router", 15) . " | " . str_pad("Stab", 8) . " | " . str_pad("Avg", 10) . " | " . str_pad("Max", 10) . " | Spikes\n";
                echo str_repeat("-", 60) . "\n";
                foreach ($routers as $rName => $d) {
                    echo str_pad($rName, 15) . " | " .
                         str_pad($d['stab']."%", 8) . " | " .
                         str_pad($d['avg_lat']."ms", 10) . " | " .
                         str_pad($d['max_lat']."ms", 10) . " | " .
                         $d['total_spikes'] . "\n";
                }
                echo "\n";
            }
        ?></div>
    </div>
    <div class="export-section">
        <h3>🚀 Full Side-by-Side CSV (Voor Analyse)</h3>
        <p style="font-size:0.8rem; color:#94a3b8;">Kopieer de onderstaande blokken voor een volledige vergelijking per device.</p>

        <?php foreach ($processedData as $device => $routers):
            $activeRouters = array_keys($routers);
            $header = "seq";
            foreach($activeRouters as $r) { $header .= ";min$r;avg$r;max$r;mdev$r;spikes$r"; }
            ?>
            <h4 style="color:#f1f5f9; margin-bottom:5px;"><?php echo $device; ?></h4>
            <pre><?php
                echo $header . "\n";
                foreach ($routers[array_key_first($routers)]['seq_labels'] as $s) {
                    echo $s;
                    foreach($activeRouters as $r) {
                        $d = $allRawRows[$device][$s][$r];
                        echo ";" . $d['min'] . ";" . $d['avg'] . ";" . $d['max'] . ";" . $d['mdev'] . ";" . $d['spikes'];
                    }
                    echo "\n";
                }
            ?></pre>
        <?php endforeach; ?>
    </div>
</body>
</html>
