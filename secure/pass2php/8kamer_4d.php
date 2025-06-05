<?php
if ($status=='On') {
	if ($d['kamer']['s']<100) sl('kamer', 100);
	if ($d['kamer']['m']>0) storemode('kamer', 0, basename(__FILE__).':'.__LINE__);
}