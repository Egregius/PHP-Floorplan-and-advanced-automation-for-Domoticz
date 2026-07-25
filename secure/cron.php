#!/usr/bin/php
<?php
declare(strict_types=1);
gc_enable();
require '/var/www/html/secure/functions.php';
lg('🟢 Starting CRON loop...','cron');
$t = $weekend = $dow = null;
$time=time();
$db = Database::getInstance();
$d=fetchdata();
$livingtemps=[];
$memory_cache = [];
$d['time'] = $time;
define('LOOP_START', $time);
$user='CRONstart';

$rollingBuffers = [];
if (file_exists('/dev/shm/cache/rollingAvg.json')) {
    $decoded = json_decode(file_get_contents('/dev/shm/cache/rollingAvg.json'), true);
    if (is_array($decoded)) {
        $rollingBuffers = $decoded;
    }
}
$poolRuntime = ['date' => date('Y-m-d'), 'seconds' => 0, 'lastCheck' => $time, 'automatisch' => false];
if (file_exists('/dev/shm/cache/poolRuntime.json')) {
    $decoded = json_decode(file_get_contents('/dev/shm/cache/poolRuntime.json'), true);
    if (is_array($decoded)) {
        $poolRuntime = $decoded;
    }
}
$steenautomatischaan = $poolRuntime['automatisch'] ?? false;

// Using https://github.com/php-mqtt/client
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
require '/var/www/vendor/autoload.php';
$connectionSettings=(new ConnectionSettings)
	->setUsername('mqtt')
	->setPassword('mqtt')
	->setKeepAliveInterval(60);
$mqtt=new MqttClient('192.168.30.22',1883,basename(__FILE__),MqttClient::MQTT_3_1,null,null);
$mqtt->connect($connectionSettings,true);
foreach (['badkamervuur2','badkamervuur1','water'] as $i) sw($i,'Off');
if ($d['weg']->s>0) {
	foreach (['boseliving','bosekeuken','ipaddock','mac','media','zetel'] as $i) sw($i, 'Off');
}
$last10 = $last30 = $last60 = $last300 = $last3600 = $last90 = $last900 = $daikinsun = $time-3600;
$prevdaikinrunning=false;
updateWekker($t, $weekend, $dow, $d);
foreach ($d as $k=>$v) {
	if (isset($v->f)&&$v->f===1) publishmqtt('d/'.$k,toJsonClean($v));
}
$daikinDefaults = ['power'=>99,'mode'=>99,'set'=>99,'fan'=>99,'spmode'=>99,'maxpow'=>99,'lastset'=>$time-300];
$daikin ??= new stdClass();
foreach (array('living', 'kamer', 'alex') as $k) {
	$daikin->$k ??= (object)$daikinDefaults;
}
while (true) {
	$time = time();
	$d['time'] = $time;
	if ($time % 10 === 0 && $time !== $last10) {
		$last10 = $time;
		$d = fetchdata();
		include '_cron10.php';
		if (checkInterval($last60, 60, $time)) {include '_cron60.php' ;stoploop();}
		if (checkInterval($last30, 20, $time))  {
			$user = 'HEATING';
			if ($d['heating']->s == -2) include '_TC_cooling_airco.php';
			elseif ($d['heating']->s == -1) include '_TC_cooling_passive.php';
			elseif ($d['heating']->s == 0) include '_TC_neutral.php';
			elseif ($d['heating']->s > 0)  include '_TC_heating.php';
			$mqtt->publish('d/t',json_encode(1));
		}
		if (checkInterval($last300, 300, $time)) {include '_cron300.php';updateWekker($t, $weekend, $dow, $d);}
		if (checkInterval($last3600, 3600, $time)) include '_cron3600.php';
		if (checkInterval($last90, 90, $time)) include '_weather.php';
		if (checkInterval($last900, 900, $time)) include '_cron900.php';
	}
	
	$next = floor($time / 10) * 10 + 10;
	$sleep = $next - microtime(true);
	$sleep = (int)round($sleep * 1e6)-1800;
	if ($sleep > 0) usleep($sleep);
}
function checkInterval(&$last, $interval, $time) {
	if (($time % $interval === 0 && $last !== $time) || $last <= $time - $interval) {
		$last = $time;
		return true;
	}
	return false;
}
function stoploop() {
	$script = __FILE__;
	if (filemtime(__DIR__ . '/functions.php') > LOOP_START) {
		lg('🛑 functions.php gewijzigd → restarting cron loop...');
		exit;
	}
	if (filemtime(__DIR__ . '/cron.php') > LOOP_START) {
		lg('🛑 cron.php gewijzigd → restarting cron loop...');
		exit;
	}
	static $cycles=0;
	if($cycles>=50) {
		gc_collect_cycles();
		$cycles=0;
	} else $cycles++;
}



