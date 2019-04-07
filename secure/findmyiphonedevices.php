<?PHP
error_reporting(E_ALL);ini_set("display_errors", "on");
header("Content-type: text/html; charset=utf-8");
require '/var/www/config.php';
require_once "settings.php";
require_once "functions.php";
require_once "findmyiphone.php";

try {
    $fmi = new FindMyiPhone($appleid, $applepass);
} catch (Exception $e) {
    print "Error: ".$e->getMessage();
    exit;
}
$fmi->printDevices();
