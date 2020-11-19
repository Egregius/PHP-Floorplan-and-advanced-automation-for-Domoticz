<?php
$files=glob('/var/www/*.php');
foreach ($files as $file) {
	if (opcache_compile_file($file)) echo $file.' OK<br>'; else echo $file.' FAILED<br>';
}
$files=glob('/var/www/html/*.php');
foreach ($files as $file) {
	if (opcache_compile_file($file)) echo $file.' OK<br>'; else echo $file.' FAILED<br>';
}
$files=glob('/var/www/html/secure/*.php');
foreach ($files as $file) {
	if (opcache_compile_file($file)) echo $file.' OK<br>'; else echo $file.' FAILED<br>';
}
$files=glob('/var/www/html/secure/pass2php/*.php');
foreach ($files as $file) {
	if (opcache_compile_file($file)) echo $file.' OK<br>'; else echo $file.' FAILED<br>';
}