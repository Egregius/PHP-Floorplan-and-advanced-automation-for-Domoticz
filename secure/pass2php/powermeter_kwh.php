<?php
/*$today=date("Y-m-d");

$dbf=new PDO("mysql:host=192.168.2.20;dbname=fuel;charset=utf8", 'fuel', 'pmHIaRmBaCaP61qR');
$dbf->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmtf=$dbf->prepare("INSERT INTO `powermeter` (stamp,kWh) VALUES (:stamp,:kWh) ON DUPLICATE KEY Update kWh=:kWh;");
$opts=array(':stamp'=>$today,':kWh'=>$status);
$stmtf->execute($opts);
$stmtf=$dbf->query("SELECT kWh FROM powermeter ORDER BY stamp DESC LIMIT 0,2;");
$kwh=array();
while ($row=$stmtf->fetch(PDO::FETCH_ASSOC)) $kwh[]=$row['kWh'];

$vandaag=$kwh[0]-$kwh[1];
if ($vandaag>0) {
	$stmtf=$dbf->prepare("INSERT INTO `elec` (idcar,stamp,kWh) VALUES (:idcar,:stamp,:kWh) ON DUPLICATE KEY Update kWh=:kWh;");
	$opts=array(':idcar'=>2,':stamp'=>$today,':kWh'=>$vandaag);
	$stmtf->execute($opts);
}
*/