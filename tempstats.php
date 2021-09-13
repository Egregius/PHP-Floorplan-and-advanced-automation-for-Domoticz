<?php
/**
 * Pass2PHP
 * php version 7.3.11-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
require 'secure/functions.php';
require '/var/www/authentication.php';
require 'scripts/chart.php';
$items=array('buiten', 'living', 'kamer', 'speelkamer', 'alex');
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta name="HandheldFriendly" content="true"/>
		<meta name="viewport" content="width=device-width,height=device-height, user-scalable=yes, minimal-ui"/>
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<title>Temperatuur Statestieken</title>
		<link href="/styles/temp.php" rel="stylesheet" type="text/css"/>
		<script type="text/javascript">function navigator_Go(url) {window.location.assign(url);}</script>
		<style>
			table{border:2px solid #CCC;border-spacing:0px;}
			th{border:1px solid #999;}
			td{text-align:right;border:1px solid #555;min-width:80px;padding-right:5px;}
			.btn{padding:8px 3px 8px 3px;}
			input[type=checkbox]{position:absolute;left:-9999px;}
			label{margin:0px;padding:14px 3px 14px 3px;border:0px solid #fff;color:#fff;background-color:#555;cursor:pointer;user-select:none;text-align:center}
			input:checked + label{background-color:#ffba00;color:#000;}

			.kamer{display: inline-block;width:144px;max-width:19.9%}
			.uur{display: inline-block;width:21.5px;max-width:4,166666667%}
			.even{background:#333;}
			.borderleft1{border-left:1px solid #AAA;}
			.borderleft2{border-left:1px solid #999;}
			.borderright1{border-left:1px solid #AAA;}
			.borderright2{border-left:1px solid #999;}
		</style>
		</head>
		<body style="width:100%">
			<a href="https://home.egregius.be/tempstats.php?buiten=on&living=on&10=on&11=on&12=on&13=on&14=on&15=on&16=on&17=on&18=on&19=on&20=on&21=on" class="btn b7">Living</a>
			<a href="https://home.egregius.be/tempstats.php?kamer=on&0=on&1=on&2=on&3=on&4=on&5=on&6=on&22=on&23=on" class="btn b7">Kamer</a>
			<a href="https://home.egregius.be/tempstats.php?speelkamer=on&0=on&1=on&2=on&3=on&4=on&5=on&6=on&22=on&23=on" class="btn b7">speelkamer</a>
			<a href="https://home.egregius.be/tempstats.php?alex=on&0=on&1=on&2=on&3=on&4=on&5=on&6=on&20=on&21=on&22=on&23=on" class="btn b7">Alex</a>
			<br>
			<form method="GET">';
foreach ($items as $i) {
	if(isset($_REQUEST[$i])) echo '
				<input type="checkbox" name="'.$i.'" id="'.$i.'" onChange="this.form.submit()" checked>
				<label for="'.$i.'" class="kamer b5" >'.$i.'</label>';
	else echo '
				<input type="checkbox" name="'.$i.'" id="'.$i.'" onChange="this.form.submit()">
				<label for="'.$i.'" class="kamer b5" >'.$i.'</label>';

	if (isset($_REQUEST[$i])) $kamers[]=$i;
}
if (!isset($kamers)) $kamers=array('buiten');
echo '<br><br>';
for ($i=0;$i<=23;$i++) {
	if(isset($_REQUEST[$i])) echo '
				<input type="checkbox" name="'.$i.'" id="'.$i.'" onChange="this.form.submit()" checked>
				<label for="'.$i.'" class="uur">'.$i.'</label>';
	else echo '
				<input type="checkbox" name="'.$i.'" id="'.$i.'" onChange="this.form.submit()">
				<label for="'.$i.'" class="uur">'.$i.'</label>';
	if (isset($_REQUEST[$i])) $hours[$i]=$i;
}
if (!isset($hours)) {
	for ($x=0;$x<=23;$x++) {
		$hours[$x]=$x;
	}
}
//print_r($hours);
echo '
			</form>
			<br>
			<br>';
$db=new mysqli('localhost', $dbuser, $dbpass, $dbname);
if ($db->connect_errno>0) die('Unable to connect to database ['.$db->connect_error.']');
$query="SELECT stamp";
foreach ($kamers as $i) $query.=", ".$i."_avg as $i";
$query.=" FROM temp_hour WHERE stamp like '2019-05%' OR stamp like '2019-06%' OR stamp like '2019-07%' OR stamp like '2019-08%' OR stamp like '2019-09%'";
$result=$db->query($query);
while ($row=$result->fetch_assoc()) $datas[]=$row;

//print_r($datas);
foreach ($kamers as $i) {
	for ($x=40;$x>=-20;$x--) ${$i.$x}=0;
}
foreach ($datas as $a) {
	$hour=substr($a['stamp'], 11, 2) * 1;
	$day=substr($a['stamp'], 0, 10);
	if (in_array($hour, $hours)) {
		foreach ($kamers as $i) {
			for ($x=40;$x>=-20;$x--) {
				if ($a[$i]>=$x) {
					${$i.$x}++;
					@$dag[$i.$x][$day]=true;
				}
			}
		}
		//echo $a['stamp'].' = '.$hour.'<br>';
		//print_r($a);
	}
}
//unset ($datas);echo '<pre>';print_r(GET_DEFINED_VARS());echo '</pre>';exit;
echo '
Statestieken mei -> september 2019
<table>
	<thead>
		<tr>
			<th></th>';
foreach ($kamers as $i) echo '
			<th colspan="4">'.ucfirst($i).'</th>';
echo '
		</tr>
		<tr>
			<th></th>';
foreach ($kamers as $i) echo '
			<th colspan="2">Dagen</th><th colspan="2">Uren</th>';
echo '
		</tr>
		<tr>
			<th>Temp</th>';
foreach ($kamers as $i) echo '
			<th>Aantal</th><th>Percent</th><th>Aantal</th><th>Percent</th>';
echo '
		</tr>
	</thead>
	<tbody>';
$aantaluren=0;
foreach ($kamers as $i) {
	if (${$i.'-20'}>$aantaluren) $aantaluren = ${$i.'-20'};
}
$aantaldagen=0;
foreach ($kamers as $i) {
	if (count($dag[$i.'-20'])>$aantaldagen) $aantaldagen = count($dag[$i.'-20']);
}
if (isset($_REQUEST['buiten'])) $start=40; else $start=26;
for ($x=$start;$x>=18;$x--) {
	if ($x%2==0) echo '
		<tr class="even">';
	else  echo '
		<tr>';
	echo '
			<td class="borderright1">'.$x.' °C </td>';
	foreach ($kamers as $i) {
		isset($dag[$i.$x])?$aantal=count($dag[$i.$x]):$aantal=0;
		echo '
			<td class="borderleft1">'.$aantal.'</td>
			<td class="borderright2">'.number_format(($aantal/$aantaldagen)*100, 2, ',', '').' %</td>
			<td class="borderleft2">'.${$i.$x}.'</td>
			<td class="borderright2">'.number_format((${$i.$x}/$aantaluren)*100, 2, ',', '').' %</td>';
	}
	echo '
		</tr>';
}
echo '
	</tbody>
</table>';
$db->close();
