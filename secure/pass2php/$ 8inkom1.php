<?php
if ($d['zon']['s']>0) {
	if ($d['wasbak']['s']>=32) $level=$d['wasbak']['s']+5;
	else $level=32;
} else {
	if ($d['wasbak']['s']>=27) $level=$d['wasbak']['s']+5;
	else $level=27;
}
sl('wasbak', $level, basename(__FILE__).':'.__LINE__);

