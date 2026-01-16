<?php
if ($status=='On') {
	if ($d['kamer']->s<100) {
		if ($d['kamer']->s==0) $d['kamer']->s=1;
		sl('kamer', ceil($d['kamer']->s*1.05));
	}
	if ($d['kamer']->m>0) storemode('kamer', 0, basename(__FILE__).':'.__LINE__);
}