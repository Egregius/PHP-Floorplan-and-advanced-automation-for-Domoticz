#!/usr/bin/php
<?php
require '/var/www/html/secure/functions.php';
lg('Starting cron10B loop...');
while (1){
	include '_cron10B.php';
	sleep(10);
}

