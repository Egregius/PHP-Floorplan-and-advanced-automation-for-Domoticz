#!/usr/bin/php
<?php
require '/var/www/html/secure/functions.php';
lg('Starting cron17 loop...');
while (1){
	include '_cron17.php';
	sleep(10);
}

