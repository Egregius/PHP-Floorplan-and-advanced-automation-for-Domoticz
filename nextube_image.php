<?php
$w=80;$h=160;$im=imagecreatetruecolor($w,$h);$bg=imagecolorallocate($im,0,0,0);imagefill($im,0,0,$bg);$fg=imagecolorallocate($im,255,186,102);
imagefilledellipse($im,32,45,18,18,$fg);for($i=0;$i<360;$i+=45){$rad=deg2rad($i);$x1=32+cos($rad)*12;$y1=45+sin($rad)*12;$x2=32+cos($rad)*18;$y2=45+sin($rad)*18;imageline($im,$x1,$y1,$x2,$y2,$fg);}
imagefilledellipse($im,44,52,16,16,$fg);imagefilledellipse($im,54,52,14,14,$fg);imagefilledellipse($im,40,60,14,14,$fg);imagefilledellipse($im,50,60,14,14,$fg);imagefilledrectangle($im,40,54,54,67,$fg);
$txt="19.1".chr(176);$f=5;
imagestring($im,$f,($w-imagefontwidth($f)*strlen($txt))/2,95,$txt,$fg);
if(isset($_GET['f'])){$f=$_GET['f'];}else{$f='png';}
if($f==='jpg'){header('Content-Type: image/jpeg');imagejpeg($im,null,95);}
elseif($f==='png'){header('Content-Type: image/png');imagepng($im);}
elseif($f==='bmp'){header('Content-Type: image/bmp');imagebmp($im);}
elseif($f==='raw'){header('Content-Type: application/octet-stream');for($y=0;$y<$h;$y++){for($x=0;$x<$w;$x++){$c=imagecolorat($im,$x,$y);$r=($c>>16)&0xFF;$g=($c>>8)&0xFF;$b=$c&0xFF;$rgb565=(($r & 0xF8)<<8)|(($g & 0xFC)<<3)|($b>>3);echo pack('n',$rgb565);}}}
imagedestroy($im);
?>