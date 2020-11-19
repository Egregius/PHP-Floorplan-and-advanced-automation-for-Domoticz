<?php
echo 'Preloading';

$directory = new RecursiveDirectoryIterator('/var/www');
$fullTree = new RecursiveIteratorIterator($directory);
$phpFiles = new RegexIterator($fullTree, '/.+((?<!Test)+\.php$)/i', RecursiveRegexIterator::GET_MATCH);

foreach ($phpFiles as $key => $file) {
	if (opcache_compile_file($key)) echo $key.' OK<br>'; else echo $key.' FAILED<br>';
}