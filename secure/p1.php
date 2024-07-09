<?php

$fp = fopen("/dev/ttyUSB1", "w+");
$data=fread($fp, 1024);

$data=explode("\n", $data);
print_r($data);
foreach ($data as $i) {
	echo $i.PHP_EOL;
	if (getStringBetween($i, ":", "(")=="1.8.1") echo $i;
}


function getStringBetween($str,$from,$to)
{
    $sub = substr($str, strpos($str,$from)+strlen($from),strlen($str));
    return substr($sub,0,strpos($sub,$to));
}