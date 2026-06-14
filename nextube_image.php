<?php
function nextube_image($sun, $clouds, $rain, $temperature, $mintemp, $maxtemp, $format = 'png') {
    $w = 80; $h = 160; $im = imagecreatetruecolor($w, $h);
    $bg = imagecolorallocate($im, 0, 0, 0); imagefill($im, 0, 0, $bg);
    $base_r = 255; $base_g = 186; $base_b = 102;
    if ($sun > 0) {
        $green = max(60, min(220, (int)(220 - ($sun * 1.6))));
        $blue = max(20, min(120, (int)(120 - ($sun * 1.0))));
        $sun_color = imagecolorallocate($im, 255, $green, $blue);
        $sx = 28; $sy = 32; $sr = max(3, min(15, (int)(2 + ($sun * 0.13))));
        imagefilledellipse($im, $sx, $sy, $sr * 2, $sr * 2, $sun_color);
        if ($sr > 4) {
            $rays = 8; $l1 = $sr + 2; $l2 = $sr + max(3, min(8, (int)($sun * 0.08)));
            for ($i = 0; $i < 360; $i += (360 / $rays)) {
                $r = deg2rad($i);
                imageline($im, (int)($sx + cos($r) * $l1), (int)($sy + sin($r) * $l1), (int)($sx + cos($r) * $l2), (int)($sy + sin($r) * $l2), $sun_color);
            }
        }
    }
    if ($clouds > 0) {
        $cloud_color = imagecolorallocate($im, min(255, $base_r + 20), min(255, $base_g + 35), min(255, $base_b + 60));
        $cx = ($sun > 0) ? 52 : 40; $cy = ($sun > 0) ? 48 : 38; 
        $cr = max(3, min(14, (int)(2 + ($clouds * 0.12))));
        imagefilledellipse($im, $cx, $cy, $cr * 2, $cr * 2, $cloud_color);
        if ($cr > 4) {
            imagefilledellipse($im, (int)($cx - ($cr * 0.7)), (int)($cy + ($cr * 0.2)), (int)($cr * 1.3), (int)($cr * 1.3), $cloud_color);
            imagefilledellipse($im, (int)($cx + ($cr * 0.6)), (int)($cy + ($cr * 0.3)), (int)($cr * 1.1), (int)($cr * 1.1), $cloud_color);
            imagefilledrectangle($im, (int)($cx - ($cr * 0.7)), $cy, (int)($cx + ($cr * 0.6)), (int)($cy + ($cr * 0.9)), $cloud_color);
        }
        if ($rain > 0 && $cr > 4) {
            $rain_color = imagecolorallocate($im, 100, 180, 255);
            $drop_count = max(2, min(6, (int)round($rain / 15)));
            $drop_length = max(3, min(8, (int)round($rain / 12)));
            $start_y = $cy + $cr + 2;
            for ($v = 0; $v < $drop_count; $v++) {
                $rx = (int)(($cx - $cr) + ($v * (($cr * 2) / max(1, $drop_count - 1))));
                $ry = (int)($start_y + (($v % 2) * 3));
                imageline($im, $rx, $ry, $rx - 1, $ry + $drop_length, $rain_color);
            }
        }
    }
    $text_color = imagecolorallocate($im, $base_r, $base_g, $base_b);
    $font_path = __DIR__ . '/Ubuntu-Bold.ttf';
    $t1 = sprintf("%.1f", $temperature);
    if (file_exists($font_path)) {
        $fsize1 = 30;
        $box1 = imagettfbbox($fsize1, 0, $font_path, $t1);
        $tx1 = (int)(($w - ($box1[2] - $box1[0])) / 2);
        imagettftext($im, $fsize1, 0, $tx1, 110, $text_color, $font_path, $t1);
        $t2 = sprintf("%.1f - %.1f", $mintemp, $maxtemp);
        $fsize2 = 12;
        $box2 = imagettfbbox($fsize2, 0, $font_path, $t2);
        $tx2 = (int)(($w - ($box2[2] - $box2[0])) / 2);
        imagettftext($im, $fsize2, 0, $tx2, 138, $text_color, $font_path, $t2);
    } else {
        $t1_fallback = sprintf("%.1f", $temperature) . chr(176);
        $t2_fallback = sprintf("%.0f/%.0f", $mintemp, $maxtemp) . chr(176);
        imagestring($im, 4, (int)(($w - imagefontwidth(4) * strlen($t1_fallback)) / 2), 100, $t1_fallback, $text_color);
        imagestring($im, 2, (int)(($w - imagefontwidth(2) * strlen($t2_fallback)) / 2), 128, $t2_fallback, $text_color);
    }
    if ($format === 'jpg') { header('Content-Type: image/jpeg'); imagejpeg($im, null, 95); }
    elseif ($format === 'png') { header('Content-Type: image/png'); imagepng($im); }
    elseif ($format === 'bmp') { header('Content-Type: image/bmp'); imagebmp($im); }
    elseif ($format === 'raw') {
        header('Content-Type: application/octet-stream');
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $c = imagecolorat($im, $x, $y);
                $rgb565 = ((($c >> 16) & 0xF8) << 8) | ((($c >> 8) & 0xFC) << 3) | (($c & 0xFF) >> 3);
                echo pack('n', $rgb565);
            }
        }
    }
    imagedestroy($im);
}
if (isset($_GET['f'])) {
    nextube_image(90, 60, 45, 19.1, 12.4, 22.8, $_GET['f']);
} else {
    nextube_image(90, 60, 45, 19.1, 12.4, 22.8, 'png');
}
?>