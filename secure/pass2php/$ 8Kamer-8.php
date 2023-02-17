<?php
if ($d['kamer']['s']>0) {
	$new=floor($d['kamer']['s']*0.65);
	sl('kamer', $new, basename(__FILE__).':'.__LINE__);
}
if ($d['kamer']['m']>0) storemode('kamer', 0, basename(__FILE__).':'.__LINE__);
resetsecurity();
