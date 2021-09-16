<?php
if ($d['zon']['s']>0) {
	if ($d['wasbak']['s']>=14) $level=$d['wasbak']['s']+5;
	else $level=14;
} else {
	if ($d['wasbak']['s']>=3) $level=$d['wasbak']['s']+5;
	else $level=3;
}
sl('wasbak', $level, basename(__FILE__).':'.__LINE__);
