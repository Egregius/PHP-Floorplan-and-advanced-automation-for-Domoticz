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
$n='badkamer';
$result=$db->query("SELECT AVG(temp) as AVG FROM (SELECT $n as temp FROM `temp` ORDER BY `temp`.`stamp` DESC LIMIT 0,30) as A");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	$avg=$row['AVG'];
}
if ($status>=$avg+0.5) {
	storeicon($n.'_temp', 'red5', basename(__FILE__).':'.__LINE__);
} elseif ($status>=$avg+0.4) {
    storeicon($n.'_temp', 'red4', basename(__FILE__).':'.__LINE__);
} elseif ($status>=$avg+0.3) {
    storeicon($n.'_temp', 'red3', basename(__FILE__).':'.__LINE__);
} elseif ($status>=$avg+0.2) {
    storeicon($n.'_temp', 'red', basename(__FILE__).':'.__LINE__);
} elseif ($status>=$avg+0.1) {
    storeicon($n.'_temp', 'up', basename(__FILE__).':'.__LINE__);
} elseif ($status<=$avg-0.5) {
    storeicon($n.'_temp', 'blue5', basename(__FILE__).':'.__LINE__);
} elseif ($status<=$avg-0.4) {
    storeicon($n.'_temp', 'blue4', basename(__FILE__).':'.__LINE__);
} elseif ($status<=$avg-0.3) {
    storeicon($n.'_temp', 'blue3', basename(__FILE__).':'.__LINE__);
} elseif ($status<=$avg-0.2) {
    storeicon($n.'_temp', 'blue', basename(__FILE__).':'.__LINE__);
} elseif ($status<=$avg-0.1) {
    storeicon($n.'_temp', 'down', basename(__FILE__).':'.__LINE__);
} else {
	storeicon($n.'_temp', '', basename(__FILE__).':'.__LINE__);
}
$prev=$d[$n.'_temp']['s'];
$set=$d[$n.'_set']['s'];
$tbadkamervuur=$d['badkamervuur1']['t'];
if ($status>$prev&&$status>$set&&$tbadkamervuur<time-600) {
    sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
    sw('badkamervuur1', 'Off', basename(__FILE__).':'.__LINE__);
} elseif ($status<$prev&&$status<$set&&$tbadkamervuur<time-600) {
    sw('badkamervuur1', 'On', basename(__FILE__).':'.__LINE__);
} else {
    include '_verwarming.php';
}