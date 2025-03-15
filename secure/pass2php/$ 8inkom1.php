<?php
if ($d['zon']>0) {
	if ($d['inkom']['s']>=32) $level=$d['inkom']['s']+5;
	else $level=32;
} else {
	if ($d['inkom']['s']>=27) $level=$d['inkom']['s']+5;
	else $level=27;
}
sl('inkom', $level, basename(__FILE__).':'.__LINE__);
