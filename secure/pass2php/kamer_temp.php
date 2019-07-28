<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
$n='kamer';
$result=$db->query("SELECT AVG(temp) as AVG FROM (SELECT $n as temp FROM `temp` ORDER BY `temp`.`stamp` DESC LIMIT 0,30) as A");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	$avg=$row['AVG'];
}
if ($status>=$avg+0.5) {
	if ($d[$n.'_temp']['icon']!='red5') {
		storeicon($n.'_temp', 'red5', basename(__FILE__).':'.__LINE__);
	}
} elseif ($status>=$avg+0.4) {
    if ($d[$n.'_temp']['icon']!='red4') {
    	storeicon($n.'_temp', 'red4', basename(__FILE__).':'.__LINE__);
    }
} elseif ($status>=$avg+0.3) {
    if ($d[$n.'_temp']['icon']!='red3') {
    	storeicon($n.'_temp', 'red3', basename(__FILE__).':'.__LINE__);
    }
} elseif ($status>=$avg+0.2) {
    if ($d[$n.'_temp']['icon']!='red') {
    	storeicon($n.'_temp', 'red', basename(__FILE__).':'.__LINE__);
    }
} elseif ($status>=$avg+0.1) {
    if ($d[$n.'_temp']['icon']!='up') {
    	storeicon($n.'_temp', 'up', basename(__FILE__).':'.__LINE__);
    }
} elseif ($status<=$avg-0.5) {
    if ($d[$n.'_temp']['icon']!='blue5') {
    	storeicon($n.'_temp', 'blue5', basename(__FILE__).':'.__LINE__);
    }
} elseif ($status<=$avg-0.4) {
    if ($d[$n.'_temp']['icon']!='blue4') {
    	storeicon($n.'_temp', 'blue4', basename(__FILE__).':'.__LINE__);
    }
} elseif ($status<=$avg-0.3) {
    if ($d[$n.'_temp']['icon']!='blue3') {
    	storeicon($n.'_temp', 'blue3', basename(__FILE__).':'.__LINE__);
    }
} elseif ($status<=$avg-0.2) {
    if ($d[$n.'_temp']['icon']!='blue') {
    	storeicon($n.'_temp', 'blue', basename(__FILE__).':'.__LINE__);
    }
} elseif ($status<=$avg-0.1) {
    if ($d[$n.'_temp']['icon']!='down') {
    	storeicon($n.'_temp', 'down', basename(__FILE__).':'.__LINE__);
    }
} else {
	if ($d[$n.'_temp']['icon']!='') {
		storeicon($n.'_temp', '', basename(__FILE__).':'.__LINE__);
	}
}
$d[$n.'_temp']['s']=$status;