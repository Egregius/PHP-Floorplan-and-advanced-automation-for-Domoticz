<?php
// analyze_daikin.php

$log_file = '/var/www/daikin_learn.json';
$config_file = '/var/www/daikin_config.json';

// Lees alle log entries
$lines = file($log_file, FILE_IGNORE_NEW_LINES);
$entries = array_map('json_decode', $lines);

echo "\n=== LIVING KAMER ANALYSE ===\n";
echo "Totaal metingen: " . count($entries) . "\n\n";

// Bereken statistics
$overshoots = 0;
$undershoots = 0;
$stable_time = 0;
$total_time = count($entries);

$dif_sum = 0;
$abs_dif_sum = 0;

for ($i = 0; $i < count($entries) - 1; $i++) {
    $current = $entries[$i];
    $next = $entries[$i + 1];

    // Check of temperatuur de verkeerde kant op ging
    if ($current->d < 0 && $next->d > $current->d) {
        $undershoots++;
    }
    if ($current->d > 0 && $next->d > $current->d) {
        $overshoots++;
    }

    // Check stabiliteit
    if (abs($next->d) <= 0.5) {
        $stable_time++;
    }

    $dif_sum += $current->d;
    $abs_dif_sum += abs($current->d);
}

$avg_dif = $dif_sum / $total_time;
$avg_abs_dif = $abs_dif_sum / $total_time;
$stability_pct = ($stable_time / $total_time) * 100;

echo "Gemiddelde afwijking: " . number_format($avg_dif, 2) . "°C\n";
echo "Gemiddelde absolute afwijking: " . number_format($avg_abs_dif, 2) . "°C\n";
echo "Stabiliteit (binnen ±0.5°C): " . number_format($stability_pct, 1) . "%\n";
echo "Overshoots (te warm): $overshoots (" . number_format($overshoots/$total_time*100, 1) . "%)\n";
echo "Undershoots (te koud): $undershoots (" . number_format($undershoots/$total_time*100, 1) . "%)\n";

// Analyseer trend_factor effectiviteit
echo "\nTrend analyse:\n";
$correct_predictions = 0;
$wrong_predictions = 0;

for ($i = 0; $i < count($entries) - 1; $i++) {
    $current = $entries[$i];
    $next = $entries[$i + 1];

    if ($current->ed < 0) {
        if ($next->d > $current->d) $correct_predictions++;
        else $wrong_predictions++;
    } elseif ($current->ed > 0) {
        if ($next->d < $current->d) $correct_predictions++;
        else $wrong_predictions++;
    }
}

$prediction_accuracy = ($correct_predictions / ($correct_predictions + $wrong_predictions)) * 100;
echo "Trend voorspelling accuraatheid: " . number_format($prediction_accuracy, 1) . "%\n";
echo "Huidige trend_factor: " . $entries[count($entries)-1]->tf . "\n";

// Suggesties
if ($prediction_accuracy < 60) {
    echo "\n⚠️  SUGGESTIE: Verhoog trend_factor (huidige waarde te laag)\n";
} elseif ($prediction_accuracy > 80 && $stability_pct < 70) {
    echo "\n⚠️  SUGGESTIE: Verlaag trend_factor (te agressief)\n";
} else {
    echo "\n✓ Trend_factor lijkt goed afgesteld\n";
}

// Laat laatste 10 entries zien
echo "\n=== LAATSTE 10 METINGEN ===\n";
$last_10 = array_slice($entries, -10);
foreach ($last_10 as $e) {
    echo date('H:i:s', $e->t) . " | dif: " . sprintf("%+.2f", $e->d) .
         " | trend: " . sprintf("%+.2f", $e->tr) .
         " | eff_dif: " . sprintf("%+.2f", $e->ed) .
         " | set: " . $e->sb . "→" . $e->sa .
         " | sp: " . $e->sp . "\n";
}

echo "\n";
