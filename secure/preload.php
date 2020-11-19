<?php
echo 'Preloading';

$directory = new RecursiveDirectoryIterator('/var/www');
$fullTree = new RecursiveIteratorIterator($directory);
$phpFiles = new RegexIterator($fullTree, '/.+((?<!Test)+\.php$)/i', RecursiveRegexIterator::GET_MATCH);

foreach ($phpFiles as $key => $file) {
	echo $key.' ';
	print_r($file, true);
	echo '<br>';
}