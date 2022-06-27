<?php
/**
 * Pass2PHP
 * php version 7.3
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
require '/var/www/html/secure/functions.php';
$ctx=stream_context_create(array('http'=>array('timeout' =>5)));
$smappee=@json_decode(@file_get_contents('http://192.168.2.15/gateway/apipublic/reportInstantaneousValues', false, $ctx), true);
if (isset($smappee['report'])&&!empty($smappee['report'])) {
	preg_match_all("/ activePower=(\\d*.\\d*)/",$smappee['report'],$matches);
	if (!empty($matches[1][1])) {
		$db=new PDO("mysql:host=localhost;dbname=$dbname;",$dbuser,$dbpass);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$newzon=round($matches[1][1], 0);
		if ($newzon<0) $newzon=0;
		if ($newzon==0 ) $db->query("UPDATE devices SET s='$newzon' WHERE n='zon';") or trigger_error($db->error);
		else $db->query("UPDATE devices SET s='$newzon',t='".TIME."' WHERE n='zon';") or trigger_error($db->error);

		if (!empty($matches[1][2])) {
			$consumption=round($matches[1][2], 0);
			$db->query("UPDATE devices SET s='$consumption',t='".TIME."' WHERE n='el';") or trigger_error($db->error);
			if ($consumption-$newzon>8500) alert('Power', 'Power usage: '.$consumption-$newzon.' W!', 600, false);
		}
		$db=null;
	}
} else {
	if (shell_exec('curl -H "Content-Type: application/json" -X POST -d "" http://192.168.2.15/gateway/apipublic/logon')!='{"success":"Logon successful!","header":"Logon to the monitor portal successful..."}') {
		exit;
	}
}
