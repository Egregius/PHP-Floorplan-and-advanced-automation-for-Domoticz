#!/usr/bin/php
<?php
require '/var/www/html/secure/functions.php';
while (1){
	$time=time();
	if ($time%10==0) echo '10'.PHP_EOL;
	if ($time%20==0) echo '	20'.PHP_EOL;
	if ($time%60==0) echo '		60'.PHP_EOL;
	if ($time%120==0) echo '			120'.PHP_EOL;
	if ($time%240==0) echo '				240'.PHP_EOL;
	if ($time%300==0) echo '					300'.PHP_EOL;
	if ($time%600==0) echo '						600'.PHP_EOL;
	sleep(1);
}