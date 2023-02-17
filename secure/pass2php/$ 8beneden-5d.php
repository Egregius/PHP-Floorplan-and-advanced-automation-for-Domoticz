<?php
if ($d['heating']['s']<0) {
	$level=85;
} else {
	$level=100;
}
if ($d['Rliving']['s']<$level) {
	sl('Rliving', $level, basename(__FILE__).':'.__LINE__);
}