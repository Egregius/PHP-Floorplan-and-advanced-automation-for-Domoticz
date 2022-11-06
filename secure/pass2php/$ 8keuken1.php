<?php
if ($d['zon']['s']>0) {
	if ($d['wasbak']['s']>=13) $level=ceil($d['wasbak']['s']*2);
	else $level=14;
} else {
	if ($d['wasbak']['s']>=4) $level=ceil($d['wasbak']['s']*1.8);
	else $level=5;
}
if ($level>100) $level=100;
sl('wasbak', $level, basename(__FILE__).':'.__LINE__);
