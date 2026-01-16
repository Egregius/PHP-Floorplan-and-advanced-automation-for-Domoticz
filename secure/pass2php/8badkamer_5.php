<?php
if ($status=='On') {
	if ($d['heating']->s>=0) {
		store('badkamer_set', 19, basename(__FILE__).':'.__LINE__);
		if ($d['badkamer_set']->m!=2) storemode('badkamer_set', 2, basename(__FILE__).':'.__LINE__);
		if ($d['badkamervuur1']->s=='Off') sw('badkamervuur1', 'On', basename(__FILE__).':'.__LINE__);
		if ($d['badkamervuur2']->s=='Off') sw('badkamervuur2', 'On', basename(__FILE__).':'.__LINE__);
	}
}