<?php
if ($status=='On') {
	if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
	sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
	sw('badkamervuur1', 'Off', basename(__FILE__).':'.__LINE__);
}