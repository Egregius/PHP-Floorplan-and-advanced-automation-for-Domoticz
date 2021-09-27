<?php
if ($d['zon']['s']>0) {
	if ($d['wasbak']['s']>=14) $level=$d['wasbak']['s']-8;
	else $level=8;
} else {
	if ($d['wasbak']['s']>=4) $level=$d['wasbak']['s']-4;
	else $level=0;
}
if ($level<0) $level=0;
sl('wasbak', $level, basename(__FILE__).':'.__LINE__);
