<?php
$user = basename(__FILE__);
if ($d['brander']->s != 'Off') sw('brander', 'Off', $user.':'.__LINE__);

$bigdif = 0;
$daikinDefaults = ['power'=>99, 'mode'=>99, 'set'=>99, 'fan'=>99, 'spmode'=>99];
$daikin ??= new stdClass();

$anyCooling = false;

foreach (['living', 'kamer', 'alex'] as $k) {
    if ($d[$k.'_set']->m == 1 && $d[$k.'_set']->s < 33 && ($d['raam'.$k]->s == 'Closed' || ($d['raam'.$k]->s == 'Open' && past('raam'.$k) <= 60))) {
        $anyCooling = true;
    }
}

foreach (['living', 'kamer', 'alex'] as $k) {
    $daikin->$k ??= (object)$daikinDefaults;

    // FIX: a set value of 1-5 (while m==1) is a POWER LEVEL, not a temperature.
    // The old code subtracted this level from the room temp regardless, producing
    // a meaningless "difference" (e.g. 22 - 5 = 17) that pushed $bigdif (and thus
    // $maxpow) to its max as a side effect. Only compute a real diff when the
    // room is in direct-temperature manual mode.
    $isPowerLevel = ($d[$k.'_set']->m == 1 && $d[$k.'_set']->s >= 1 && $d[$k.'_set']->s <= 5);

    if ($d[$k.'_set']->s != 'D' && $d[$k.'_set']->s != 'Off' && !$isPowerLevel) {
        ${'dif'.$k} = number_format($d[$k.'_temp']->s - $d[$k.'_set']->s, 1);
        if (${'dif'.$k} > $bigdif) $bigdif = ${'dif'.$k};
    } else {
        ${'dif'.$k} = 0;
    }
}

if ($d['buiten_temp']->s > 30) $bigdif *= 1.5;
elseif ($d['buiten_temp']->s > 26) $bigdif *= 1.2;

if ($bigdif >= 2.2) $maxpow = 100;
elseif ($bigdif >= 1.8) $maxpow = 90;
elseif ($bigdif >= 1.4) $maxpow = 80;
elseif ($bigdif >= 1.0) $maxpow = 70;
elseif ($bigdif >= 0.6) $maxpow = 60;
elseif ($bigdif >= 0.3) $maxpow = 50;
else $maxpow = 40;

if ($d['weg']->s > 0) $maxpow = 40;
$maxpow = max(40, min(100, $maxpow));

foreach (['living', 'kamer', 'alex'] as $k) {
    if ($d[$k.'_set']->m == 1 && $d[$k.'_set']->s < 33 && ($d['raam'.$k]->s == 'Closed' || ($d['raam'.$k]->s == 'Open' && past('raam'.$k) <= 60))) {
        if ($d[$k.'_set']->s == 2) $maxpow = max($maxpow, 50);
        elseif ($d[$k.'_set']->s == 3) $maxpow = max($maxpow, 60);
        elseif ($d[$k.'_set']->s == 4) $maxpow = max($maxpow, 80);
        elseif ($d[$k.'_set']->s == 5) $maxpow = max($maxpow, 100);
    }
}

foreach (['living', 'kamer', 'alex'] as $k) {
    $fan = 'A';
    $spmode = -1;
    if (($d[$k.'_set']->m == 0 || $d[$k.'_set']->m == 2) && ($d['raam'.$k]->s == 'Closed' || ($d['raam'.$k]->s == 'Open' && past('raam'.$k) <= 60))) {
        if ($anyCooling) {
            // Another room is actively cooling: join Cool mode at a fixed
            // setpoint instead of Dry, so this zone adds real cooling demand
            // and helps the shared compressor ramp up.
            $mode = 3;
            $power = 1;
            $set = 23;
        } else {
            $mode = 2;
            $power = 1;
            $set = 22;
        }
        if ($d[$k.'_set']->s != 'D') store($k.'_set', 'D', $user.':'.__LINE__);
    } elseif ($d[$k.'_set']->m == 1 && $d[$k.'_set']->s < 33 && ($d['raam'.$k]->s == 'Closed' || ($d['raam'.$k]->s == 'Open' && past('raam'.$k) <= 60))) {
        $mode = 3;
        $power = 1;
        if ($d[$k.'_set']->s == 1) { $fan = 3; $set = 18; }
        elseif ($d[$k.'_set']->s == 2) { $fan = 4; $set = 18; }
        elseif ($d[$k.'_set']->s == 3) { $fan = 5; $set = 18; }
        elseif ($d[$k.'_set']->s == 4) { $fan = 6; $set = 18; $spmode = 0; }
        elseif ($d[$k.'_set']->s == 5) { $fan = 7; $set = 18; $spmode = 1; }
        else {
            $set = $d[$k.'_set']->s;
            if ((int)${'dif'.$k} > 2) $spmode = 1;
            elseif ((int)${'dif'.$k} > 1) $spmode = 0;
        }
    } elseif ($d['weg']->s >= 2 && $d['dag']->s >= 10 && $d['c'] >= 10) {
        $mode = 2;
        $power = 1;
    } else {
        $mode = 2;
        $power = 0;
        $set = 33;
        if ($d[$k.'_set']->s != 'Off') store($k.'_set', 'Off', $user.':'.__LINE__);
    }

    if ($d['daikin']->s == 'On') {
        if ((($daikin->$k->set != $set || $daikin->$k->power != $power || $daikin->$k->mode != $mode || $daikin->$k->spmode != $spmode || $daikin->$k->fan != $fan) && $spmode < 2) || ($power != 0 && $daikin->$k->lastset <= $time - 291) || ($power == 0 && $daikin->$k->lastset <= $time - 291)) {
            if (daikinset($k, $power, $mode, $set, $user.':'.__LINE__, $fan, $spmode, $maxpow)) {
                $daikin->$k->power = $power;
                $daikin->$k->mode = $mode;
                $daikin->$k->fan = $fan;
                $daikin->$k->set = $set;
                $daikin->$k->spmode = $spmode;
                $daikin->$k->lastset = $time;
            }
        }
    } elseif ($power == 1 && $d['daikin']->s == 'Off' && past('daikin') > 900) {
        sw('daikin', 'On');
    }
}

require('_Rolluiken_Cooling.php');
require('_TC_badkamer.php');