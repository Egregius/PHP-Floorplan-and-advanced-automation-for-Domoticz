<?php
require('phpMQTT.php');
$server = '192.168.2.28';     // change if necessary
$port = 1883;                     // change if necessary
$username = '';                   // set your username
$password = '';                   // set your password
$client_id = 'phpMQTT-subscriber'; // make sure this is unique for connecting to sever - you could use uniqid()

$mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

if ($mqtt->connect(true, NULL, $username, $password)) {
	$mqtt->publish('domoticz/test', 'Hello World! at ' . date('r'), 0, false);
	$mqtt->close();
} else {
    echo "Time out!\n";
}
