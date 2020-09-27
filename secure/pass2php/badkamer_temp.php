<?php
/**
 * Pass2PHP
 * php version 7.3
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
$n='badkamer';
if ($status>$d[$n.'_temp']['s']+0.2) $status=$d[$n.'_temp']['s']+0.2;
elseif ($status<$d[$n.'_temp']['s']-0.2) $status=$d[$n.'_temp']['s']-0.2;
$db=new PDO("mysql:host=localhost;dbname=$dbname;",$dbuser,$dbpass);
$result=$db->query("SELECT AVG(temp) as AVG FROM (SELECT $n as temp FROM `temp` ORDER BY `temp`.`stamp` DESC LIMIT 0,15) as A");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	$avg=$row['AVG'];
}
$diff=$status-$avg;
if ($d[$n.'_temp']['icon']!=$diff) {
	storeicon($n.'_temp', $diff, basename(__FILE__).':'.__LINE__);
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