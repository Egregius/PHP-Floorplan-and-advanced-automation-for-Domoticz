<?php
if ($d['zon']['s']>0) {
	if ($d['snijplank']['s']>=14) $level=ceil($d['snijplank']['s']*1.8);
	else $level=15;
} else {
	if ($d['snijplank']['s']>=11) $level=ceil($d['snijplank']['s']*1.6);
	else $level=12;
}
if ($level>100) $level=100;
sl('snijplank', $level, basename(__FILE__).':'.__LINE__);
