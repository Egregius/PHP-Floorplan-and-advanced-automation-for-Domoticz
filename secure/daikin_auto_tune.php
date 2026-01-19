<?php
// auto_tune_daikin.php - Draai dit bijvoorbeeld dagelijks via cron

$log_file = '/var/www/daikin_learn.json';
$config_file = '/var/www/daikin_config.json';
echo "=== DAIKIN AUTO-TUNE ===\n";
echo date('Y-m-d H:i:s') . "\n\n";

// Lees huidige configuratie
if (file_exists($config_file)) {
    $config = json_decode(file_get_contents($config_file), true);
} else {
    $config = [
        'trend_factor' => ['living' => 2, 'kamer' => 2.5, 'alex' => 2.5],
        'history' => [],
        'last_tune' => 0
    ];
}

// Check of we al recent getuned hebben (minimaal 1 dag wachten)
if ($config['last_tune'] > time() - 86400) {
    echo "⏸️  Te recent getuned (" . date('Y-m-d H:i', $config['last_tune']) . "), skip.\n";
    exit;
}

// Analyseer laatste 24 uur data (genoeg voor goede analyse)
$one_day_ago = time() - 86400;
$lines = file($log_file, FILE_IGNORE_NEW_LINES);
$entries = array_filter(
    array_map('json_decode', $lines),
    function($e) use ($one_day_ago) { return $e->t > $one_day_ago; }
);

if (count($entries) < 200) {
    echo "❌ Te weinig data (slechts " . count($entries) . " entries), minimaal 200 nodig.\n";
    exit;
}

echo "📊 Analyseer " . count($entries) . " metingen van afgelopen 24u\n\n";

// Bereken performance metrics
$stability = 0;
$overshoots = 0;
$undershoots = 0;
$total_abs_dif = 0;

for ($i = 0; $i < count($entries) - 1; $i++) {
    $current = $entries[$i];
    $next = $entries[$i + 1];

    // Stabiliteit: binnen ±0.5°C
    if (abs($current->d) <= 0.5) $stability++;

    // Overshoot: temperatuur gaat te ver de verkeerde kant op
    if ($current->d > 0 && $next->d > $current->d + 0.2) $overshoots++;
    if ($current->d < 0 && $next->d < $current->d - 0.2) $undershoots++;

    $total_abs_dif += abs($current->d);
}

$stability_score = $stability / count($entries);
$overshoot_rate = ($overshoots + $undershoots) / count($entries);
$avg_abs_dif = $total_abs_dif / count($entries);

// Performance score: hoe hoger, hoe beter
// We willen: hoge stabiliteit, lage overshoots, kleine afwijkingen
$performance = $stability_score - ($overshoot_rate * 2) - ($avg_abs_dif * 0.5);

echo "Huidige prestaties:\n";
echo "  Stabiliteit: " . number_format($stability_score * 100, 1) . "%\n";
echo "  Overshoot rate: " . number_format($overshoot_rate * 100, 1) . "%\n";
echo "  Gem. afwijking: " . number_format($avg_abs_dif, 2) . "°C\n";
echo "  Performance score: " . number_format($performance, 3) . "\n\n";

// Vergelijk met vorige performance
$current_factor = $config['trend_factor']['living'];
$last_performance = $config['history']['performance'] ?? null;
$last_factor = $config['history']['trend_factor'] ?? $current_factor;

echo "Huidige trend_factor: $current_factor\n";

// Besluit of we moeten aanpassen
$adjustment = 0;

