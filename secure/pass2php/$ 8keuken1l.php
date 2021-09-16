<?php
if ($d['wasbak']['s']<100) {
	if ($d['wasbak']['s']==0) $d['wasbak']['s']=1;
	$new=ceil($d['wasbak']['s']*1.05);
	if ($new>100) $new=100;
	sl('wasbak', $new);
}
