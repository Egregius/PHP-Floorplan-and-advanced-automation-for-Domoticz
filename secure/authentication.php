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
session_name("__Secure-Session-Egregiushome");
session_set_cookie_params(['lifetime' => 0,'path' => '/','domain' =>'.egregius.be','secure' => true,'httponly' => true,'samesite' => 'Lax']);
session_start();
$_SESSION['redirect']='https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
if (!isset($_SESSION['User'])) {
	if (!isset($_COOKIE['__Secure-Egregius'])) {
		header("Location: https://login.egregius.be");
		header('HTTP/1.1 301 Redirect');
		die("Redirecting to: https://login.egregius.be");
	} else {
		$_SESSION['User']=json_decode(file_get_contents('https://login.egregius.be/remote.php?cookie='.$_COOKIE['__Secure-Egregius']), true);
	}
}
if($_SESSION['User']['allowedonlyonlan']==1) {
	if (substr($_SERVER['HTTP_X_FORWARDED_FOR'], 0, 10 )!='192.168.2.'&&substr($_SERVER['HTTP_X_FORWARDED_FOR'], 0, 10 )!='192.168.4.') die('Only allowed when on LAN');
}
if($_SESSION['User']['home']!=1) die('Not allowed');

$authenticated=true;
$home=true;