if ($last_performance === null) {
    // Eerste keer, sla alleen baseline op
    echo "📝 Eerste run, sla baseline op.\n";
} else {
    echo "Vorige performance: " . number_format($last_performance, 3) . " (factor: $last_factor)\n";

    // Gradient descent met momentum
    if ($performance < $last_performance - 0.02) {
        // Prestatie significant verslechterd
        echo "❌ Prestatie verslechterd!\n";

        // Keer richting om: als we factor verhoogden, verlaag nu
        $last_adjustment = $current_factor - $last_factor;
        $adjustment = -$last_adjustment * 0.5; // Halve stap terug
        echo "   → Keer richting om: " . ($adjustment > 0 ? '+' : '') . number_format($adjustment, 2) . "\n";

    } elseif ($performance > $last_performance + 0.02) {
        // Prestatie verbeterd, ga door in deze richting
        echo "✅ Prestatie verbeterd!\n";

        $last_adjustment = $current_factor - $last_factor;
        $adjustment = $last_adjustment * 0.8; // Ga voorzichtig door
        echo "   → Ga door: " . ($adjustment > 0 ? '+' : '') . number_format($adjustment, 2) . "\n";

    } else {
        // Prestatie stabiel, probeer kleine optimalisatie
        echo "➡️  Prestatie stabiel.\n";

        if ($overshoot_rate > 0.15) {
            // Te veel overshoots
            $adjustment = -0.15;
            echo "   → Te veel overshoots, verlaag factor\n";
        } elseif ($stability_score < 0.65) {
            // Te instabiel
            $adjustment = 0.15;
            echo "   → Te instabiel, verhoog factor\n";
        } elseif ($avg_abs_dif > 0.6) {
            // Gemiddeld te ver van target
            $adjustment = 0.1;
            echo "   → Gemiddeld te ver van target, verhoog factor\n";
        } else {
            // Prima, kleine willekeurige verkenning
            $adjustment = (rand(0, 1) ? 0.05 : -0.05);
            echo "   → Exploratie: " . ($adjustment > 0 ? '+' : '') . number_format($adjustment, 2) . "\n";
        }
    }
}

$new_factor = $current_factor + $adjustment;

// Beperk tussen 0.5 en 5.0
$new_factor = max(0.5, min(5.0, $new_factor));

// Rond af op 0.05
$new_factor = round($new_factor * 20) / 20;

if ($new_factor != $current_factor) {
    echo "\n🔧 AANPASSING: trend_factor $current_factor → $new_factor\n";
    $config['trend_factor']['living'] = $new_factor;
} else {
    echo "\n⏸️  Geen aanpassing nodig (binnen limieten)\n";
}

// Update history
$config['history'] = [
    'performance' => $performance,
    'trend_factor' => $new_factor,
    'stability' => $stability_score,
    'overshoot_rate' => $overshoot_rate,
    'avg_abs_dif' => $avg_abs_dif,
    'timestamp' => time(),
    'data_points' => count($entries)
];
$config['last_tune'] = time();

// Sla configuratie op
file_put_contents($config_file, json_encode($config, JSON_PRETTY_PRINT));

echo "\n✅ Configuratie opgeslagen!\n";
echo "Volgende tune frühestens: " . date('Y-m-d H:i', time() + 86400) . "\n";

// Optioneel: roteer log file als die te groot wordt
$log_size = filesize($log_file) / 1024 / 1024; // MB
if ($log_size > 10) {
    echo "\n📦 Log file is " . number_format($log_size, 1) . "MB, archiveer oude data...\n";

    // Bewaar alleen laatste 7 dagen
    $seven_days_ago = time() - (7 * 86400);
    $all_lines = file($log_file, FILE_IGNORE_NEW_LINES);
    $recent_lines = array_filter(
        $all_lines,
        function($line) use ($seven_days_ago) {
            $entry = json_decode($line);
            return $entry && $entry->t > $seven_days_ago;
        }
    );

    // Archiveer oude data
/*    $archive_file = '/path/to/daikin_learn_' . date('Y-m-d') . '.json.gz';
    $old_lines = array_diff($all_lines, $recent_lines);
    if (count($old_lines) > 0) {
        $gz = gzopen($archive_file, 'w9');
        gzwrite($gz, implode("\n", $old_lines));
        gzclose($gz);
        echo "   Gearchiveerd " . count($old_lines) . " entries naar $archive_file\n";
    }*/

    // Schrijf alleen recente data terug
    file_put_contents($log_file, implode("\n", $recent_lines) . "\n");
    echo "   Log verkleind naar " . count($recent_lines) . " entries\n";
}

echo "\n";
