<?php
/**
 * nextube_weather.php — Fancy Nextube weather image generator
 * Canvas: 80 × 160 px  |  Requires PHP 7.4+ with GD + FreeType
 *
 * All intensity params: 0–100
 *
 * URL params:
 *   sun, clouds, rain, fog, snow, thunder, wind   → intensity 0–100
 *   temp, min, max                                 → temperature (°C or °F)
 *   f                                              → png | jpg | bmp | raw
 *
 * Examples:
 *   ?sun=80&clouds=20&temp=22&min=15&max=25&f=png
 *   ?clouds=80&rain=60&thunder=70&temp=14&min=10&max=18&f=png
 *   ?clouds=100&snow=80&wind=40&temp=-3&min=-6&max=0&f=png
 *   ?fog=90&clouds=30&temp=9&min=7&max=13&f=png
 */

function nextube_image(
    float  $sun,
    float  $clouds,
    float  $rain,
    float  $temperature,
    float  $mintemp,
    float  $maxtemp,
    float  $fog     = 0,
    float  $snow    = 0,
    float  $thunder = 0,
    float  $wind    = 0,
    string $format  = 'png'
): void {
    $W = 80; $H = 160;
    $im = imagecreatetruecolor($W, $H);
    imagealphablending($im, true);
    imagesavealpha($im, true);

    // ── Helpers ─────────────────────────────────────────────────────────────
    // Arrow functions (PHP 7.4+) auto-capture $im from enclosing scope
    $C  = fn($r,$g,$b)       => imagecolorallocate($im, (int)$r, (int)$g, (int)$b);
    $CA = fn($r,$g,$b,$a)    => imagecolorallocatealpha($im, (int)$r, (int)$g, (int)$b, min(127,(int)$a));
    $EL = fn($cx,$cy,$rw,$rh,$col) => imagefilledellipse($im,(int)$cx,(int)$cy,(int)($rw*2),(int)($rh*2),$col);

    // ═══════════════════════════════════════════════════════════════════════
    // 1. BACKGROUND GRADIENT (condition-aware)
    // ═══════════════════════════════════════════════════════════════════════
    for ($y = 0; $y < $H; $y++) {
        $t = $y / $H;
        if ($thunder > 40)                     $col = $C(10+$t*8,   8+$t*10,  20+$t*20);
        elseif ($fog     > 50)                 $col = $C(16+$t*14, 16+$t*14,  20+$t*16);
        elseif ($snow    > 50)                 $col = $C( 8+$t*10, 10+$t*12,  20+$t*22);
        elseif ($sun > 60 && $clouds < 40)     $col = $C(20+$t*12, 12+$t* 8,   4+$t* 4);
        else                                   $col = $C( 8+$t*10, 10+$t*12,  18+$t*18);
        imageline($im, 0, $y, $W-1, $y, $col);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // 2. FOG / MIST BANDS  (drawn behind icons so they show through)
    // ═══════════════════════════════════════════════════════════════════════
    if ($fog > 0) {
        for ($b = 0; $b < 8; $b++) {
            $by    = 5 + $b * 9;
            // More fog → lower alpha value (less transparent = more visible)
            $alpha = (int)max(8, min(122, 127 - $fog * 0.86 + $b * 5));
            $dx    = ($b & 1) * 4;   // slight color variation between bands
            imagefilledrectangle($im, 0, $by, $W-1, $by+3,
                $CA(168+$dx, 174+$dx, 196+$dx, $alpha));
            // Thin bright edge for a layered mist look
            imageline($im, 0, $by, $W-1, $by,
                $CA(200+$dx, 205+$dx, 218+$dx, min(127, $alpha+18)));
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    // 3. SUN
    // ═══════════════════════════════════════════════════════════════════════
    if ($sun > 0) {
        $sx = ($clouds > 20) ? 22 : 40;
        $sy = ($clouds > 20) ? 26 : 34;
        $sr = max(5, min(14, (int)(4 + $sun * 0.10)));
        $si = $sun / 100;
        $sR = 255;
        $sG = (int)(210 - $si * 60);   // warmer (more orange) at high intensity
        $sB = (int)( 55 - $si * 40);

        // Glow rings: drawn outermost first (most transparent → least transparent)
        for ($rg = 5; $rg >= 1; $rg--) {
            $rr = $sr + $rg * 2 + 1;
            $EL($sx, $sy, $rr, $rr, $CA($sR, $sG, $sB, min(125, 88 + $rg * 7)));
        }
        // Main disc
        $sunCol = $C($sR, $sG, $sB);
        $EL($sx, $sy, $sr, $sr, $sunCol);
        // Bright top-left highlight for a 3D orb feel
        $EL($sx-1, $sy-2,
            max(2, (int)($sr * 0.40)), max(2, (int)($sr * 0.35)),
            $C(255, min(255,$sG+50), min(255,$sB+80)));
        // Rays (slightly rotated 22.5° so they don't land on axis corners)
        if ($sun > 25) {
            $rl = max(2, (int)(1 + $sun * 0.06));
            for ($i = 0; $i < 8; $i++) {
                $a  = deg2rad($i * 45 + 22.5);
                $x1 = (int)($sx + cos($a) * ($sr + 2));
                $y1 = (int)($sy + sin($a) * ($sr + 2));
                $x2 = (int)($sx + cos($a) * ($sr + 2 + $rl));
                $y2 = (int)($sy + sin($a) * ($sr + 2 + $rl));
                imageline($im, $x1, $y1, $x2, $y2, $sunCol);
            }
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    // 4. CLOUD  (3-layer bumpy top, flat bottom, storm-tinted if needed)
    //    Position is also used by rain / snow / lightning below.
    // ═══════════════════════════════════════════════════════════════════════
    $CX    = ($sun > 0) ? 52 : 40;          // cloud center X
    $CY    = ($sun > 0) ? 42 : 36;          // cloud center Y
    $CR    = max(5, min(15, (int)(4 + $clouds * 0.11)));  // cloud radius
    $dropY = (int)($CY + $CR * 0.55) + 4;   // Y where precipitation starts

    if ($clouds > 0) {
        if ($thunder > 30)                [$cR,$cG,$cB] = [100, 100, 125];
        elseif ($rain > 30 || $snow > 30) [$cR,$cG,$cB] = [138, 140, 165];
        else                              [$cR,$cG,$cB] = [185, 188, 212];

        $cBase = $C($cR,         $cG,         $cB       );
        $cMid  = $C($cR + 20,    $cG + 20,    $cB + 14  );
        $cTop  = $C($cR + 38,    $cG + 36,    $cB + 22  );
        $cShad = $C(max(0,$cR-22), max(0,$cG-22), max(0,$cB-20));

        // Shadow base rectangle
        imagefilledrectangle($im,
            (int)($CX - $CR*.87), (int)($CY - $CR*.22),
            (int)($CX + $CR*.87), (int)($CY + $CR*.60), $cShad);
        // Side bumps (base layer)
        $EL($CX - $CR*.62, $CY + $CR*.05, $CR*.72, $CR*.72, $cBase);
        $EL($CX + $CR*.52, $CY + $CR*.15, $CR*.65, $CR*.65, $cBase);
        // Mid bumps
        $EL($CX - $CR*.28, $CY - $CR*.20, $CR*.78, $CR*.78, $cMid);
        $EL($CX + $CR*.28, $CY - $CR*.10, $CR*.70, $CR*.70, $cMid);
        // Top center (tallest, brightest)
        $EL($CX,           $CY - $CR*.38, $CR*.82, $CR*.82, $cTop);
        // Flat-bottom fill (covers bump bottoms, keeps cloud base clean)
        imagefilledrectangle($im,
            (int)($CX - $CR*.82), (int)($CY + $CR*.05),
            (int)($CX + $CR*.82), (int)($CY + $CR*.55), $cBase);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // 5. LIGHTNING BOLT
    // ═══════════════════════════════════════════════════════════════════════
    if ($thunder > 0 && $clouds > 20) {
        $bx  = (int)($CX + 2);
        $by  = $dropY;
        // Classic zigzag: top → left-mid → right-mid → bottom-left
        $pts = [
            [$bx,      $by     ],
            [$bx - 4,  $by + 7 ],
            [$bx + 2,  $by + 7 ],
            [$bx - 4,  $by + 17],
        ];
        // Yellow-white glow halo for heavy thunder
        if ($thunder > 40) {
            $glowCol = $CA(255, 255, 145, 74);
            for ($i = 0; $i < 3; $i++) {
                foreach ([[-1,0],[1,0],[0,-1]] as [$dx,$dy]) {
                    imageline($im,
                        $pts[$i][0]+$dx, $pts[$i][1]+$dy,
                        $pts[$i+1][0]+$dx, $pts[$i+1][1]+$dy,
                        $glowCol);
                }
            }
        }
        // Bright bolt
        $boltCol = $C(255, 238, 50);
        for ($i = 0; $i < 3; $i++) {
            imageline($im, $pts[$i][0], $pts[$i][1], $pts[$i+1][0], $pts[$i+1][1], $boltCol);
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    // 6. RAIN DROPS
    // ═══════════════════════════════════════════════════════════════════════
    if ($rain > 0 && $clouds > 0) {
        $drops  = max(2, min(8, (int)($rain / 12)));
        $dLen   = max(3, min(8, (int)(2 + $rain * 0.055)));
        $slant  = ($wind > 40) ? 2 : 1;   // wind slants rain
        $dCol   = $C( 85, 158, 255);
        $dHigh  = $C(155, 210, 255);       // bright top pixel = teardrop glint
        $spread = (int)($CR * 1.6);
        for ($d = 0; $d < $drops; $d++) {
            $ox = (int)(($d / max(1,$drops-1)) * $spread * 2 - $spread);
            $ry = $dropY + ($d % 3) * 3;
            $rx = (int)$CX + $ox;
            if ($ry + $dLen < 79) {
                imageline($im, $rx, $ry, $rx - $slant, $ry + $dLen, $dCol);
                imagesetpixel($im, $rx, $ry, $dHigh);
            }
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    // 7. SNOWFLAKES
    // ═══════════════════════════════════════════════════════════════════════
    if ($snow > 0 && $clouds > 0) {
        $flakes  = max(3, min(13, (int)($snow / 8)));
        $sBright = $C(242, 248, 255);
        $sSoft   = $C(205, 228, 255);
        $big     = ($snow > 60);   // heavy snow → cross-shaped flakes
        // Fixed grid (offset from cloud center X, offset from $dropY)
        $grid = [
            [ 0, 0], [-6, 4], [ 6, 4], [-2, 9], [ 5, 9],
            [ 1,14], [-6,14], [ 6,15], [ 0,19], [-4,21],
            [ 5,21], [-2,25], [ 3,25],
        ];
        for ($f = 0; $f < min($flakes, count($grid)); $f++) {
            $fx = (int)($CX + $grid[$f][0]);
            $fy = $dropY + $grid[$f][1];
            if ($fy >= 79) continue;
            imagesetpixel($im, $fx, $fy, $sBright);
            if ($big) {
                foreach ([[-1,0],[1,0],[0,-1],[0,1]] as [$dx,$dy]) {
                    imagesetpixel($im, $fx+$dx, $fy+$dy, $sSoft);
                }
            }
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    // 8. WIND STREAKS
    // ═══════════════════════════════════════════════════════════════════════
    if ($wind > 25) {
        // More transparent at low wind, more opaque at high wind
        $wa   = (int)max(18, min(118, 127 - $wind * 0.92));
        $wCol = $CA(172, 188, 215, $wa);
        foreach ([[2,28,18,28],[5,38,24,38],[1,49,13,49]] as [$x1,$y1,$x2,$y2]) {
            imageline($im, $x1, $y1, $x2, $y2, $wCol);
            // Tapered tail (shorter, more transparent second line)
            imageline($im, $x1, $y1+1, (int)(($x1+$x2)*.6), $y1+1,
                $CA(172, 188, 215, max(28, $wa-18)));
        }
        if ($wind > 65) {
            imageline($im, 3, 58, 21, 58, $wCol);
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    // 9. DIVIDER LINE  (amber, two-pixel for subtle depth)
    // ═══════════════════════════════════════════════════════════════════════
    imageline($im, 6, 82, $W-6, 82, $CA(255, 185, 100,  94));
    imageline($im, 6, 83, $W-6, 83, $CA(255, 185, 100, 112));

    // ═══════════════════════════════════════════════════════════════════════
    // 10. TEMPERATURE TEXT
    //     Color-coded: cold=blue, cool=light-blue, warm=amber, hot=orange-red
    // ═══════════════════════════════════════════════════════════════════════
    if     ($temperature >= 30) [$tR,$tG,$tB] = [255, 112,  58];
    elseif ($temperature >= 20) [$tR,$tG,$tB] = [255, 186, 102];
    elseif ($temperature >= 10) [$tR,$tG,$tB] = [155, 202, 255];
    else                        [$tR,$tG,$tB] = [125, 180, 255];
    $tCol   = $C($tR, $tG, $tB);
    $dimCol = $C((int)($tR*.58), (int)($tG*.58), (int)($tB*.58));

//    $font = __DIR__ . '/Ubuntu-Bold.ttf';
    $font = __DIR__ . '/fonts/JetBrainsMono.woff2';
    if (file_exists($font)) {
        $deg   = "\xC2\xB0";       // UTF-8 °  (U+00B0)
        $ndash = "\xe2\x80\x93";   // UTF-8 –  (U+2013)

        $mainStr  = sprintf("%.1f%s", $temperature, $deg);
        $rangeStr = sprintf("%.0f%s%s%.0f%s", $mintemp, $deg, $ndash, $maxtemp, $deg);

        // Auto-shrink main temp if too wide (e.g. "-19.5°")
        $fs1 = 30;
        $b1  = imagettfbbox($fs1, 0, $font, $mainStr);
        while (($b1[2] - $b1[0]) > ($W - 2) && $fs1 > 15) {
            $fs1--;
            $b1 = imagettfbbox($fs1, 0, $font, $mainStr);
        }
        $tx1 = (int)(($W - ($b1[2] - $b1[0])) / 2) - $b1[0];
        imagettftext($im, $fs1, 0, $tx1, 115, $tCol, $font, $mainStr);

        // Auto-shrink range string
        $fs2 = 18;
        $b2  = imagettfbbox($fs2, 0, $font, $rangeStr);
        while (($b2[2] - $b2[0]) > ($W - 2) && $fs2 > 8) {
            $fs2--;
            $b2 = imagettfbbox($fs2, 0, $font, $rangeStr);
        }
        $tx2 = (int)(($W - ($b2[2] - $b2[0])) / 2) - $b2[0];
        imagettftext($im, $fs2, 0, $tx2, 138, $dimCol, $font, $rangeStr);

    } else {
        // Fallback: GD built-in font (no FreeType needed)
        $ms = sprintf("%.1f",      $temperature)          . chr(176);
        $rs = sprintf("%.0f/%.0f", $mintemp, $maxtemp)    . chr(176);
        imagestring($im, 4, (int)(($W - imagefontwidth(4)*strlen($ms))/2), 100, $ms, $tCol);
        imagestring($im, 2, (int)(($W - imagefontwidth(2)*strlen($rs))/2), 128, $rs, $dimCol);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // 11. OUTPUT
    // ═══════════════════════════════════════════════════════════════════════
    if ($format === 'jpg') {
        header('Content-Type: image/jpeg');
        imagejpeg($im, null, 95);
    } elseif ($format === 'bmp') {
        header('Content-Type: image/bmp');
        imagebmp($im);
    } elseif ($format === 'raw') {
        // RGB565 big-endian, pixel by pixel — for direct framebuffer/display use
        header('Content-Type: application/octet-stream');
        for ($y = 0; $y < $H; $y++) {
            for ($x = 0; $x < $W; $x++) {
                $col = imagecolorat($im, $x, $y);
                $rgb565 = ((($col>>16)&0xF8)<<8) | ((($col>>8)&0xFC)<<3) | (($col&0xFF)>>3);
                echo pack('n', $rgb565);
            }
        }
    } else {
        header('Content-Type: image/png');
        imagepng($im);
    }

    imagedestroy($im);
}

// ── URL dispatch ─────────────────────────────────────────────────────────────
function _gp(string $k, float $d): float {
    return isset($_GET[$k]) ? (float)$_GET[$k] : $d;
}

nextube_image(
    _gp('sun',     0),
    _gp('clouds',  0),
    _gp('rain',    0),
    _gp('temp',   20.0),
    _gp('min',    15.0),
    _gp('max',    25.0),
    _gp('fog',     0),
    _gp('snow',    0),
    _gp('thunder', 0),
    _gp('wind',    0),
    $_GET['f'] ?? 'png'
);