//Nextube weather
function push_to_nextube(int $tube, string $imageData, string $format): void {
    $mime = $format === 'jpg' ? 'image/jpeg' : "image/{$format}";
    $ch = curl_init("http://192.168.40.93/api/cx_image?tube={$tube}");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: {$mime}"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $imageData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

function generate_base_background(
    int $W, int $H, float $sun, float $clouds, float $fog, float $snow, float $thunder
): GdImage {
    $im = imagecreatetruecolor($W, $H);
    imagealphablending($im, true);
    imagesavealpha($im, true);

    $C  = fn($r,$g,$b)    => imagecolorallocate($im, (int)$r, (int)$g, (int)$b);
    $CA = fn($r,$g,$b,$a) => imagecolorallocatealpha($im, (int)$r, (int)$g, (int)$b, min(127,(int)$a));

    for ($y = 0; $y < $H; $y++) {
        $t = $y / $H;
        if ($thunder > 40)           $col = $C(10+$t*8,   8+$t*10,  20+$t*20);
        elseif ($fog     > 50)       $col = $C(16+$t*14, 16+$t*14,  20+$t*16);
        elseif ($snow    > 50)       $col = $C( 8+$t*10, 10+$t*12,  20+$t*22);
        elseif ($sun > 60 && $clouds < 40) $col = $C(20+$t*12, 12+$t* 8,   4+$t* 4);
        else                               $col = $C( 8+$t*10, 10+$t*12,  18+$t*18);
        imageline($im, 0, $y, $W-1, $y, $col);
    }

    if ($fog > 0) {
        for ($b = 0; $b < 8; $b++) {
            $by    = 5 + $b * 19;
            $alpha = (int)max(8, min(122, 127 - $fog * 0.86 + $b * 5));
            $dx    = ($b & 1) * 4;
            imagefilledrectangle($im, 0, $by, $W-1, $by+6, $CA(168+$dx, 174+$dx, 196+$dx, $alpha));
            imageline($im, 0, $by, $W-1, $by, $CA(200+$dx, 205+$dx, 218+$dx, min(127, $alpha+18)));
        }
    }
    return $im;
}

function get_image_data(GdImage $im, string $format, int $W, int $H): string {
    ob_start();
    if ($format === 'jpg') {
        imagejpeg($im, null, 95);
    } elseif ($format === 'bmp') {
        imagebmp($im);
    } elseif ($format === 'raw') {
        for ($y = 0; $y < $H; $y++) {
            for ($x = 0; $x < $W; $x++) {
                $col = imagecolorat($im, $x, $y);
                $rgb565 = ((($col>>16)&0xF8)<<8) | ((($col>>8)&0xFC)<<3) | (($col&0xFF)>>3);
                echo pack('n', $rgb565);
            }
        }
    } else {
        imagepng($im);
    }
    return ob_get_clean();
}

function nextube_image(
    float  $sun         = 0,
    float  $clouds      = 0,
    float  $rain        = 0,
    float  $temperature = 0,
    float  $mintemp     = 0,
    float  $maxtemp     = 0,
    float  $fog         = 0,
    float  $snow        = 0,
    float  $thunder     = 0,
    float  $wind        = 0,
    string $format      = 'jpg'
): void {
    $W = 80; $H = 160;

    // ── IMAGE 1: ICON ───────────────────────────────────────────────────────
    $im1 = generate_base_background($W, $H, $sun, $clouds, $fog, $snow, $thunder);
    
    $C  = fn($r,$g,$b)    => imagecolorallocate($im1, (int)$r, (int)$g, (int)$b);
    $CA = fn($r,$g,$b,$a) => imagecolorallocatealpha($im1, (int)$r, (int)$g, (int)$b, min(127,(int)$a));
    $EL = fn($cx,$cy,$rw,$rh,$col) => imagefilledellipse($im1,(int)$cx,(int)$cy,(int)($rw*2),(int)($rh*2),$col);

    if ($sun > 0) {
        $sx = ($clouds > 20) ? 25 : 40;
        $sy = ($clouds > 20) ? 65 : 80;
        $sr = max(12, min(32, (int)(10 + $sun * 0.22)));
        $si = $sun / 100;
        $sR = 255; $sG = (int)(210 - $si * 60); $sB = (int)( 55 - $si * 40);

        for ($rg = 5; $rg >= 1; $rg--) {
            $rr = $sr + $rg * 4 + 1;
            $EL($sx, $sy, $rr, $rr, $CA($sR, $sG, $sB, min(125, 88 + $rg * 7)));
        }
        $sunCol = $C($sR, $sG, $sB);
        $EL($sx, $sy, $sr, $sr, $sunCol);
        $EL($sx-2, $sy-4, max(4, (int)($sr * 0.40)), max(4, (int)($sr * 0.35)), $C(255, min(255,$sG+50), min(255,$sB+80)));
        if ($sun > 25) {
            $rl = max(5, (int)(3 + $sun * 0.15));
            for ($i = 0; $i < 8; $i++) {
                $a  = deg2rad($i * 45 + 22.5);
                imageline($im1, (int)($sx + cos($a) * ($sr + 4)), (int)($sy + sin($a) * ($sr + 4)), (int)($sx + cos($a) * ($sr + 4 + $rl)), (int)($sy + sin($a) * ($sr + 4 + $rl)), $sunCol);
            }
        }
    }

    $CX    = ($sun > 0) ? 52 : 40;
    $CY    = ($sun > 0) ? 92 : 80;
    $CR    = max(12, min(32, (int)(10 + $clouds * 0.22)));
    $dropY = (int)($CY + $CR * 0.55) + 6;

    if ($clouds > 0) {
        if ($thunder > 30)         [$cR,$cG,$cB] = [100, 100, 125];
        elseif ($rain > 30 || $snow > 30) [$cR,$cG,$cB] = [138, 140, 165];
        else                               [$cR,$cG,$cB] = [185, 188, 212];

        $cBase = $C($cR, $cG, $cB);
        $cMid  = $C($cR + 20, $cG + 20, $cB + 14);
        $cTop  = $C($cR + 38, $cG + 36, $cB + 22);
        $cShad = $C(max(0,$cR-22), max(0,$cG-22), max(0,$cB-20));

        imagefilledrectangle($im1, (int)($CX - $CR*.87), (int)($CY - $CR*.22), (int)($CX + $CR*.87), (int)($CY + $CR*.60), $cShad);
        $EL($CX - $CR*.62, $CY + $CR*.05, $CR*.72, $CR*.72, $cBase);
        $EL($CX + $CR*.52, $CY + $CR*.15, $CR*.65, $CR*.65, $cBase);
        $EL($CX - $CR*.28, $CY - $CR*.20, $CR*.78, $CR*.78, $cMid);
        $EL($CX + $CR*.28, $CY - $CR*.10, $CR*.70, $CR*.70, $cMid);
        $EL($CX, $CY - $CR*.38, $CR*.82, $CR*.82, $cTop);
        imagefilledrectangle($im1, (int)($CX - $CR*.82), (int)($CY + $CR*.05), (int)($CX + $CR*.82), (int)($CY + $CR*.55), $cBase);
    }

    if ($thunder > 0 && $clouds > 20) {
        $bx = (int)($CX + 4); $by = $dropY;
        $pts = [[$bx, $by], [$bx - 8, $by + 14], [$bx + 4, $by + 14], [$bx - 8, $by + 34]];
        if ($thunder > 40) {
            $glowCol = $CA(255, 255, 145, 74);
            for ($i = 0; $i < 3; $i++) {
                foreach ([[-2,0],[2,0],[0,-2],[0,2]] as [$dx,$dy]) {
                    imageline($im1, $pts[$i][0]+$dx, $pts[$i][1]+$dy, $pts[$i+1][0]+$dx, $pts[$i+1][1]+$dy, $glowCol);
                }
            }
        }
        $boltCol = $C(255, 238, 50);
        for ($i = 0; $i < 3; $i++) {
            imageline($im1, $pts[$i][0], $pts[$i][1], $pts[$i+1][0], $pts[$i+1][1], $boltCol);
        }
    }

    if ($rain > 0 && $clouds > 0) {
        $drops = max(4, min(12, (int)($rain / 8)));
        $dLen  = max(6, min(16, (int)(4 + $rain * 0.11)));
        $slant = ($wind > 40) ? 4 : 2;
        $dCol  = $C(85, 158, 255); $dHigh = $C(155, 210, 255);
        $spread = (int)($CR * 1.6);
        for ($d = 0; $d < $drops; $d++) {
            $ox = (int)(($d / max(1,$drops-1)) * $spread * 2 - $spread);
            $ry = $dropY + ($d % 4) * 5; $rx = (int)$CX + $ox;
            if ($ry + $dLen < $H) {
                imageline($im1, $rx, $ry, $rx - $slant, $ry + $dLen, $dCol);
                imagesetpixel($im1, $rx, $ry, $dHigh);
            }
        }
    }

    if ($snow > 0 && $clouds > 0) {
        $flakes = max(4, min(18, (int)($snow / 5)));
        $sBright = $C(242, 248, 255); $sSoft = $C(205, 228, 255);
        $big = ($snow > 60);
        $grid = [[0,0], [-10,6], [10,6], [-4,14], [8,14], [2,22], [-10,22], [10,24], [0,30], [-6,34], [8,34], [-4,40], [4,40]];
        for ($f = 0; $f < min($flakes, count($grid)); $f++) {
            $fx = (int)($CX + $grid[$f][0]); $fy = $dropY + $grid[$f][1];
            if ($fy >= $H) continue;
            imagesetpixel($im1, $fx, $fy, $sBright);
            if ($big) {
                foreach ([[-1,0],[1,0],[0,-1],[0,1]] as [$dx,$dy]) {
                    imagesetpixel($im1, $fx+$dx, $fy+$dy, $sSoft);
                }
            }
        }
    }

    if ($wind > 25) {
        $wa = (int)max(18, min(118, 127 - $wind * 0.92));
        $wCol = $CA(172, 188, 215, $wa);
        foreach ([[4,50,30,50],[10,70,45,70],[2,90,25,90]] as [$x1,$y1,$x2,$y2]) {
            imageline($im1, $x1, $y1, $x2, $y2, $wCol);
            imageline($im1, $x1, $y1+1, (int)(($x1+$x2)*.6), $y1+1, $CA(172, 188, 215, max(28, $wa-18)));
        }
    }

    $imgData1 = get_image_data($im1, $format, $W, $H);
    push_to_nextube(5, $imgData1, $format);
    imagedestroy($im1);

    // ── IMAGE 2: TEMPERATURE ────────────────────────────────────────────────
    $im2 = generate_base_background($W, $H, $sun, $clouds, $fog, $snow, $thunder);
    $C2  = fn($r,$g,$b)    => imagecolorallocate($im2, (int)$r, (int)$g, (int)$b);
    $CA2 = fn($r,$g,$b,$a) => imagecolorallocatealpha($im2, (int)$r, (int)$g, (int)$b, min(127,(int)$a));

    if     ($temperature >= 30) [$tR,$tG,$tB] = [255, 112,  58];
    elseif ($temperature >= 20) [$tR,$tG,$tB] = [255, 186, 102];
    elseif ($temperature >= 10) [$tR,$tG,$tB] = [155, 202, 255];
    else                        [$tR,$tG,$tB] = [125, 180, 255];
    $tCol = $C2($tR, $tG, $tB);

    $font = '/var/www/html/fonts/JetBrainsMono.woff2';
    if (file_exists($font)) {
        $absTemp = abs($temperature);
        $intPart = (int)$absTemp;
        $decPart = (int)round(($absTemp - $intPart) * 10);
        if ($decPart === 10) { $intPart++; $decPart = 0; }
        
        $intStr = ($temperature < 0 && ($intPart > 0 || $decPart > 0) ? '-' : '') . $intPart;
        $decStr = (string)$decPart;

        $minStr = sprintf("%.0f", $mintemp);
        $maxStr = sprintf("%.0f", $maxtemp);

        $fsInt = 52; 
        $fsDec = 26;
        $gap = 2; // Exacte witruimte in pixels tussen het hele getal en de decimaal

        do {
            $bInt = imagettfbbox($fsInt, 0, $font, $intStr);
            $bDec = imagettfbbox($fsDec, 0, $font, $decStr);
            
            $wInt = $bInt[2] - $bInt[0];
            $wDec = $bDec[2] - $bDec[0];
            $totalTextWidth = $wInt + $wDec + $gap;

            if ($totalTextWidth <= ($W - 4) || $fsInt <= 15) {
                break;
            }
            $fsInt--;
            $fsDec = (int)($fsInt / 2);
        } while (true);

        // startX berekend vanaf de absolute linkerkant van de bounding box om verschuivingen te tackelen
        $startX = (int)(($W - $totalTextWidth) / 2) - $bInt[0];
        $baselineY = 62;
        $floatY = 38;

        imagettftext($im2, $fsInt, 0, $startX, $baselineY, $tCol, $font, $intStr);
        // Decimaal start nu gegarandeerd NA het hele getal (startX + wInt + gap)
        imagettftext($im2, $fsDec, 0, $startX + $wInt + $gap, $floatY, $tCol, $font, $decStr);

        // 2. Visuele Temperatuurbalk (Midden van scherm)
        $barX1 = 8; $barX2 = $W - 8; $barY = 82; $barH = 6;
        $totalBarW = $barX2 - $barX1;
        imagefilledrectangle($im2, $barX1, $barY, $barX2, $barY + $barH, $CA2(0, 0, 0, 75));
        
        $range = $maxtemp - $mintemp;
        $pct = ($range > 0) ? ($temperature - $mintemp) / $range : 0.5;
        $pct = max(0, min(1, $pct));
        $fillX = (int)($barX1 + ($totalBarW * $pct));

        if ($fillX > $barX1) {
            imagefilledrectangle($im2, $barX1, $barY, $fillX, $barY + $barH, $CA2($tR, $tG, $tB, 30));
        }
        imagefilledellipse($im2, $fillX, (int)($barY + $barH / 2), 6, 6, $tCol);

        // 3. Grote Min/Max Waarden (Onderkant scherm)
        $fsMin = 32; $bMin = imagettfbbox($fsMin, 0, $font, $minStr);
        while (($bMin[2] - $bMin[0]) > (($W / 2) - 4) && $fsMin > 10) {
            $fsMin--; $bMin = imagettfbbox($fsMin, 0, $font, $minStr);
        }
        imagettftext($im2, $fsMin, 0, 4 - $bMin[0], 138, $C2(130, 185, 255), $font, $minStr);

        $fsMax = 32; $bMax = imagettfbbox($fsMax, 0, $font, $maxStr);
        while (($bMax[2] - $bMax[0]) > (($W / 2) - 4) && $fsMax > 10) {
            $fsMax--; $bMax = imagettfbbox($fsMax, 0, $font, $maxStr);
        }
        imagettftext($im2, $fsMax, 0, $W - 4 - ($bMax[2] - $bMax[0]) - $bMax[0], 138, $C2(255, 130, 100), $font, $maxStr);

    } else {
        $ms = str_replace('.', ',', sprintf("%.1f", $temperature));
        $rs = sprintf("%.0f/%.0f", $mintemp, $maxtemp);
        imagestring($im2, 5, (int)(($W - imagefontwidth(5)*strlen($ms))/2), 35, $ms, $tCol);
        imagestring($im2, 4, (int)(($W - imagefontwidth(4)*strlen($rs))/2), 105, $rs, $tCol);
    }

    $imgData2 = get_image_data($im2, $format, $W, $H);
    push_to_nextube(6, $imgData2, $format);
    imagedestroy($im2);
    
}

function addSample(array &$buffers, $key, $value, $maxSize = 60) {
    if (!isset($buffers[$key])) {
        $buffers[$key] = [];
    }
    $buffers[$key][] = $value;
    if (count($buffers[$key]) > $maxSize) {
        array_shift($buffers[$key]);
    }
}
function rollingHeld(array $buffers, $key, callable $condition, $n = null, $mode = 'all') {
    if (empty($buffers[$key])) {
        return false;
    }
    $values = $n ? array_slice($buffers[$key], -$n) : $buffers[$key];
    if (count($values) < ($n ?? 60)) {
        return false;
    }
    $minMatches = is_int($mode) ? $mode : count($values);
    $matches = 0;
    foreach ($values as $v) {
        if ($condition($v)) {
            $matches++;
            if ($matches >= $minMatches) {
                return true;
            }
        }
    }
    return false;
}

function rollingAbove($key, $threshold, $n = 12, $mode = 'all') {
    global $rollingBuffers;
    return rollingHeld($rollingBuffers, $key, function ($v) use ($threshold) {
        return $v >= $threshold;
    }, $n, $mode);
}

function rollingBelow($key, $threshold, $n = 12, $mode = 'all') {
    global $rollingBuffers;
    return rollingHeld($rollingBuffers, $key, function ($v) use ($threshold) {
        return $v <= $threshold;
    }, $n, $mode);
}
function socAdjustedWindow($soc, $minWindow, $maxWindow) {
    $soc = max(0, min(100, $soc)); // clamp voor de zekerheid
    $factor = $soc / 100;          // 0 = leeg, 1 = vol
    return (int) round($minWindow + $factor * ($maxWindow - $minWindow));
}
function saveRollingState(array $buffers) {
    file_put_contents('/dev/shm/cache/rollingAvg.json', json_encode($buffers));
}