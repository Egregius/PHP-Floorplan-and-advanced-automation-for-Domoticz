<?php
ini_set('output_buffering', 'off');
ini_set('zlib.output_compression', false);
while (ob_get_level() > 0) ob_end_flush();
ob_implicit_flush(true);
header('Content-Type: text/plain');
echo str_repeat(" ", 1024);
echo "OK";
flush();
sleep(8);
require '/var/www/html/secure/functions.php';
hass('switch','turn_off','switch.mac');