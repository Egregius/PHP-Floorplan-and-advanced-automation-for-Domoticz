<?php
/**
 * Pass2PHP 
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
//lg(__FILE__.':'.$s);
$user='cron120';

$items=array('buiten', 'living', 'badkamer', 'kamer', 'tobi', 'alex', 'zolder');

$stamp=strftime("%F %T", TIME-900);
echo $stamp.'<br>';
$sql="SELECT AVG(buiten) AS buiten, AVG(living) AS living, AVG(badkamer) AS badkamer, AVG(kamer) AS kamer, AVG(tobi) AS tobi, AVG(alex) AS alex, AVG(zolder) AS zolder FROM `temp` WHERE stamp>='$stamp'";
$db=dbconnect();
$result=$db->query($sql);
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	$avg=$row;
}
foreach ($items as $i) {
	$diff=$d[$i.'_temp']['s']-$avg[$i];
	echo $i.' = '.$diff.'<br>';
	if ($d[$i.'_temp']['icon']!=$diff) {
		storeicon($i.'_temp', $diff, basename(__FILE__).':'.__LINE__);
	}
	if ($d[$i.'_temp']['m']==1&&past($i.'_temp')>21600) {
		storemode($i.'_temp', 0, basename(__FILE__).':'.__LINE__);
	}
}