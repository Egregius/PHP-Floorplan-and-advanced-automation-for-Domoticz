<?php
if ($d['zon']['s']>0) {
	if ($d['snijplank']['s']>=13) $level=ceil($d['snijplank']['s']*1.8);
	else $level=14;
} else {
	if ($d['snijplank']['s']>=3) $level=ceil($d['snijplank']['s']*1.6);
	else $level=3;
}
if ($level>100) $level=100;
sl('snijplank', $level, basename(__FILE__).':'.__LINE__);
