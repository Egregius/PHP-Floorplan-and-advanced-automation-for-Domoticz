<?php
/**
 * Pass2PHP Control roller while on vacation
 * php version 7.3
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
$db=dbconnect();
$stmt=$db->query("select name, up, down from vacation;");
while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) $r[$row['name']] = $row;
foreach ($r as $k=>$v) {
	lg(print_r($v, true));
	if ($v['up']<($d['civil_twilight']['s']-64800)) {
		$up=$d['civil_twilight']['s']+rand(0, 1800);
		$db->query("UPDATE vacation SET up = '$up' WHERE name = '$k';");
	}
	if ($v['down']<($d['civil_twilight']['m']-64800)) {
		$down=$d['civil_twilight']['m']-rand(0, 1800);
		$db->query("UPDATE vacation SET down = '$down' WHERE name = '$k';");
	}
}
