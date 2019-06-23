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
$n='alex';
$result=$db->query("SELECT AVG(temp) as AVG FROM (SELECT $n as temp FROM `temp` ORDER BY `temp`.`stamp` DESC LIMIT 0,30) as A");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	$avg=$row['AVG'];
}
if ($status>=$avg+0.5) {
	storeicon($n.'_temp', 'red5');
} elseif ($status>=$avg+0.4) {
    storeicon($n.'_temp', 'red4');
} elseif ($status>=$avg+0.3) {
    storeicon($n.'_temp', 'red3');
} elseif ($status>=$avg+0.2) {
    storeicon($n.'_temp', 'red');
} elseif ($status>=$avg+0.1) {
    storeicon($n.'_temp', 'up');
} elseif ($status<=$avg-0.5) {
    storeicon($n.'_temp', 'blue5');
} elseif ($status<=$avg-0.4) {
    storeicon($n.'_temp', 'blue4');
} elseif ($status<=$avg-0.3) {
    storeicon($n.'_temp', 'blue3');
} elseif ($status<=$avg-0.2) {
    storeicon($n.'_temp', 'blue');
} elseif ($status<=$avg-0.1) {
    storeicon($n.'_temp', 'down');
} else {
	storeicon($n.'_temp', '');
}
$d[$n.'_temp']['s']=$status;