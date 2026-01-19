<?php
$n='living';
if ($status>$d[$n.'_temp']->s+2) $status=$d[$n.'_temp']->s+2;
elseif ($status<$d[$n.'_temp']->s-2) $status=$d[$n.'_temp']->s-2;
$db = Database::getInstance();
$stamp=date('Y-m-d H:i:s', $time-600);
$sql="SELECT AVG(".$n.") AS AVG FROM `temp` WHERE stamp>='$stamp'";
$result=$db->query($sql);
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	$avg=$row['AVG'];
}
$diff=round($status-$avg,4);
if ($d[$n.'_temp']['icon']!=$diff) {
	storeicon($n.'_temp', $diff, basename(__FILE__).':'.__LINE__);
}
lgtype('trend_living','temp='.$status.'	trend='.$diff.'	thermometer');
