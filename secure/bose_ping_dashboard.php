<?php
$logDir = '/temp/';
$files = glob($logDir . 'ping_*.csv');

$data = [];
foreach ($files as $file) {
    // Parse bestandsnaam: ping_living_192.168.2.101_20260306_08.csv
    $parts = explode('_', basename($file, '.csv'));
    $name = $parts[1];
    $ip = $parts[2];
    $hour = $parts[4];

    if (($handle = fopen($file, "r")) !== FALSE) {
        fgetcsv($handle); // Skip header
        while (($row = fgetcsv($handle)) !== FALSE) {
            // [timestamp, min, avg, max, mdev, spikes]
            $data[$name]['logs'][] = [
                'time' => $row[0],
                'avg' => (float)$row[2],
                'max' => (float)$row[3],
                'mdev' => (float)$row[4],
                'spikes' => (int)$row[5]
            ];
        }
        fclose($handle);
    }
}

echo "<h1>Network Performance Dashboard</h1>";
echo "<table border='1' cellpadding='10' style='border-collapse:collapse; font-family:sans-serif;'>";
echo "<tr style='background:#eee;'><th>Device</th><th>Metingen</th><th>Gem. Latency</th><th>Grootste Spike</th><th>Totaal Spikes (>20ms)</th><th>Stabiliteit</th></tr>";

foreach ($data as $name => $info) {
    $logs = $info['logs'];
    $totalAvg = array_sum(array_column($logs, 'avg')) / count($logs);
    $maxPeak = max(array_column($logs, 'max'));
    $totalSpikes = array_sum(array_column($logs, 'spikes'));

    // Stabiliteit: percentage metingen zonder spikes
    $cleanMetingen = count(array_filter($logs, function($l) { return $l['spikes'] == 0; }));
    $stability = ($cleanMetingen / count($logs)) * 100;

    $color = ($stability > 95) ? "green" : (($stability > 80) ? "orange" : "red");

    echo "<tr>";
    echo "<td><strong>$name</strong></td>";
    echo "<td>" . count($logs) . "</td>";
    echo "<td>" . round($totalAvg, 2) . " ms</td>";
    echo "<td>" . round($maxPeak, 2) . " ms</td>";
    echo "<td>$totalSpikes</td>";
    echo "<td style='color:white; background:$color;'>" . round($stability, 1) . "%</td>";
    echo "</tr>";
}
echo "</table>";

// Optioneel: Laat de laatste 10 spikes van de garage zien
if (isset($data['garage'])) {
    echo "<h2>Laatste Spikes Garage (Potential Audio Drops)</h2><ul>";
    $spikyLogs = array_reverse(array_filter($data['garage']['logs'], function($l) { return $l['spikes'] > 0; }));
    foreach (array_slice($spikyLogs, 0, 10) as $log) {
        echo "<li>{$log['time']}: {$log['max']}ms ({$log['spikes']} pakketten traag)</li>";
    }
    echo "</ul>";
}
?>
