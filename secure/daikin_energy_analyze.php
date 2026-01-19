<?php
// energy_analyze.php - Nieuw script voor energie-analyse

$log_file = '/var/www/daikin_learn.json';

$lines = file($log_file, FILE_IGNORE_NEW_LINES);
$entries = array_map('json_decode', $lines);

// Filter entries met energie data
$with_energy = array_filter($entries, fn($e) => isset($e->kwh) && isset($e->w));

echo "=== ENERGIE ANALYSE ===\n";
echo "Periode: " . date('Y-m-d H:i', reset($with_energy)->t) . " tot " .
     date('Y-m-d H:i', end($with_energy)->t) . "\n";
echo "Totaal metingen: " . count($with_energy) . "\n\n";

// Bereken totaal verbruik over de periode
$first_kwh = reset($with_energy)->kwh;
$last_kwh = end($with_energy)->kwh;
$total_kwh = $last_kwh - $first_kwh;
$duration_hours = (end($with_energy)->t - reset($with_energy)->t) / 3600;

echo "VERBRUIK:\n";
echo "Totaal verbruik: " . number_format($total_kwh, 2) . " kWh\n";
echo "Periode duur: " . number_format($duration_hours, 1) . " uur\n";
echo "Gemiddeld vermogen: " . number_format($total_kwh / $duration_hours * 1000, 0) . " Watt\n\n";

// Correlatie: verbruik vs temperatuurafwijking
echo "CORRELATIE:\n";

$low_dif = array_filter($with_energy, fn($e) => abs($e->d) <= 0.3);
$high_dif = array_filter($with_energy, fn($e) => abs($e->d) > 0.8);

if (count($low_dif) > 10 && count($high_dif) > 10) {
    $avg_power_stable = array_sum(array_map(fn($e) => $e->w, $low_dif)) / count($low_dif);
    $avg_power_unstable = array_sum(array_map(fn($e) => $e->w, $high_dif)) / count($high_dif);

    echo "Gem. vermogen bij stabiel (|dif|<0.3°C): " . number_format($avg_power_stable, 0) . "W\n";
    echo "Gem. vermogen bij instabiel (|dif|>0.8°C): " . number_format($avg_power_unstable, 0) . "W\n";
    echo "Verschil: " . number_format($avg_power_unstable - $avg_power_stable, 0) . "W ";
    echo "(" . number_format(($avg_power_unstable / $avg_power_stable - 1) * 100, 1) . "%)\n\n";
}

// Verbruik per power mode
echo "VERBRUIK PER MODE:\n";
foreach ([-1, 0, 1] as $mode) {
    $mode_entries = array_filter($with_energy, fn($e) => $e->sp == $mode);
    if (count($mode_entries) > 5) {
        $avg_w = array_sum(array_map(fn($e) => $e->w, $mode_entries)) / count($mode_entries);
        $mode_name = $mode == 1 ? 'Power' : ($mode == 0 ? 'Normaal' : 'Eco');
        echo "$mode_name: " . number_format($avg_w, 0) . "W (n=" . count($mode_entries) . ")\n";
    }
}

// Dagelijks verbruik (als we genoeg dagen hebben)
echo "\n=== DAGELIJKS OVERZICHT ===\n";

$by_day = [];
foreach ($with_energy as $e) {
    $day = date('Y-m-d', $e->t);
    if (!isset($by_day[$day])) {
        $by_day[$day] = ['entries' => [], 'first_kwh' => null, 'last_kwh' => null];
    }
    $by_day[$day]['entries'][] = $e;
    if ($by_day[$day]['first_kwh'] === null) $by_day[$day]['first_kwh'] = $e->kwh;
    $by_day[$day]['last_kwh'] = $e->kwh;
}

foreach ($by_day as $day => $data) {
    $day_kwh = $data['last_kwh'] - $data['first_kwh'];
    $avg_dif = array_sum(array_map(fn($e) => abs($e->d), $data['entries'])) / count($data['entries']);
    $stability = count(array_filter($data['entries'], fn($e) => abs($e->d) <= 0.5)) / count($data['entries']) * 100;

    echo "$day: " . number_format($day_kwh, 2) . " kWh | ";
    echo "gem. afwijking: " . number_format($avg_dif, 2) . "°C | ";
    echo "stabiliteit: " . number_format($stability, 0) . "%\n";
}

// Efficiency score
echo "\n=== EFFICIENCY SCORE ===\n";
$stability_score = count(array_filter($with_energy, fn($e) => abs($e->d) <= 0.5)) / count($with_energy);
$avg_abs_dif = array_sum(array_map(fn($e) => abs($e->d), $with_energy)) / count($with_energy);

echo "Stabiliteit: " . number_format($stability_score * 100, 1) . "%\n";
echo "Gem. afwijking: " . number_format($avg_abs_dif, 2) . "°C\n";
echo "Geschat efficiency rating: ";

if ($stability_score > 0.8 && $avg_abs_dif < 0.4) {
    echo "⭐⭐⭐⭐⭐ Excellent\n";
} elseif ($stability_score > 0.7 && $avg_abs_dif < 0.6) {
    echo "⭐⭐⭐⭐ Goed\n";
} elseif ($stability_score > 0.6 && $avg_abs_dif < 0.8) {
    echo "⭐⭐⭐ Redelijk\n";
} else {
    echo "⭐⭐ Kan beter (tune parameters)\n";
}

echo "\n💡 TIP: Hogere stabiliteit = lager energieverbruik\n";
