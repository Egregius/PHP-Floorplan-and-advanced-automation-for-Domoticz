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
    float  $sun     = 0,
    float  $clouds  = 0,
    float  $rain    = 0,
    float  $temperature = 0,
    float  $mintemp = 0,
    float  $maxtemp = 0,
    float  $fog     = 0,
    float  $snow    = 0,
    float  $thunder = 0,
    float  $wind    = 0,
    string $format  = 'jpg'
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

    // ── IMAGE 2: TEMPERATURE (Screen-wide & Progress bar) ───────────────────
    $im2 = generate_base_background($W, $H, $sun, $clouds, $fog, $snow, $thunder);
    $C2  = fn($r,$g,$b)    => imagecolorallocate($im2, (int)$r, (int)$g, (int)$b);
    $CA2 = fn($r,$g,$b,$a) => imagecolorallocatealpha($im2, (int)$r, (int)$g, (int)$b, min(127,(int)$a));

    if     ($temperature >= 30) [$tR,$tG,$tB] = [255, 112,  58];
    elseif ($temperature >= 20) [$tR,$tG,$tB] = [255, 186, 102];
    elseif ($temperature >= 10) [$tR,$tG,$tB] = [155, 202, 255];
    else                        [$tR,$tG,$tB] = [125, 180, 255];
    $tCol   = $C2($tR, $tG, $tB);
    $dimCol = $C2((int)($tR*.65), (int)($tG*.65), (int)($tB*.65));

    $font =  '/var/www/html/fonts/JetBrainsMono.woff2';
    if (file_exists($font)) {
        // Hoofdtemperatuur: komma in plaats van punt, geen graden symbool
        $mainStr = str_replace('.', ',', sprintf("%.1f", $temperature));
        $minStr  = sprintf("%.0f", $mintemp);
        $maxStr  = sprintf("%.0f", $maxtemp);

        // 1. Grote Hoofdtemperatuur (Bovenkant scherm)
        $fs1 = 54; $b1 = imagettfbbox($fs1, 0, $font, $mainStr);
        while (($b1[2] - $b1[0]) > ($W - 4) && $fs1 > 15) {
            $fs1--; $b1 = imagettfbbox($fs1, 0, $font, $mainStr);
        }
        imagettftext($im2, $fs1, 0, (int)(($W - ($b1[2] - $b1[0])) / 2) - $b1[0], 62, $tCol, $font, $mainStr);

        // 2. Visuele Temperatuurbalk (Midden van scherm)
        $barX1 = 8; $barX2 = $W - 8; $barY = 82; $barH = 6;
        $totalBarW = $barX2 - $barX1;
        // Achtergrond balk (semi-transparant donker)
        imagefilledrectangle($im2, $barX1, $barY, $barX2, $barY + $barH, $CA2(0, 0, 0, 75));
        
        // Bereken positie huidige temp binnen min/max range
        $range = $maxtemp - $mintemp;
        $pct = ($range > 0) ? ($temperature - $mintemp) / $range : 0.5;
        $pct = max(0, min(1, $pct));
        $fillX = (int)($barX1 + ($totalBarW * $pct));

        // Gevulde actieve balk deel
        if ($fillX > $barX1) {
            imagefilledrectangle($im2, $barX1, $barY, $fillX, $barY + $barH, $CA2($tR, $tG, $tB, 30));
        }
        // Subtiele indicator stip op de huidige stand
        imagefilledellipse($im2, $fillX, (int)($barY + $barH / 2), 6, 6, $tCol);

        // 3. Grote Min/Max Waarden (Onderkant scherm)
        $fsMin = 26; $bMin = imagettfbbox($fsMin, 0, $font, $minStr);
        while (($bMin[2] - $bMin[0]) > (($W / 2) - 6) && $fsMin > 10) {
            $fsMin--; $bMin = imagettfbbox($fsMin, 0, $font, $minStr);
        }
        imagettftext($im2, $fsMin, 0, 6 - $bMin[0], 134, $C2(130, 185, 255), $font, $minStr);

        $fsMax = 26; $bMax = imagettfbbox($fsMax, 0, $font, $maxStr);
        while (($bMax[2] - $bMax[0]) > (($W / 2) - 6) && $fsMax > 10) {
            $fsMax--; $bMax = imagettfbbox($fsMax, 0, $font, $maxStr);
        }
        imagettftext($im2, $fsMax, 0, $W - 6 - ($bMax[2] - $bMax[0]) - $bMax[0], 134, $C2(255, 130, 100), $font, $maxStr);

    } else {
        // Fallback GD built-in fonts
        $ms = str_replace('.', ',', sprintf("%.1f", $temperature));
        $rs = sprintf("%.0f/%.0f", $mintemp, $maxtemp);
        imagestring($im2, 5, (int)(($W - imagefontwidth(5)*strlen($ms))/2), 35, $ms, $tCol);
        imagestring($im2, 4, (int)(($W - imagefontwidth(4)*strlen($rs))/2), 105, $rs, $dimCol);
    }

    $imgData2 = get_image_data($im2, $format, $W, $H);
    push_to_nextube(6, $imgData2, $format);
    imagedestroy($im2);
    
    echo "Images successfully pushed to Nextube.";
}