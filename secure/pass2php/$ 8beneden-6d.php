<?php
if ($d['heating']['s']<0) {
	$level=85;
} else {
	$level=100;
}
$item='Rbureel';
if ($d[$item]['s']<$level) {
	sl($item, $level, basename(__FILE__).':'.__LINE__);
}
$item='RkeukenL';
if ($d[$item]['s']<$level) {
	sl($item, $level, basename(__FILE__).':'.__LINE__);
}
$item='RkeukenR';
if ($d[$item]['s']<$level) {
	sl($item, $level, basename(__FILE__).':'.__LINE__);
}
