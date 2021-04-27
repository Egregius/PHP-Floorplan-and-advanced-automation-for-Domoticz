<?php
/**
 * Pass2PHP
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
$n='badkamer';
if ($status>$d[$n.'_temp']['s']+0.2) $status=$d[$n.'_temp']['s']+0.2;
elseif ($status<$d[$n.'_temp']['s']-0.2) $status=$d[$n.'_temp']['s']-0.2;
if (!isset($db)) $db=new PDO("mysql:host=localhost;dbname=$dbname;",$dbuser,$dbpass);
$result=$db->query("SELECT AVG(temp) as AVG FROM (SELECT $n as temp FROM `temp` ORDER BY `temp`.`stamp` DESC LIMIT 0,15) as A");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	$avg=$row['AVG'];
}
$diff=$status-$avg;
if ($d[$n.'_temp']['icon']!=$diff) {
	storeicon($n.'_temp', $diff, basename(__FILE__).':'.__LINE__);
}

if ($status>$d[$n.'_temp']['s']&&$status>$d[$n.'_set']['s']&&past('badkamervuur1')>900) {
	sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
	sw('badkamervuur1', 'Off', basename(__FILE__).':'.__LINE__);
} elseif ($status<$d[$n.'_temp']['s']&&$status<$d[$n.'_set']['s']&&past('badkamervuur1')>900) {
	sw('badkamervuur1', 'On', basename(__FILE__).':'.__LINE__);
}
if ($status>$d[$n.'_temp']['s']&&$d[$n.'_set']['s']&&$d['badkamervuur1']['s']=='Off'&&past('badkamervuur1')>900) RefreshZwave(104);
