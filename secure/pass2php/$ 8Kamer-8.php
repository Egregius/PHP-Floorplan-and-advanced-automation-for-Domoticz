<?php
if ($d['kamer']['s']>1) {
	$new=floor($d['kamer']['s']*0.65);
	sl('kamer', $new, basename(__FILE__).':'.__LINE__,true);
} else sw('kamer', 'Off', basename(__FILE__).':'.__LINE__,true);
resetsecurity();
