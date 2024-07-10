<?php

	$fp = fopen("/dev/ttyUSB1", "w+");
	$data=fread($fp, 1024);
	
	$data=explode("\n", $data);
	//print_r($data);
	foreach ($data as $i) {
		$c=string_between_two_string($i, ":", "(");
		if ($c=="1.8.1") {
			$w=1*substr($i,10,-6);
			$elec=$w;
			echo 'Elec dag :	'.$w.PHP_EOL;
		} elseif ($c=="1.8.2") {
			$w=1*substr($i,10,-6);
			$elec+=$w;
			echo 'Elec nacht :	'.$w.PHP_EOL;
//		} elseif ($c=="2.8.1") {
//			echo 'Injectie dag :		'. 1*substr($i,10,-6).PHP_EOL;
//		} elseif ($c=="2.8.2") {
//			echo 'Injectie nacht :	'. 1*substr($i,10,-6).PHP_EOL;
		} elseif ($c=="24.2.1") {
			$w=1*substr($i,26,-5);
			$water=$w;
			echo 'Water :		'.$w.PHP_EOL;
		} elseif ($c=="24.2.3") {
			$w=1*substr($i,26,-5);
			$gas=$w;
			echo 'Gas :		'.$w.PHP_EOL;
		}
	}
	
	echo 'Elec totaal :	'.$elec.PHP_EOL;

if (isset($elec,$gas,$water)) {
	require '/var/www/config.php';
	@file_get_contents($vurl."verbruik=$elec&gas=$gas&water=$water");
	
}

function string_between_two_string($str, $starting_word, $ending_word) {
    $subtring_start = strpos($str, $starting_word);
    $subtring_start += strlen($starting_word); 
    $size = strpos($str, $ending_word, $subtring_start) - $subtring_start; 
    return substr($str, $subtring_start, $size); 
}


