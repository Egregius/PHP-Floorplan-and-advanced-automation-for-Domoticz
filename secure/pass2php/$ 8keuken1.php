<?php
if ($d['zon']['s']>0) {
	if ($d['wasbak']['s']>=14) $level=ceil($d['wasbak']['s']*1.3);
	else $level=8;
} else {
	if ($d['wasbak']['s']>=3) $level=ceil($d['wasbak']['s']*1.1);
	else $level=3;
}
if ($level>100) $level=100;
sl('wasbak', $level, basename(__FILE__).':'.__LINE__);
