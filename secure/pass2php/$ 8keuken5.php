<?php
if ($d['zon']['s']>0) {
	if ($d['wasbak']['s']>=14) $level=floor($d['wasbak']['s']*0.7);
	else $level=8;
} else {
	if ($d['wasbak']['s']>=4) $level=floor($d['wasbak']['s']*0.9);
	else $level=0;
}
if ($level<0) $level=0;
sl('wasbak', $level, basename(__FILE__).':'.__LINE__);
