<?php
$n='kamer';
if (!isset($db)) $db=new PDO("mysql:host=localhost;dbname=$dbname;",$dbuser,$dbpass);
$result=$db->query("SELECT AVG(temp) as AVG FROM (SELECT $n as temp FROM `temp` ORDER BY `temp`.`stamp` DESC LIMIT 0,15) as A");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	$avg=$row['AVG'];
}
$diff=$status-$avg;
if ($d[$n.'_temp']['icon']!=$diff) {
	storeicon($n.'_temp', $diff, basename(__FILE__).':'.__LINE__);
}
$d[$n.'_temp']['s']=$status;
