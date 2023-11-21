<?php
if ($d['inkom']['s']<100) {
	if ($d['inkom']['s']==0) $d['inkom']['s']=1;
	$new=ceil($d['inkom']['s']*1.05);
	if ($new>100) $new=100;
	sl('inkom', $new);
}
mset('8inkom', time());
