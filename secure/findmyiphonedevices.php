<?PHP
header("Content-type: text/html; charset=utf-8");
require 'functions.php';
require 'authentication.php';
require 'findmyiphone.php';

try {
    $fmi = new FindMyiPhone($appleid, $applepass);
} catch (Exception $e) {
    print "Error: ".$e->getMessage();
    exit;
}
$fmi->printDevices();