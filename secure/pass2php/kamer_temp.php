<?php
$n='kamer';
$db = Database::getInstance();
$result=$db->query("SELECT AVG(temp) as AVG FROM (SELECT $n as temp FROM `temp` ORDER BY `temp`.`stamp` DESC LIMIT 0,15) as A");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	$avg=$row['AVG'];
}
$diff=round($st-$avg,4);
if ($d[$n.'_temp']->i!=$diff) {
	storeicon($n.'_temp', $diff, basename(__FILE__).':'.__LINE__);
}
$d[$n.'_temp']->s=$st;
