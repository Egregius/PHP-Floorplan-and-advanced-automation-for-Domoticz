<?php
$user=basename(__FILE__);
//lg($user);
$stamp=date('Y-m-d H:i:s', $time-900);
$sql="SELECT AVG(buiten) AS buiten, AVG(living) AS living, AVG(badkamer) AS badkamer, AVG(kamer) AS kamer, AVG(waskamer) AS waskamer, AVG(alex) AS alex, AVG(zolder) AS zolder FROM `temp` WHERE stamp>='$stamp'";
$result=$db->query($sql);
while ($row = $result->fetch(PDO::FETCH_ASSOC)) $avg=$row;
foreach (array('buiten', 'living', 'badkamer', 'kamer', 'waskamer', 'alex', 'zolder') as $i) {
	$diff=$d[$i.'_temp']['s']-$avg[$i];
	if ($d[$i.'_temp']['icon']!=$diff) storeicon($i.'_temp', $diff, basename(__FILE__).':'.__LINE__);
//	if ($d[$i.'_temp']['m']==1&&past($i.'_temp')>21600) storemode($i.'_temp', 0, basename(__FILE__).':'.__LINE__);
}
